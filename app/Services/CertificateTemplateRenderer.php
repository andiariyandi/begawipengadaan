<?php

namespace App\Services;

/**
 * CertificateTemplateRenderer — render HTML sertifikat PBJ & penggantian placeholder PDF.
 *
 * Mendukung 3 mode tata letak:
 * 1. Penggantian Placeholder langsung di dalam PDF (`pdf_placeholders` — otomatis mendeteksi
 *    koordinat {{placeholder}} di dalam file PDF dan menggantinya dengan data asli peserta
 *    di posisi yang presisi tanpa perlu koding HTML).
 * 2. Overlay gambar latar belakang (background PNG/JPG) dari Canva / Figma / Photoshop.
 * 3. Custom Body HTML / Frame Klasik.
 */
class CertificateTemplateRenderer
{
    /** @var array<string, string> */
    private array $placeholders = [];
    private DigitalSignatureService $signatureService;

    public function __construct()
    {
        $this->signatureService = new DigitalSignatureService();
    }

    /**
     * Render HTML sertifikat lengkap.
     *
     * @param array<string, mixed> $certificateData
     * @param array<string, mixed> $template
     */
    public function render(array $certificateData, array $template): string
    {
        $payload            = $this->decodePayload($template['template_payload'] ?? null);
        $this->placeholders = $this->buildPlaceholderMap($certificateData, $template, $payload);

        $bodyHtml = isset($payload['body_html']) && is_string($payload['body_html']) && trim($payload['body_html']) !== ''
            ? $payload['body_html']
            : null;

        $customCss     = isset($payload['css']) && is_string($payload['css']) ? trim($payload['css']) : '';
        $bgStyle       = $this->resolveBackgroundStyle($template);
        $hasBackground = $this->hasBackground($template);
        $isPdfBg       = $this->isPdfBackground($template);
        $layoutMode    = (string) ($payload['layout_mode'] ?? '');

        $title = htmlspecialchars((string) ($certificateData['certificate_title'] ?? $certificateData['title'] ?? 'Sertifikat'), ENT_QUOTES, 'UTF-8');

        // Resolusi placeholder koordinat PDF (dari payload atau auto-detect langsung dari file PDF)
        $pdfPlaceholders = $this->resolveActivePdfPlaceholders($template, $payload, $isPdfBg, $bodyHtml, $layoutMode);

        $pageWidthMm  = (float) ($payload['page_width_mm'] ?? 297.0);
        $pageHeightMm = (float) ($payload['page_height_mm'] ?? 210.0);
        if ($pageWidthMm <= 50) {
            $pageWidthMm = 297.0;
        }
        if ($pageHeightMm <= 50) {
            $pageHeightMm = 210.0;
        }

        $css = $this->defaultCss($customCss, $pageWidthMm, $pageHeightMm);

        if (! empty($pdfPlaceholders) && $layoutMode !== 'custom_html') {
            // Mode 1: Penggantian Placeholder Presisi pada PDF / Kanvas Visual
            $body      = $this->renderPdfPlaceholdersBody($pdfPlaceholders);
            $headerBar = '';
            $qrBlock   = '';
        } elseif ($bodyHtml !== null) {
            // Mode 2: Custom Body HTML
            $body      = $this->applyPlaceholders($bodyHtml);
            $headerBar = '';
            $qrBlock   = $this->renderQrBlock($payload, $certificateData);
        } elseif ($hasBackground) {
            // Mode 3: Default Overlay di atas gambar background
            $body      = $this->applyPlaceholders($this->overlayBody());
            $headerBar = '';
            $qrBlock   = $this->renderQrBlock($payload, $certificateData);
        } else {
            // Mode 4: Frame Standar Klasik (tanpa file background)
            $body      = $this->applyPlaceholders($this->defaultBody());
            $headerBar = '<div class="cert-header"><span class="cert-app">BegawiPengadaan</span><span class="cert-type">' . $title . '</span></div>';
            $qrBlock   = $this->renderQrBlock($payload, $certificateData);
        }

        return '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">'
            . '<title>' . $title . '</title>'
            . $css
            . $bgStyle
            . '</head><body>'
            . $headerBar
            . $body
            . $qrBlock
            . '</body></html>';
    }

    /**
     * Resolusi daftar koordinat placeholder PDF:
     * - Gunakan `payload['pdf_placeholders']` jika sudah disimpan dari form.
     * - Jika kosong tetapi template memiliki file PDF background, otomatis inspeksi file PDF
     *   untuk mendeteksi {{placeholder}} yang diketik di dalam PDF.
     *
     * @param array<string, mixed> $template
     * @param array<string, mixed> &$payload
     * @return array<int, array<string, mixed>>
     */
    private function resolveActivePdfPlaceholders(
        array $template,
        array &$payload,
        bool $isPdfBg,
        ?string $bodyHtml,
        string $layoutMode
    ): array {
        if (! empty($payload['pdf_placeholders']) && is_array($payload['pdf_placeholders'])) {
            return $payload['pdf_placeholders'];
        }

        if ($isPdfBg && ($bodyHtml === null || $layoutMode === 'pdf_placeholders')) {
            $pdfPath = $this->getBackgroundPhysicalPath($template);
            if ($pdfPath && is_file($pdfPath)) {
                $service    = new CertificatePdfPlaceholderService();
                $inspection = $service->inspectPdf($pdfPath);
                if (! empty($inspection['page_width_mm'])) {
                    $payload['page_width_mm']  = $inspection['page_width_mm'];
                    $payload['page_height_mm'] = $inspection['page_height_mm'];
                }
                if (! empty($inspection['placeholders'])) {
                    return $inspection['placeholders'];
                }
            }
        }

        return [];
    }

    /**
     * Render lapisan elemen placeholder pada koordinat persisnya di atas halaman PDF/kanvas.
     *
     * @param array<int, array<string, mixed>> $pdfPlaceholders
     */
    public function renderPdfPlaceholdersBody(array $pdfPlaceholders): string
    {
        $html  = '<div class="cert-pdf-placeholder-canvas">';
        $index = 0;

        foreach ($pdfPlaceholders as $item) {
            if (! is_array($item)) {
                continue;
            }
            $index++;
            $tag = trim((string) ($item['tag'] ?? ''));
            if ($tag === '') {
                continue;
            }
            if (! str_starts_with($tag, '{{') && $tag !== '__QR_BLOCK__') {
                $tag = '{{' . trim($tag, '{} ') . '}}';
            }

            $type       = (string) ($item['type'] ?? ((in_array($tag, ['{{qr_code}}', '{{qr_block}}', '__QR_BLOCK__'], true)) ? 'qr' : 'text'));
            $xPct       = max(0.0, min(100.0, (float) ($item['x_pct'] ?? 50.0)));
            $yPct       = max(0.0, min(100.0, (float) ($item['y_pct'] ?? 50.0)));
            $fontSize   = max(6, min(96, (int) ($item['font_size'] ?? 16)));
            $fontWeight = (($item['font_weight'] ?? 'normal') === 'bold') ? 'bold' : 'normal';
            $color      = preg_match('/^#[0-9a-fA-F]{3,6}$/', (string) ($item['color'] ?? '')) ? (string) $item['color'] : '#0f172a';
            $align      = in_array($item['align'] ?? 'center', ['left', 'center', 'right'], true) ? (string) $item['align'] : 'center';
            $maskBg     = ! empty($item['mask_bg']);
            $maskColor  = preg_match('/^#[0-9a-fA-F]{3,6}$/', (string) ($item['mask_color'] ?? '')) ? (string) $item['mask_color'] : '#ffffff';
            $qrSize     = max(40, min(320, (int) ($item['qr_size'] ?? 110)));

            // Jika opsi masking diaktifkan (menutup teks placeholder asli pada PDF raster/kompleks)
            if ($maskBg) {
                $origX = max(0.0, min(100.0, (float) ($item['orig_x_pct'] ?? max(0.0, $xPct - 12.0))));
                $origY = max(0.0, min(100.0, (float) ($item['orig_y_pct'] ?? $yPct)));
                $origW = max(2.0, min(100.0, (float) ($item['orig_w_pct'] ?? 24.0)));
                $origH = max(1.5, min(50.0, (float) ($item['orig_h_pct'] ?? 4.5)));

                $html .= sprintf(
                    '<div class="cert-ph-mask" style="position:absolute;left:%.2F%%;top:%.2F%%;width:%.2F%%;height:%.2F%%;background-color:%s;z-index:1;"></div>',
                    $origX,
                    $origY,
                    $origW,
                    $origH,
                    htmlspecialchars($maskColor, ENT_QUOTES, 'UTF-8')
                );
            }

            if ($type === 'qr') {
                // Untuk QR Code, pusatkan atau posisikan sesuai align
                $qrLeftStyle = match ($align) {
                    'center' => sprintf('left:%.2F%%;margin-left:-%dpx;', $xPct, (int) round($qrSize / 2)),
                    'right'  => sprintf('left:%.2F%%;margin-left:-%dpx;', $xPct, $qrSize),
                    default  => sprintf('left:%.2F%%;', $xPct),
                };

                $html .= sprintf(
                    '<style>.cert-ph-qr-%d img { width:%dpx !important; height:%dpx !important; }</style>'
                        . '<div class="cert-ph-qr cert-ph-qr-%d" style="position:absolute;%stop:%.2F%%;width:%dpx;height:%dpx;z-index:3;">__QR_BLOCK__</div>',
                    $index,
                    $qrSize,
                    $qrSize,
                    $index,
                    $qrLeftStyle,
                    $yPct,
                    $qrSize,
                    $qrSize
                );
                continue;
            }

            // Ganti tag placeholder dengan nilai peserta sesungguhnya
            $resolvedValue = $this->applyPlaceholders($tag);

            // Khusus TTE & Segel Digital Resmi (Blok HTML tanda tangan naskah dinas)
            if (in_array($tag, ['{{tanda_tangan}}', '{{stempel_tte}}', '{{segel_digital}}'], true)) {
                $blockWidth  = ($tag === '{{tanda_tangan}}') ? 260 : 210;
                $alignMargin = match ($align) {
                    'center' => sprintf('left:%.2F%%;margin-left:-%dpx;', $xPct, (int) round($blockWidth / 2)),
                    'right'  => sprintf('left:%.2F%%;margin-left:-%dpx;', $xPct, $blockWidth),
                    default  => sprintf('left:%.2F%%;', $xPct),
                };
                $html .= sprintf(
                    '<div class="cert-ph-signature" style="position:absolute;%stop:%.2F%%;width:%dpx;z-index:3;">%s</div>',
                    $alignMargin,
                    $yPct,
                    $blockWidth,
                    $resolvedValue
                );
                continue;
            }

            // Hitung kotak perataan presisi yang 100% kompatibel dengan Dompdf maupun browser
            if ($align === 'center') {
                if ($xPct >= 40.0 && $xPct <= 60.0) {
                    $posCss = 'left:0;width:100%;text-align:center;';
                } else {
                    $halfSpan = min(max($xPct, 15.0), max(100.0 - $xPct, 15.0));
                    $boxLeft  = max(0.0, $xPct - $halfSpan);
                    $boxWidth = $halfSpan * 2.0;
                    $posCss   = sprintf('left:%.2F%%;width:%.2F%%;text-align:center;', $boxLeft, $boxWidth);
                }
            } elseif ($align === 'right') {
                $boxWidth = max(5.0, $xPct);
                $posCss   = sprintf('left:0;width:%.2F%%;text-align:right;', $boxWidth);
            } else {
                $boxWidth = max(5.0, 100.0 - $xPct);
                $posCss   = sprintf('left:%.2F%%;width:%.2F%%;text-align:left;', $xPct, $boxWidth);
            }

            $html .= sprintf(
                '<div class="cert-ph-text" style="position:absolute;%stop:%.2F%%;font-size:%dpt;font-weight:%s;color:%s;line-height:1.2;white-space:nowrap;z-index:2;">%s</div>',
                $posCss,
                $yPct,
                $fontSize,
                $fontWeight,
                htmlspecialchars($color, ENT_QUOTES, 'UTF-8'),
                $resolvedValue
            );
        }

        $html .= '</div>';
        return $html;
    }

    // ===============================================================
    // Placeholders Builder & Replacement
    // ===============================================================

    /**
     * @param array<string, mixed> $data
     * @param array<string, mixed> $template
     * @param array<string, mixed> $payload
     * @return array<string, string>
     */
    public function buildPlaceholderMap(array $data, array $template, array $payload = []): array
    {
        $certNumber = (string) ($data['certificate_number'] ?? $data['nomor_sertifikat'] ?? '—');
        $name       = (string) ($data['participant_name'] ?? $data['user_name'] ?? 'Peserta Terdaftar');

        // Auto-detect Identitas (NIP, NI-PPK, NIB, NIDN, NIK)
        $idNumber = (string) ($data['identity_number'] ?? $data['nip'] ?? '—');
        if (trim($idNumber) === '') {
            $idNumber = '—';
        }
        $idLabel = (string) ($data['identity_label'] ?? 'NIP');
        if (trim($idLabel) === '') {
            $idLabel = 'NIP';
        }

        // Auto-fallback cerdas: jika template lama memakai {{nip}} dan peserta adalah non-ASN,
        // nilai {{nip}} otomatis terisi nomor identitas peserta yang bersangkutan (NIB, NIK, atau NIDN)
        $nip = (string) ($data['nip'] ?? $idNumber);
        if (trim($nip) === '' || $nip === '—') {
            $nip = $idNumber;
        }

        $nik   = (string) ($data['nik'] ?? ($idLabel === 'NIK' ? $idNumber : '—'));
        $nib   = (string) ($data['nib'] ?? ($idLabel === 'NIB' ? $idNumber : '—'));
        $nidn  = (string) ($data['nidn'] ?? (in_array($idLabel, ['NIDN', 'NIM'], true) ? $idNumber : '—'));
        $niPpk = (string) ($data['ni_ppk'] ?? ($idLabel === 'NI-PPK' ? $idNumber : '—'));

        // Auto-detect Instansi & Organisasi (Instansi Pemerintah, Perusahaan/Badan Usaha, Perguruan Tinggi, Organisasi Umum)
        $instansiLabel = (string) ($data['institution_label'] ?? 'Instansi');
        $instansi      = (string) ($data['institution_name'] ?? $data['instansi'] ?? 'Pemerintah Provinsi Lampung');
        $unit          = (string) ($data['unit_name'] ?? $data['unit_kerja'] ?? $instansi);
        $jabatan       = (string) ($data['position_name'] ?? $data['jabatan'] ?? 'Peserta');
        $businessName  = (string) ($data['business_name'] ?? ($instansiLabel === 'Perusahaan / Badan Usaha' ? $instansi : '—'));
        $campusName    = (string) ($data['campus_name'] ?? ($instansiLabel === 'Perguruan Tinggi' ? $instansi : '—'));
        $pangkat       = (string) ($data['rank_grade'] ?? $data['pangkat_golongan'] ?? '—');
        $tipePeserta   = (string) ($data['tipe_peserta'] ?? 'Peserta Pengadaan');
        $emailPeserta  = (string) ($data['user_email'] ?? '—');
        $phonePeserta  = (string) ($data['user_phone'] ?? '—');

        // Detail Kegiatan
        $title          = (string) ($data['certificate_title'] ?? $data['title'] ?? 'Sertifikat Pengadaan');
        $subtitle       = (string) ($data['subtitle'] ?? $data['class_name'] ?? '');
        $actType        = (string) ($data['activity_type'] ?? $data['tipe_kegiatan'] ?? 'Arena Belajar PBJ');
        $kodeTipe       = (string) ($data['kode_tipe'] ?? 'AB');
        $idKegiatan     = (string) ($data['id_kegiatan'] ?? $data['class_id'] ?? $data['event_id'] ?? '1');
        $jp             = (string) ($data['jam_pelajaran'] ?? $data['jp'] ?? '—');
        $durasi         = (string) (! empty($data['duration_minutes']) ? ($data['duration_minutes'] . ' Menit') : $jp);
        $score          = (string) ($data['final_score'] ?? '—');
        $predikat       = (string) ($data['predicate'] ?? $data['predikat'] ?? 'Lulus');
        $lokasiKegiatan = (string) ($data['lokasi_kegiatan'] ?? $data['location'] ?? 'Provinsi Lampung');
        $metodeKegiatan = self::standardizeMethod((string) ($data['metode_kegiatan'] ?? $data['learning_method'] ?? $data['event_mode'] ?? 'Daring'));
        $tingkatKursus  = (string) ($data['tingkat_kursus'] ?? $data['course_level'] ?? $data['level'] ?? 'Tingkat Dasar (Level 1)');
        $kodeKelas      = (string) ($data['kode_kelas'] ?? $data['class_code'] ?? '—');
        $namaProgram    = (string) ($data['nama_program'] ?? $data['program_title'] ?? $title);

        // Tanggal (Format Bahasa Indonesia)
        $rawIssued     = $data['issued_at'] ?? 'now';
        $issuedDate    = $this->formatTanggalIndo((string) $rawIssued);
        $hariTglTerbit = $this->formatHariTanggalIndo((string) $rawIssued);
        $bulanTahun    = $this->formatBulanTahunIndo((string) $rawIssued);
        $year          = date('Y', strtotime((string) $rawIssued) ?: time());
        $city          = (string) ($payload['issuance_city'] ?? $data['city'] ?? 'Bandar Lampung');
        $expiresAt     = ! empty($data['expires_at']) ? $this->formatTanggalIndo((string) $data['expires_at']) : 'Berlaku Selamanya';

        $startDate   = ! empty($data['event_start_at'])
            ? $this->formatTanggalIndo((string) $data['event_start_at'])
            : (! empty($data['start_date']) ? (string) $data['start_date'] : $issuedDate);
        $endDate     = ! empty($data['event_end_at'])
            ? $this->formatTanggalIndo((string) $data['event_end_at'])
            : (! empty($data['end_date']) ? (string) $data['end_date'] : $issuedDate);
        $periodDate  = ($startDate === $endDate) ? $startDate : ($startDate . ' s.d. ' . $endDate);

        // Penerbit & Pejabat
        $issuerName  = (string) ($payload['issuer_name'] ?? $data['issuer_name'] ?? 'Pemerintah Provinsi Lampung — Biro Pengadaan Barang dan Jasa');
        $signerName  = (string) ($payload['signer_name'] ?? 'Ir. S. Hendriyanto, S.T., M.T.');
        $signerPos   = (string) ($payload['signer_position'] ?? 'Kepala Biro Pengadaan Barang dan Jasa');
        $signerNip   = (string) ($payload['signer_nip'] ?? '19730915 199902 1 001');
        $signerRank  = (string) ($payload['signer_rank'] ?? $data['signer_rank'] ?? 'Pembina Utama Muda / IV.c');

        $verifyUrl   = (string) ($data['verification_url'] ?? '');

        $map = [
            // 1. Nomor Sertifikat & Identitas Terbit
            '{{nomor_sertifikat}}'      => htmlspecialchars($certNumber, ENT_QUOTES, 'UTF-8'),
            '{{certificate_number}}'    => htmlspecialchars($certNumber, ENT_QUOTES, 'UTF-8'),
            '{{tanggal_terbit}}'        => $issuedDate,
            '{{issued_date}}'           => $issuedDate,
            '{{hari_tanggal_terbit}}'   => $hariTglTerbit,
            '{{bulan_tahun}}'           => $bulanTahun,
            '{{tahun}}'                 => $year,
            '{{year}}'                  => $year,
            '{{kota_terbit}}'           => htmlspecialchars($city, ENT_QUOTES, 'UTF-8'),
            '{{tempat_terbit}}'         => htmlspecialchars($city, ENT_QUOTES, 'UTF-8'),
            '{{masa_berlaku}}'          => htmlspecialchars($expiresAt, ENT_QUOTES, 'UTF-8'),
            '{{tanggal_kedaluwarsa}}'   => htmlspecialchars($expiresAt, ENT_QUOTES, 'UTF-8'),

            // 2. Data Peserta (Auto-Detect Multi-Profil: ASN, PPPK, Penyedia, Kampus, Umum)
            '{{nama_peserta}}'          => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            '{{nama_lengkap}}'          => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            '{{participant_name}}'      => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),

            // Auto-detect Identitas
            '{{nomor_identitas}}'       => htmlspecialchars($idNumber, ENT_QUOTES, 'UTF-8'),
            '{{identitas_peserta}}'     => htmlspecialchars($idNumber, ENT_QUOTES, 'UTF-8'),
            '{{label_identitas}}'       => htmlspecialchars($idLabel, ENT_QUOTES, 'UTF-8'),
            '{{nip}}'                   => htmlspecialchars($nip, ENT_QUOTES, 'UTF-8'),
            '{{nip_peserta}}'           => htmlspecialchars($nip, ENT_QUOTES, 'UTF-8'),
            '{{identity_number}}'       => htmlspecialchars($idNumber, ENT_QUOTES, 'UTF-8'),
            '{{ni_ppk}}'                => htmlspecialchars($niPpk, ENT_QUOTES, 'UTF-8'),
            '{{nik}}'                   => htmlspecialchars($nik, ENT_QUOTES, 'UTF-8'),
            '{{nib}}'                   => htmlspecialchars($nib, ENT_QUOTES, 'UTF-8'),
            '{{nidn}}'                  => htmlspecialchars($nidn, ENT_QUOTES, 'UTF-8'),

            // Auto-detect Instansi & Organisasi
            '{{instansi}}'              => htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8'),
            '{{instansi_peserta}}'      => htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8'),
            '{{institution_name}}'      => htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8'),
            '{{organisasi}}'            => htmlspecialchars($instansi, ENT_QUOTES, 'UTF-8'),
            '{{label_instansi}}'        => htmlspecialchars($instansiLabel, ENT_QUOTES, 'UTF-8'),
            '{{nama_usaha}}'            => htmlspecialchars($businessName, ENT_QUOTES, 'UTF-8'),
            '{{perusahaan}}'            => htmlspecialchars($businessName, ENT_QUOTES, 'UTF-8'),
            '{{badan_usaha}}'           => htmlspecialchars($businessName, ENT_QUOTES, 'UTF-8'),
            '{{perguruan_tinggi}}'      => htmlspecialchars($campusName, ENT_QUOTES, 'UTF-8'),
            '{{nama_kampus}}'           => htmlspecialchars($campusName, ENT_QUOTES, 'UTF-8'),

            '{{unit_kerja}}'            => htmlspecialchars($unit, ENT_QUOTES, 'UTF-8'),
            '{{jabatan}}'               => htmlspecialchars($jabatan, ENT_QUOTES, 'UTF-8'),
            '{{position_name}}'         => htmlspecialchars($jabatan, ENT_QUOTES, 'UTF-8'),
            '{{pangkat_golongan}}'      => htmlspecialchars($pangkat, ENT_QUOTES, 'UTF-8'),
            '{{pangkat}}'               => htmlspecialchars($pangkat, ENT_QUOTES, 'UTF-8'),
            '{{golongan}}'              => htmlspecialchars($pangkat, ENT_QUOTES, 'UTF-8'),
            '{{tipe_peserta}}'          => htmlspecialchars($tipePeserta, ENT_QUOTES, 'UTF-8'),
            '{{kategori_peserta}}'      => htmlspecialchars($tipePeserta, ENT_QUOTES, 'UTF-8'),
            '{{jenis_profil}}'          => htmlspecialchars($tipePeserta, ENT_QUOTES, 'UTF-8'),
            '{{email_peserta}}'         => htmlspecialchars($emailPeserta, ENT_QUOTES, 'UTF-8'),
            '{{telepon_peserta}}'       => htmlspecialchars($phonePeserta, ENT_QUOTES, 'UTF-8'),

            // 3. Kegiatan
            '{{judul_kegiatan}}'        => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            '{{nama_kegiatan}}'         => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            '{{certificate_title}}'     => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            '{{source_label}}'          => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            '{{nama_kursus}}'           => htmlspecialchars($title, ENT_QUOTES, 'UTF-8'),
            '{{nama_kelas}}'            => htmlspecialchars($subtitle ?: $title, ENT_QUOTES, 'UTF-8'),
            '{{kode_kelas}}'            => htmlspecialchars($kodeKelas, ENT_QUOTES, 'UTF-8'),
            '{{nama_program}}'          => htmlspecialchars($namaProgram, ENT_QUOTES, 'UTF-8'),
            '{{tingkat_kursus}}'        => htmlspecialchars($tingkatKursus, ENT_QUOTES, 'UTF-8'),
            '{{level_kompetensi}}'      => htmlspecialchars($tingkatKursus, ENT_QUOTES, 'UTF-8'),
            '{{tipe_kegiatan}}'         => htmlspecialchars($actType, ENT_QUOTES, 'UTF-8'),
            '{{activity_type}}'         => htmlspecialchars($actType, ENT_QUOTES, 'UTF-8'),
            '{{kode_tipe}}'             => htmlspecialchars($kodeTipe, ENT_QUOTES, 'UTF-8'),
            '{{id_kegiatan}}'           => htmlspecialchars($idKegiatan, ENT_QUOTES, 'UTF-8'),
            '{{lokasi_kegiatan}}'       => htmlspecialchars($lokasiKegiatan, ENT_QUOTES, 'UTF-8'),
            '{{tempat_kegiatan}}'       => htmlspecialchars($lokasiKegiatan, ENT_QUOTES, 'UTF-8'),
            '{{metode_kegiatan}}'       => htmlspecialchars($metodeKegiatan, ENT_QUOTES, 'UTF-8'),
            '{{moda_kegiatan}}'         => htmlspecialchars($metodeKegiatan, ENT_QUOTES, 'UTF-8'),

            // 4. Jam Pelajaran & Nilai
            '{{jam_pelajaran}}'         => htmlspecialchars($jp, ENT_QUOTES, 'UTF-8'),
            '{{jp}}'                    => htmlspecialchars($jp, ENT_QUOTES, 'UTF-8'),
            '{{durasi}}'                => htmlspecialchars($durasi, ENT_QUOTES, 'UTF-8'),
            '{{nilai_akhir}}'           => htmlspecialchars($score, ENT_QUOTES, 'UTF-8'),
            '{{final_score}}'           => htmlspecialchars($score, ENT_QUOTES, 'UTF-8'),
            '{{predikat}}'              => htmlspecialchars($predikat, ENT_QUOTES, 'UTF-8'),
            '{{predicate}}'             => htmlspecialchars($predikat, ENT_QUOTES, 'UTF-8'),

            // 5. Tanggal
            '{{tanggal_mulai}}'         => $startDate,
            '{{start_date}}'            => $startDate,
            '{{tanggal_selesai}}'       => $endDate,
            '{{end_date}}'              => $endDate,
            '{{periode_kegiatan}}'      => $periodDate,

            // 6. Penerbit & Penandatangan
            '{{penerbit}}'              => htmlspecialchars($issuerName, ENT_QUOTES, 'UTF-8'),
            '{{issuer_name}}'           => htmlspecialchars($issuerName, ENT_QUOTES, 'UTF-8'),
            '{{penandatangan_nama}}'    => htmlspecialchars($signerName, ENT_QUOTES, 'UTF-8'),
            '{{signer_name}}'           => htmlspecialchars($signerName, ENT_QUOTES, 'UTF-8'),
            '{{penandatangan_jabatan}}' => htmlspecialchars($signerPos, ENT_QUOTES, 'UTF-8'),
            '{{signer_position}}'       => htmlspecialchars($signerPos, ENT_QUOTES, 'UTF-8'),
            '{{penandatangan_nip}}'     => htmlspecialchars($signerNip, ENT_QUOTES, 'UTF-8'),
            '{{signer_nip}}'            => htmlspecialchars($signerNip, ENT_QUOTES, 'UTF-8'),
            '{{penandatangan_pangkat}}' => htmlspecialchars($signerRank, ENT_QUOTES, 'UTF-8'),

            // 7. QR & Verifikasi
            '{{url_verifikasi}}'        => htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8'),
            '{{verification_url}}'      => htmlspecialchars($verifyUrl, ENT_QUOTES, 'UTF-8'),
            '{{qr_code}}'               => '<!--CERTIFICATE_QR_PLACEHOLDER-->',
            '{{qr_block}}'              => '<!--CERTIFICATE_QR_PLACEHOLDER-->',
        ];

        // 8. Tanda Tangan Elektronik (TTE) & Segel Kriptografis
        $tteMap = $this->signatureService->buildSignaturePlaceholders($data, $payload);

        return array_merge($map, $tteMap);
    }

    private function applyPlaceholders(string $html): string
    {
        return strtr($html, $this->placeholders);
    }

    private function formatTanggalIndo(string $dateStr): string
    {
        $ts = strtotime($dateStr);
        if ($ts === false) {
            $ts = time();
        }
        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $m = (int) date('n', $ts);
        return date('d', $ts) . ' ' . ($bulan[$m] ?? date('F', $ts)) . ' ' . date('Y', $ts);
    }

    private function formatHariTanggalIndo(string $dateStr): string
    {
        $ts = strtotime($dateStr);
        if ($ts === false) {
            $ts = time();
        }
        $hari = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
        $h = (int) date('w', $ts);
        return ($hari[$h] ?? 'Senin') . ', ' . $this->formatTanggalIndo($dateStr);
    }

    private function formatBulanTahunIndo(string $dateStr): string
    {
        $ts = strtotime($dateStr);
        if ($ts === false) {
            $ts = time();
        }
        $bulan = [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $m = (int) date('n', $ts);
        return ($bulan[$m] ?? date('F', $ts)) . ' ' . date('Y', $ts);
    }

    /**
     * Standardize learning / event delivery method into strictly: 'Daring', 'Luring', or 'Hybrid'.
     */
    public static function standardizeMethod(?string $raw): string
    {
        $clean = strtolower(trim((string) $raw));
        if ($clean === '') {
            return 'Daring';
        }

        if (
            str_contains($clean, 'hybrid')
            || str_contains($clean, 'blended')
            || str_contains($clean, 'campuran')
        ) {
            return 'Hybrid';
        }

        if (
            str_contains($clean, 'luring')
            || str_contains($clean, 'offline')
            || str_contains($clean, 'tatap_muka')
            || str_contains($clean, 'tatap muka')
            || str_contains($clean, 'klasikal')
        ) {
            return 'Luring';
        }

        return 'Daring';
    }

    // ===============================================================
    // Daftar Placeholder & Panduan Desain
    // ===============================================================

    /**
     * Mengembalikan daftar semua placeholder resmi yang dapat dipakai di PDF maupun form.
     *
     * @return array<string, array<int, array{tag: string, label: string, example: string}>>
     */
    public static function getAvailablePlaceholders(): array
    {
        return [
            'Nomor & Identitas Sertifikat' => [
                ['tag' => '{{nomor_sertifikat}}', 'label' => 'Nomor Sertifikat Resmi (Format: SERT-{num}/{tipe}-{id}/05/{thn})', 'example' => 'SERT-00035/AB-5/05/2026'],
                ['tag' => '{{tanggal_terbit}}', 'label' => 'Tanggal Penerbitan Resmi', 'example' => date('d F Y')],
                ['tag' => '{{hari_tanggal_terbit}}', 'label' => 'Hari & Tanggal Penerbitan Lengkap', 'example' => 'Senin, ' . date('d F Y')],
                ['tag' => '{{bulan_tahun}}', 'label' => 'Bulan & Tahun Penerbitan', 'example' => date('F Y')],
                ['tag' => '{{tahun}}', 'label' => 'Tahun Penerbitan (4 Digit)', 'example' => date('Y')],
                ['tag' => '{{kota_terbit}}', 'label' => 'Kota Tempat Penerbitan Sertifikat', 'example' => 'Bandar Lampung'],
                ['tag' => '{{masa_berlaku}}', 'label' => 'Masa Berlaku / Kedaluwarsa Sertifikat', 'example' => 'Berlaku 3 Tahun'],
            ],
            'Data Peserta (Auto-Detect Multi-Profil)' => [
                ['tag' => '{{nama_peserta}}', 'label' => 'Nama Lengkap Peserta (Beserta Gelar)', 'example' => 'Ir. Rizky Yulitanto, S.T., M.M.'],
                ['tag' => '{{nomor_identitas}}', 'label' => 'Nomor Identitas Auto-Detect (NIP / NI-PPK / NIB / NIDN / NIK)', 'example' => '20000315 202321 1 002'],
                ['tag' => '{{label_identitas}}', 'label' => 'Label Jenis Identitas Auto-Detect (NIP / NI-PPK / NIB / NIDN / NIK)', 'example' => 'NIP'],
                ['tag' => '{{nip}}', 'label' => 'NIP / Nomor Identitas Pegawai (Auto-fallback ke NIB/NIDN/NIK bila non-ASN)', 'example' => '20000315 202321 1 002'],
                ['tag' => '{{ni_ppk}}', 'label' => 'Nomor Induk PPPK (Khusus Pegawai PPPK)', 'example' => '19900315 202321 1 002'],
                ['tag' => '{{nib}}', 'label' => 'NIB / Nomor Induk Berusaha (Khusus Pelaku Usaha / Penyedia)', 'example' => '0220101234567'],
                ['tag' => '{{nidn}}', 'label' => 'NIDN / NIM (Khusus Civitas Akademika Perguruan Tinggi)', 'example' => '0012058001'],
                ['tag' => '{{nik}}', 'label' => 'NIK Kependudukan KTP (Khusus Masyarakat Umum / Praktisi)', 'example' => '1871010101850001'],
                ['tag' => '{{instansi}}', 'label' => 'Instansi / Nama Perusahaan / Nama Kampus (Auto-Detect)', 'example' => 'Biro Pengadaan Barang dan Jasa Prov. Lampung'],
                ['tag' => '{{label_instansi}}', 'label' => 'Label Jenis Instansi (Instansi / Perusahaan / Perguruan Tinggi)', 'example' => 'Instansi'],
                ['tag' => '{{nama_usaha}}', 'label' => 'Nama Perusahaan / Badan Usaha (Khusus Pelaku Usaha / Rekanan PBJ)', 'example' => 'PT Mahakarya Konstruksi Lampung'],
                ['tag' => '{{perguruan_tinggi}}', 'label' => 'Nama Perguruan Tinggi / Universitas (Khusus Akademisi)', 'example' => 'Universitas Lampung'],
                ['tag' => '{{unit_kerja}}', 'label' => 'Unit Kerja / OPD / Bidang Usaha / Fakultas', 'example' => 'Bagian Pengelolaan Pengadaan'],
                ['tag' => '{{jabatan}}', 'label' => 'Jabatan / Profesi / Peran Peserta', 'example' => 'Pengelola Pengadaan Ahli Muda'],
                ['tag' => '{{pangkat_golongan}}', 'label' => 'Pangkat dan Golongan Ruang Pegawai (Khusus ASN)', 'example' => 'Pembina / IV.a'],
                ['tag' => '{{tipe_peserta}}', 'label' => 'Kategori Peserta (ASN PNS / PPPK / Penyedia / Akademisi / Umum)', 'example' => 'Aparatur Sipil Negara (PNS)'],
                ['tag' => '{{email_peserta}}', 'label' => 'Alamat Email Terdaftar Peserta', 'example' => 'ahmad.fauzi@lampungprov.go.id'],
            ],
            'Kegiatan Pembelajaran & Event' => [
                ['tag' => '{{judul_kegiatan}}', 'label' => 'Judul Kursus / Kelas PBJ / Tema Event', 'example' => 'Pengantar Pengadaan Barang/Jasa Pemerintah (Level 1)'],
                ['tag' => '{{nama_kelas}}', 'label' => 'Nama Kelas / Batch Pembelajaran', 'example' => 'Batch I Tahun Anggaran ' . date('Y')],
                ['tag' => '{{nama_program}}', 'label' => 'Nama Program Pembelajaran PBJ', 'example' => 'Program Pengembangan Kompetensi PBJ'],
                ['tag' => '{{kode_kelas}}', 'label' => 'Kode Rombongan Belajar / Kelas', 'example' => 'KLS-PBJ-2026-001'],
                ['tag' => '{{tingkat_kursus}}', 'label' => 'Tingkat Kompetensi Kursus (Level 1 / Dasar / Lanjutan)', 'example' => 'Tingkat Dasar (Level 1)'],
                ['tag' => '{{tipe_kegiatan}}', 'label' => 'Tipe Kegiatan (Arena Belajar, Webinar, Bimtek, Workshop, dll)', 'example' => 'Arena Belajar'],
                ['tag' => '{{kode_tipe}}', 'label' => 'Kode Singkatan Tipe (AB, EV, WB, BT, WS, SO, SM, PL, RK, KL)', 'example' => 'AB'],
                ['tag' => '{{id_kegiatan}}', 'label' => 'ID Numerik Kelas / Event', 'example' => '5'],
                ['tag' => '{{jam_pelajaran}}', 'label' => 'Beban Jam Pelajaran (1 JP = 60 menit)', 'example' => '24 JP'],
                ['tag' => '{{durasi}}', 'label' => 'Total Durasi Waktu Kegiatan', 'example' => '1440 Menit'],
                ['tag' => '{{nilai_akhir}}', 'label' => 'Nilai Akhir Evaluasi / Post-Test', 'example' => '94.5'],
                ['tag' => '{{predikat}}', 'label' => 'Predikat Kelulusan (Sangat Baik / Baik / Cukup / Lulus)', 'example' => 'Sangat Baik'],
                ['tag' => '{{periode_kegiatan}}', 'label' => 'Rentang Waktu Pelaksanaan Kegiatan', 'example' => '10 Oktober 2026 s.d. 20 Oktober 2026'],
                ['tag' => '{{tanggal_mulai}}', 'label' => 'Tanggal Mulai Kegiatan', 'example' => '10 Oktober 2026'],
                ['tag' => '{{tanggal_selesai}}', 'label' => 'Tanggal Selesai Kegiatan', 'example' => '20 Oktober 2026'],
                ['tag' => '{{lokasi_kegiatan}}', 'label' => 'Tempat / Lokasi Penyelenggaraan Kegiatan', 'example' => 'Hotel Novotel Bandar Lampung'],
                ['tag' => '{{metode_kegiatan}}', 'label' => 'Metode Pelaksanaan Baku (Daring / Luring / Hybrid)', 'example' => 'Daring'],
                ['tag' => '{{moda_kegiatan}}',   'label' => 'Alias Metode Pelaksanaan Baku (Daring / Luring / Hybrid)', 'example' => 'Daring'],
            ],
            'Pengesahan & Verifikasi Digital' => [
                ['tag' => '{{penerbit}}', 'label' => 'Instansi Resmi Penerbit', 'example' => 'Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung'],
                ['tag' => '{{penandatangan_nama}}', 'label' => 'Nama Pejabat Penanda Tangan', 'example' => 'Ir. S. Hendriyanto, S.T., M.T.'],
                ['tag' => '{{penandatangan_jabatan}}', 'label' => 'Jabatan Pejabat Penanda Tangan', 'example' => 'Kepala Biro Pengadaan Barang dan Jasa'],
                ['tag' => '{{penandatangan_nip}}', 'label' => 'NIP Pejabat Penanda Tangan', 'example' => '19730915 199902 1 001'],
                ['tag' => '{{penandatangan_pangkat}}', 'label' => 'Pangkat / Golongan Pejabat Penanda Tangan', 'example' => 'Pembina Utama Muda / IV.c'],
                ['tag' => '{{tanda_tangan}}', 'label' => 'Stempel Blok Tanda Tangan Elektronik (TTE) Resmi Pejabat', 'example' => '[Blok TTE Resmi Pejabat]'],
                ['tag' => '{{stempel_tte}}', 'label' => 'Stempel Sertifikat Elektronik (Terotentikasi)', 'example' => '[Sertifikat Elektronik]'],
                ['tag' => '{{segel_digital}}', 'label' => 'Alias Stempel Sertifikat Elektronik', 'example' => '[Sertifikat Elektronik]'],
                ['tag' => '{{tanggal_tte}}', 'label' => 'Waktu & Tanggal Pembubuhan TTE Lengkap (WIB)', 'example' => date('d F Y') . ' pukul 10:45 WIB'],
                ['tag' => '{{integritas_dokumen}}', 'label' => 'Kode Hash Integritas Dokumen (SHA-256 Checksum)', 'example' => 'SHA256: 8F4A12BC90E3'],
                ['tag' => '{{status_tte}}', 'label' => 'Status Keabsahan TTE Dokumen', 'example' => 'Terverifikasi & Sah Secara Elektronik'],
                ['tag' => '{{qr_code}}', 'label' => 'Gambar QR Code Keabsahan Digital', 'example' => '[QR Code Verifikasi]'],
                ['tag' => '{{url_verifikasi}}', 'label' => 'Tautan Verifikasi Keabsahan Publik', 'example' => 'https://begawipengadaan.lampungprov.go.id/verifikasi-sertifikat/...'],
            ],
        ];
    }

    // ===============================================================
    // Default & Overlay Layouts
    // ===============================================================

    /**
     * Layout Overlay Khusus Desain Background Gambar (Canva / Figma).
     */
    public function overlayBody(): string
    {
        return <<<HTML
<div class="cert-overlay-container">
  <div class="cert-overlay-top">
    <div class="cert-overlay-no">Nomor: {{nomor_sertifikat}}</div>
  </div>

  <div class="cert-overlay-main">
    <div class="cert-overlay-awarded">DIBERIKAN KEPADA:</div>
    <div class="cert-overlay-name">{{nama_peserta}}</div>
    <div class="cert-overlay-identity">
      <span>NIP: {{nip}}</span> &bull; <span>{{instansi}}</span>
    </div>

    <div class="cert-overlay-desc">
      Atas partisipasi dan kelulusannya pada program pembelajaran kompetensi pengadaan:
    </div>
    <div class="cert-overlay-title">{{judul_kegiatan}}</div>
    <div class="cert-overlay-meta">
      Beban Belajar: <strong>{{jam_pelajaran}}</strong> &bull; Predikat: <strong>{{predikat}}</strong> &bull; Periode: {{periode_kegiatan}}
    </div>
  </div>

  <div class="cert-overlay-footer">
    <table class="cert-overlay-footer-table">
      <tr>
        <td class="cert-overlay-signer">
          <div class="cert-signer-date">Bandar Lampung, {{tanggal_terbit}}</div>
          <div class="cert-signer-role">{{penandatangan_jabatan}}</div>
          <div class="cert-signer-space"></div>
          <div class="cert-signer-name"><strong>{{penandatangan_nama}}</strong></div>
          <div class="cert-signer-nip">NIP. {{penandatangan_nip}}</div>
        </td>
        <td class="cert-overlay-qr">
          <div class="cert-qr-box">
            __QR_BLOCK__
            <div class="cert-qr-caption">Scan QR Verifikasi Keabsahan</div>
          </div>
        </td>
      </tr>
    </table>
  </div>
</div>
HTML;
    }

    /**
     * Default Body Layout Klasik (Bila tanpa gambar latar belakang).
     */
    private function defaultBody(): string
    {
        return <<<HTML
<div class="cert-frame-default">
  <div class="cert-frame-inner">
    <h1>SERTIFIKAT KELULUSAN</h1>
    <p class="cert-intro">Diberikan secara resmi kepada:</p>
    <div class="cert-participant-name">{{nama_peserta}}</div>
    <p class="cert-participant-nip">NIP: {{nip}} &bull; {{instansi}}</p>

    <p class="cert-event-title">
      Telah memenuhi standar evaluasi dan dinyatakan <strong>{{predikat}}</strong> dalam kegiatan:<br>
      <strong>{{judul_kegiatan}}</strong>
    </p>

    <p class="cert-event-meta">
      Beban Belajar: <strong>{{jam_pelajaran}}</strong> &bull; Periode: {{periode_kegiatan}}
    </p>

    <div class="cert-meta">
      <div class="cert-meta-cell label">
        <p class="cert-meta-label">Nomor Sertifikat</p>
        <p class="cert-meta-value cert-number">{{nomor_sertifikat}}</p>
        <p class="cert-meta-label">Tanggal Terbit</p>
        <p class="cert-meta-value">{{tanggal_terbit}}</p>
        <p class="cert-meta-label">Pejabat Pengesah</p>
        <p class="cert-meta-value"><strong>{{penandatangan_nama}}</strong><br><small>{{penandatangan_jabatan}}</small></p>
      </div>
      <div class="cert-meta-cell qr">
        __QR_BLOCK__
        <p class="cert-verify">Verifikasi Keabsahan Digital:<br><a href="{{url_verifikasi}}">{{url_verifikasi}}</a></p>
      </div>
    </div>
  </div>
</div>
HTML;
    }

    /**
     * CSS dasar & styling pendukung print sesuai dimensi halaman PDF.
     */
    private function defaultCss(string $customCss = '', float $widthMm = 297.0, float $heightMm = 210.0): string
    {
        return <<<CSS
<style>
  @page {
    size: {$widthMm}mm {$heightMm}mm;
    margin: 0;
  }
  * { box-sizing: border-box; }
  html, body {
    margin: 0; padding: 0;
    width: {$widthMm}mm; height: {$heightMm}mm;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    color: #0f172a;
    -webkit-print-color-adjust: exact;
    background-color: #ffffff;
  }

  /* Kanvas Penggantian Placeholder PDF Presisi */
  .cert-pdf-placeholder-canvas {
    position: relative;
    width: {$widthMm}mm;
    height: {$heightMm}mm;
    margin: 0;
    padding: 0;
    overflow: hidden;
    background: transparent !important;
  }
  .cert-ph-signature {
    margin: 0;
    padding: 0;
    line-height: normal;
  }

  /* Header Bar jika tanpa background */
  .cert-header {
    display: flex; justify-content: space-between; align-items: center;
    border-bottom: 3px double #15803d; padding: 18px 45px 10px;
  }
  .cert-app { font-size: 16px; font-weight: bold; letter-spacing: 2px; color: #166534; }
  .cert-type { font-size: 11px; color: #64748b; letter-spacing: 1.5px; text-transform: uppercase; }

  /* Frame Default Styling */
  .cert-frame-default {
    padding: 25px 45px;
    height: 100%;
    box-sizing: border-box;
  }
  .cert-frame-inner {
    border: 3px solid #166534;
    padding: 30px 40px;
    min-height: 165mm;
    text-align: center;
    position: relative;
  }
  .cert-frame-inner h1 {
    font-size: 32px; letter-spacing: 6px; color: #166534; margin: 10px 0 6px;
  }
  .cert-intro { font-size: 12px; color: #475569; margin: 0 0 14px; text-transform: uppercase; letter-spacing: 1px; }
  .cert-participant-name {
    font-size: 28px; font-weight: bold; color: #0f172a; letter-spacing: 1px; margin: 6px 0;
  }
  .cert-participant-nip { font-size: 12px; color: #475569; margin: 0 0 20px; }
  .cert-event-title { font-size: 14px; color: #334155; margin: 0 0 8px; line-height: 1.5; }
  .cert-event-title strong { font-size: 18px; color: #166534; display: block; margin-top: 4px; }
  .cert-event-meta { font-size: 12px; color: #64748b; margin-bottom: 25px; }

  .cert-meta { width: 100%; display: table; text-align: left; margin-top: 20px; }
  .cert-meta-cell { display: table-cell; vertical-align: top; }
  .cert-meta-cell.label { width: 65%; }
  .cert-meta-cell.qr { width: 35%; text-align: right; }
  .cert-meta-label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; margin: 0 0 2px; }
  .cert-meta-value { font-size: 13px; color: #0f172a; margin: 0 0 10px; }
  .cert-meta-value.cert-number { font-size: 14px; font-weight: bold; color: #166534; letter-spacing: 1.5px; }
  .cert-verify { font-size: 9px; color: #64748b; text-align: right; margin-top: 6px; }
  .cert-verify a { color: #166534; text-decoration: none; word-break: break-all; }

  /* Overlay Layout (untuk background bergambar) */
  .cert-overlay-container {
    width: {$widthMm}mm; height: {$heightMm}mm;
    padding: 30mm 25mm 20mm;
    box-sizing: border-box;
    position: relative;
    background: transparent;
  }
  .cert-overlay-top { text-align: center; margin-bottom: 20px; }
  .cert-overlay-no {
    font-size: 13px; font-weight: bold; color: #1e3a8a; letter-spacing: 1px;
    background: rgba(255,255,255,0.85); display: inline-block; padding: 4px 18px;
    border-radius: 4px; border: 1px solid rgba(203,213,225,0.6);
  }
  .cert-overlay-main { text-align: center; margin-top: 10px; }
  .cert-overlay-awarded {
    font-size: 12px; letter-spacing: 3px; color: #475569; text-transform: uppercase; font-weight: 600; margin-bottom: 6px;
  }
  .cert-overlay-name {
    font-size: 28px; font-weight: bold; color: #0f172a; letter-spacing: 1px; margin-bottom: 4px;
    text-shadow: 0 1px 2px rgba(255,255,255,0.9);
  }
  .cert-overlay-identity { font-size: 13px; color: #334155; margin-bottom: 14px; }
  .cert-overlay-desc { font-size: 12px; color: #475569; margin-bottom: 6px; }
  .cert-overlay-title {
    font-size: 20px; font-weight: bold; color: #15803d; letter-spacing: 0.5px; margin-bottom: 6px;
  }
  .cert-overlay-meta { font-size: 12px; color: #334155; margin-bottom: 15px; }

  .cert-overlay-footer {
    position: absolute; bottom: 20mm; left: 25mm; right: 25mm;
  }
  .cert-overlay-footer-table { width: 100%; border-collapse: collapse; }
  .cert-overlay-signer {
    width: 65%; vertical-align: bottom; text-align: left; padding-left: 20px;
  }
  .cert-signer-date { font-size: 12px; color: #334155; margin-bottom: 2px; }
  .cert-signer-role { font-size: 12px; font-weight: 600; color: #0f172a; margin-bottom: 48px; }
  .cert-signer-name { font-size: 13px; color: #0f172a; }
  .cert-signer-nip { font-size: 11px; color: #64748b; }

  .cert-overlay-qr {
    width: 35%; vertical-align: bottom; text-align: right; padding-right: 20px;
  }
  .cert-qr-box { display: inline-block; text-align: center; }
  .cert-qr-caption { font-size: 9px; color: #64748b; margin-top: 4px; }

  {$customCss}
</style>
CSS;
    }

    // ===============================================================
    // Background Resolution
    // ===============================================================

    /**
     * Memeriksa apakah template memiliki gambar/PDF background.
     */
    public function hasBackground(array $template): bool
    {
        $bgFileId = isset($template['background_file_id']) ? (int) $template['background_file_id'] : 0;
        if ($bgFileId > 0) {
            return true;
        }

        $payload = $this->decodePayload($template['template_payload'] ?? null);
        return ! empty($payload['background_path']);
    }

    /**
     * Memeriksa apakah background template bertipe dokumen PDF.
     */
    public function isPdfBackground(array $template): bool
    {
        $filePath = $this->getBackgroundPhysicalPath($template);
        if (! $filePath || ! is_file($filePath)) {
            return false;
        }

        return strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) === 'pdf';
    }

    /**
     * Resolve background image menjadi CSS string teroptimasi (base64 data URI).
     * Untuk PDF, menghasilkan layer transparan agar tidak menimpa template PDF asli.
     */
    private function resolveBackgroundStyle(array $template): string
    {
        $filePath = $this->getBackgroundPhysicalPath($template);
        if (! $filePath || ! is_file($filePath)) {
            return '';
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            return '<style>
              html, body {
                background: transparent !important;
                background-color: transparent !important;
              }
              .cert-overlay-container, .cert-frame-default, .cert-frame-inner, .cert-pdf-placeholder-canvas {
                background: transparent !important;
                background-color: transparent !important;
              }
            </style>';
        }

        $mimeType = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            default       => 'image/png',
        };

        $imageData = @file_get_contents($filePath);
        if ($imageData === false || strlen($imageData) === 0) {
            return '';
        }

        $base64Data = base64_encode($imageData);
        $dataUri    = 'data:' . $mimeType . ';base64,' . $base64Data;

        return '<style>
          body {
            background-image: url("' . $dataUri . '");
            background-position: center center;
            background-repeat: no-repeat;
            background-size: 100% 100%;
          }
        </style>';
    }

    /**
     * Cari path fisik file background template.
     */
    public function getBackgroundPhysicalPath(array $template): ?string
    {
        $bgFileId = isset($template['background_file_id']) ? (int) $template['background_file_id'] : 0;

        if ($bgFileId > 0) {
            try {
                $db  = db_connect();
                $row = $db->table('file_assets')
                    ->select(['disk', 'path', 'lifecycle_status'])
                    ->where('id', $bgFileId)
                    ->get()
                    ->getRowArray();

                if ($row && ($row['lifecycle_status'] ?? '') === 'available') {
                    $relative = ltrim((string) $row['path'], '/');
                    $abs      = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, WRITEPATH . $relative);
                    if (is_file($abs)) {
                        return $abs;
                    }
                }
            } catch (\Throwable) {
            }
        }

        $payload = $this->decodePayload($template['template_payload'] ?? null);
        if (! empty($payload['background_path'])) {
            $testPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, WRITEPATH . ltrim((string) $payload['background_path'], '/\\'));
            if (is_file($testPath)) {
                return $testPath;
            }
        }

        return null;
    }

    // ===============================================================
    // QR Block Rendering
    // ===============================================================

    private function renderQrBlock(array $payload, array $certificateData): string
    {
        $position = (string) ($payload['qr_position'] ?? 'right');
        $img      = '<!--CERTIFICATE_QR_PLACEHOLDER-->';

        if ($position === 'bottom') {
            return '<div style="text-align:center;margin-top:20px;">' . $img . '</div>'
                . '<p class="cert-verify" style="margin-top:6px;text-align:center;">Verifikasi publik: <a href="{{url_verifikasi}}">{{url_verifikasi}}</a></p>';
        }

        return '';
    }

    // ===============================================================
    // Payload parsing
    // ===============================================================

    /**
     * @return array<string, mixed>
     */
    public function decodePayload($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw) && $raw !== '') {
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}
