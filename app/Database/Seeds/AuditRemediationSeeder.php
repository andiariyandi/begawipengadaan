<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AuditRemediationSeeder — Seeder pembersihan data legacy, penghapusan role evaluator
 * (digabung ke Operator), regenerasi PDF sertifikat historis yang NULL, dan
 * pengisian data pendukung fitur (tanpa mengubah skema/kolom database).
 */
class AuditRemediationSeeder extends Seeder
{
    public function run(): void
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        echo "=== 1. Membersihkan Role Evaluator & Menggabungkan Hak Akses ke Operator ===\n";
        $operatorRole  = $db->table('roles')->where('key', 'operator')->get()->getRowArray();
        $evaluatorRole = $db->table('roles')->where('key', 'evaluator')->get()->getRowArray();

        if ($operatorRole && $evaluatorRole) {
            $opId = (int) $operatorRole['id'];
            $evId = (int) $evaluatorRole['id'];

            // Pindahkan semua permission dari evaluator ke operator bila belum ada
            $evPerms = $db->table('role_permissions')->where('role_id', $evId)->get()->getResultArray();
            $opPerms = array_map('intval', array_column(
                $db->table('role_permissions')->where('role_id', $opId)->get()->getResultArray(),
                'permission_id'
            ));

            foreach ($evPerms as $ep) {
                $pid = (int) $ep['permission_id'];
                if (! in_array($pid, $opPerms, true)) {
                    $db->table('role_permissions')->insert([
                        'role_id'       => $opId,
                        'permission_id' => $pid,
                        'granted_by'    => $ep['granted_by'] ?? 13,
                        'created_at'    => $now,
                    ]);
                }
            }

            // Pindahkan user_roles dari evaluator ke operator bila ada
            $evUsers = $db->table('user_roles')->where('role_id', $evId)->get()->getResultArray();
            foreach ($evUsers as $eu) {
                $uid = (int) $eu['user_id'];
                $hasOp = $db->table('user_roles')->where(['user_id' => $uid, 'role_id' => $opId])->countAllResults() > 0;
                if (! $hasOp) {
                    $db->table('user_roles')->insert([
                        'user_id'     => $uid,
                        'role_id'     => $opId,
                        'status'      => $eu['status'],
                        'assigned_by' => $eu['assigned_by'],
                        'assigned_at' => $eu['assigned_at'] ?? $now,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ]);
                }
            }

            // Bersihkan tabel relasi dan hapus role evaluator
            $db->table('role_permissions')->where('role_id', $evId)->delete();
            $db->table('user_roles')->where('role_id', $evId)->delete();
            $db->table('role_requests')->where('requested_role_id', $evId)->delete();
            $db->table('roles')->where('id', $evId)->delete();
            echo "  [OK] Role 'evaluator' (#{$evId}) dihapus & seluruh hak aksesnya digabung ke 'operator' (#{$opId}).\n";
        } else {
            echo "  [OK] Role 'evaluator' sudah bersih.\n";
        }

        // Bersihkan class_evaluators jika ada isinya
        if ($db->tableExists('class_evaluators')) {
            $db->table('class_evaluators')->emptyTable();
        }

        echo "\n=== 2. Membersihkan Data Legacy / Sampah Testing (test_resource) ===\n";
        $deletedApprovals = $db->table('approvals')
            ->whereIn('resource_type', ['test_resource', 'test_single_resource'])
            ->delete();
        echo "  [OK] Data sampah approvals ('test_resource') berhasil dibersihkan.\n";

        $db->table('email_verifications')->emptyTable();
        $db->table('password_resets')->where('expires_at <', $now)->delete();

        echo "\n=== 3. Memperbaiki & Meregenerasi PDF untuk Sertifikat Historis (CERT-EVT-* / pdf_file_id NULL) ===\n";
        $nullPdfCerts = $db->table('certificates')
            ->groupStart()
            ->where('pdf_file_id IS NULL', null, false)
            ->orWhere('template_id IS NULL', null, false)
            ->orLike('certificate_number', 'CERT-', 'after')
            ->groupEnd()
            ->get()
            ->getResultArray();

        $generator = new \App\Services\CertificateGenerator();
        $regenCount = 0;
        foreach ($nullPdfCerts as $cRow) {
            try {
                $res = $generator->regeneratePdfForCertificate((int) $cRow['id'], 13, true);
                $regenCount++;
                echo "  [OK] Cert #{$cRow['id']} diperbarui -> {$res['certificate_number']} (PDF Asset #{$res['pdf_file_id']})\n";
            } catch (\Throwable $e) {
                echo "  [WARN] Gagal regenerasi Cert #{$cRow['id']}: {$e->getMessage()}\n";
            }
        }
        echo "  Total sertifikat diregenerasi: {$regenCount}\n";

        echo "\n=== 4. Mengisi Data Pendukung Fitur & Testing (Tanpa Ubah Skema) ===\n";
        $adminUser   = $db->table('users')->where('username', 'superadmin')->orWhere('id', 13)->get()->getRowArray()
            ?: $db->table('users')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        $adminId     = (int) ($adminUser['id'] ?? 13);
        $trainerRole = $db->table('roles')->where('key', 'trainer')->get()->getRowArray();
        $opRole      = $db->table('roles')->where('key', 'operator')->get()->getRowArray();

        // 4a. Competency Certificates & Competency Gap Snapshots
        $intelService = new \App\Services\Admin\IntelligenceAnalyticsService($db);
        $intelService->ensureBaselineData();

        if ($db->table('competency_certificates')->countAllResults() === 0) {
            $records = $db->table('jf_ppbj_records')->get()->getResultArray();
            $certNum = 100;
            $certsToInsert = [];
            foreach ($records as $rec) {
                $recId = (int) $rec['id'];
                $certNum++;
                $certsToInsert[] = [
                    'jf_ppbj_record_id'   => $recId,
                    'certificate_type_id' => ($certNum % 5) + 1,
                    'certificate_number'  => 'SKKNI-PBJ/LPG/2026/' . str_pad((string) $certNum, 5, '0', STR_PAD_LEFT),
                    'issued_at'           => '2024-03-15 08:00:00',
                    'expires_at'          => '2027-03-15 08:00:00',
                    'status'              => 'verified',
                    'verified_by'         => $adminId,
                    'verified_at'         => $now,
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ];
                if ($recId % 2 === 0) {
                    $certNum++;
                    $certsToInsert[] = [
                        'jf_ppbj_record_id'   => $recId,
                        'certificate_type_id' => (($certNum + 1) % 5) + 1,
                        'certificate_number'  => 'SKKNI-PBJ/LPG/2026/' . str_pad((string) $certNum, 5, '0', STR_PAD_LEFT),
                        'issued_at'           => '2023-11-10 08:00:00',
                        'expires_at'          => '2026-12-20 08:00:00',
                        'status'              => 'verified',
                        'verified_by'         => $adminId,
                        'verified_at'         => $now,
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }
            }
            if (! empty($certsToInsert)) {
                $db->table('competency_certificates')->insertBatch($certsToInsert);
                echo "  [SEED] competency_certificates: " . count($certsToInsert) . " baris.\n";
            }
        }

        if ($db->table('competency_gap_snapshots')->countAllResults() === 0) {
            $gapData = $intelService->getCompetencyGapAnalysis(2026);
            $compRow = $db->table('competencies')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $compId  = (int) ($compRow['id'] ?? 1);
            $snaps   = [];
            foreach (array_slice($gapData['matrix'] ?? [], 0, 10) as $row) {
                $snaps[] = [
                    'organization_unit_id' => (int) ($row['opd_id'] ?? $row['id'] ?? 1),
                    'competency_id'        => $compId,
                    'period_date'          => date('Y-m-d'),
                    'required_count'       => (int) ($row['need'] ?? $row['required_count'] ?? 0),
                    'available_count'      => (int) ($row['available'] ?? $row['available_count'] ?? 0),
                    'gap_count'            => (int) ($row['gap'] ?? $row['gap_count'] ?? 0),
                    'metadata'             => json_encode([
                        'year'          => 2026,
                        'coverage_pct'  => $row['coverage_pct'] ?? 0,
                        'priority_rank' => $row['priority'] ?? 'Sedang',
                    ], JSON_UNESCAPED_UNICODE),
                    'generated_by'         => $adminId,
                    'created_at'           => $now,
                ];
            }
            if (! empty($snaps)) {
                $db->table('competency_gap_snapshots')->insertBatch($snaps);
                echo "  [SEED] competency_gap_snapshots: " . count($snaps) . " baris.\n";
            }
        }

        // 4b. News (Berita PBJ) jika kosong
        if ($db->table('news')->countAllResults() === 0) {
            $cats = $db->table('news_categories')->orderBy('id', 'ASC')->get()->getResultArray();
            $cat1 = (int) ($cats[0]['id'] ?? 1);
            $cat2 = (int) ($cats[1]['id'] ?? $cat1);

            $newsRows = [
                [
                    'category_id'     => $cat1,
                    'title'           => 'Biro PBJ Setda Provinsi Lampung Perkuat Akselerasi E-Purchasing Katalog Elektronik Versi 6',
                    'slug'            => 'biro-pbj-lampung-akselerasi-katalog-elektronik-v6',
                    'summary'         => 'Pemerintah Provinsi Lampung melalui Biro Pengadaan Barang dan Jasa menyelenggarakan pendampingan intensif transisi Katalog Elektronik V6 bagi seluruh PPK dan Pejabat Pengadaan OPD.',
                    'reading_time'    => '4 Menit Baca',
                    'tags'            => 'E-Katalog V6, Transformasi Digital, UKPBJ Lampung',
                    'content'         => '<p><strong>BANDAR LAMPUNG</strong> — Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung terus mengakselerasi kesiapan seluruh Organisasi Perangkat Daerah (OPD) dalam mengimplementasikan Katalog Elektronik Versi 6 (E-Katalog V6).</p><p>Melalui portal ekosistem <em>BegawiPengadaan</em>, seluruh Pejabat Pembuat Komitmen (PPK), Pejabat Pengadaan, dan Pokja Pemilihan dapat mengakses panduan teknis, regulasi terbaru, serta mengikuti kelas pembelajaran mandiri secara terintegrasi.</p>',
                    'source_name'     => 'Humas Biro PBJ Provinsi Lampung',
                    'is_featured'     => 1,
                    'carousel_enabled'=> 1,
                    'carousel_order'  => 1,
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'published_at'    => date('Y-m-d H:i:s', strtotime('-3 days')),
                    'view_count'      => 148,
                    'author_id'       => $adminId,
                    'reviewed_by'     => $adminId,
                    'published_by'    => $adminId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
                [
                    'category_id'     => $cat2,
                    'title'           => 'Peningkatan Kapasitas SDM JF-PPBJ Menuju UKPBJ Proaktif Level Kematangan Terbaik',
                    'slug'            => 'peningkatan-kapasitas-sdm-jf-ppbj-ukpbj-lampung',
                    'summary'         => 'Program pengembangan kompetensi berkelanjutan melalui Arena Belajar BegawiPengadaan mendukung pemenuhan standar kompetensi SKKNI Pengadaan Barang/Jasa.',
                    'reading_time'    => '3 Menit Baca',
                    'tags'            => 'JF PPBJ, Kompetensi, Arena Belajar',
                    'content'         => '<p><strong>BANDAR LAMPUNG</strong> — Pengembangan Sumber Daya Manusia Jabatan Fungsional Pengelola Pengadaan Barang/Jasa (JF-PPBJ) menjadi pilar utama dalam mempertahankan tingkat kematangan UKPBJ Provinsi Lampung.</p><p>Seluruh riwayat pelatihan, nilai evaluasi Pre-Test dan Post-Test, hingga penerbitan sertifikat digital kini tercatat secara transparan dan dapat diverifikasi melalui QR Code kriptografi SHA-256.</p>',
                    'source_name'     => 'Bidang Pembinaan & Advokasi PBJ',
                    'is_featured'     => 1,
                    'carousel_enabled'=> 1,
                    'carousel_order'  => 2,
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'published_at'    => date('Y-m-d H:i:s', strtotime('-1 day')),
                    'view_count'      => 95,
                    'author_id'       => $adminId,
                    'reviewed_by'     => $adminId,
                    'published_by'    => $adminId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
                [
                    'category_id'     => $cat1,
                    'title'           => 'Optimalisasi Penggunaan Produk Dalam Negeri (PDN) dan UMK pada Belanja Pengadaan Tahun 2026',
                    'slug'            => 'optimalisasi-pdn-dan-umk-belanja-pengadaan-2026',
                    'summary'         => 'Langkah strategis Biro PBJ Provinsi Lampung dalam mengawal target komitmen Tingkat Komponen Dalam Negeri (TKDN) dan pemberdayaan usaha mikro kecil daerah.',
                    'reading_time'    => '5 Menit Baca',
                    'tags'            => 'PDN, TKDN, UMK Lampung, Pengadaan Berkelanjutan',
                    'content'         => '<p><strong>BANDAR LAMPUNG</strong> — Komitmen afirmasi Produk Dalam Negeri (PDN) dan pelaku Usaha Mikro, Kecil, dan Koperasi (UMKK) di Provinsi Lampung terus diperkuat melalui reviu perencanaan pengadaan yang terukur di seluruh perangkat daerah.</p>',
                    'source_name'     => 'Biro PBJ Setda Provinsi Lampung',
                    'is_featured'     => 0,
                    'carousel_enabled'=> 1,
                    'carousel_order'  => 3,
                    'workflow_status' => 'published',
                    'visibility'      => 'public',
                    'published_at'    => $now,
                    'view_count'      => 64,
                    'author_id'       => $adminId,
                    'reviewed_by'     => $adminId,
                    'published_by'    => $adminId,
                    'created_at'      => $now,
                    'updated_at'      => $now,
                ],
            ];
            $db->table('news')->insertBatch($newsRows);
            echo "  [SEED] news: " . count($newsRows) . " berita publik.\n";
        }

        // 4c. Satisfaction Surveys (Survei Kepuasan Masyarakat) jika kosong
        if ($db->table('satisfaction_surveys')->countAllResults() === 0) {
            $surveys = [
                [
                    'ticket_number'             => 'SKM-20261020-A101',
                    'rating_overall'            => 5,
                    'service_access_rating'     => 5,
                    'service_regulation_rating' => 5,
                    'service_learning_rating'   => 5,
                    'service_helpdesk_rating'   => 5,
                    'respondent_role'           => 'ASN / Pejabat Pembuat Komitmen (PPK)',
                    'is_anonymous'              => 0,
                    'respondent_name'           => 'Ir. Hendra Saputra, M.T.',
                    'respondent_institution'    => 'Dinas Bina Marga dan Bina Konstruksi Prov. Lampung',
                    'respondent_contact'        => '081272001122',
                    'feedback'                  => 'Portal BegawiPengadaan sangat membantu kami dalam mencari referensi regulasi terbaru dan mengikuti bimtek pengadaan secara daring.',
                    'recommendation'            => 'Sangat Merekomendasikan',
                    'ip_address'                => '103.142.11.10',
                    'user_agent'                => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'created_at'                => date('Y-m-d H:i:s', strtotime('-4 days')),
                    'updated_at'                => date('Y-m-d H:i:s', strtotime('-4 days')),
                ],
                [
                    'ticket_number'             => 'SKM-20261022-B204',
                    'rating_overall'            => 5,
                    'service_access_rating'     => 5,
                    'service_regulation_rating' => 5,
                    'service_learning_rating'   => 4,
                    'service_helpdesk_rating'   => 5,
                    'respondent_role'           => 'Pokja Pemilihan / JF-PPBJ',
                    'is_anonymous'              => 0,
                    'respondent_name'           => 'Dewi Kartika Sari, S.E., M.M.',
                    'respondent_institution'    => 'Biro Pengadaan Barang dan Jasa Setda Prov. Lampung',
                    'respondent_contact'        => '081369004455',
                    'feedback'                  => 'Fitur verifikasi sertifikat digital dengan QR Code dan unduhan template dokumen KAK/HPS sangat mempercepat pekerjaan teknis.',
                    'recommendation'            => 'Sangat Merekomendasikan',
                    'ip_address'                => '103.142.11.24',
                    'user_agent'                => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'created_at'                => date('Y-m-d H:i:s', strtotime('-2 days')),
                    'updated_at'                => date('Y-m-d H:i:s', strtotime('-2 days')),
                ],
                [
                    'ticket_number'             => 'SKM-20261024-C309',
                    'rating_overall'            => 4,
                    'service_access_rating'     => 5,
                    'service_regulation_rating' => 4,
                    'service_learning_rating'   => 5,
                    'service_helpdesk_rating'   => 4,
                    'respondent_role'           => 'Pelaku Usaha / Penyedia',
                    'is_anonymous'              => 0,
                    'respondent_name'           => 'PT Siger Karya Mandiri',
                    'respondent_institution'    => 'Asosiasi Penyedia Barang/Jasa Lampung',
                    'respondent_contact'        => 'info@sigerkarya.co.id',
                    'feedback'                  => 'Layanan konsultasi helpdesk dua arah merespons kendala pendaftaran E-Katalog dengan cepat dan jelas.',
                    'recommendation'            => 'Merekomendasikan',
                    'ip_address'                => '114.125.80.12',
                    'user_agent'                => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
                    'created_at'                => $now,
                    'updated_at'                => $now,
                ],
            ];
            $db->table('satisfaction_surveys')->insertBatch($surveys);
            echo "  [SEED] satisfaction_surveys: " . count($surveys) . " responden IKM.\n";
        }

        // 4d. Role Requests (Permohonan Role) jika kosong
        if ($db->table('role_requests')->countAllResults() === 0 && $trainerRole) {
            $pesertaUser = $db->table('users u')
                ->select('u.id')
                ->join('user_roles ur', 'ur.user_id = u.id')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('r.key', 'peserta')
                ->limit(1)
                ->get()
                ->getRowArray();

            if ($pesertaUser) {
                $db->table('role_requests')->insert([
                    'user_id'           => (int) $pesertaUser['id'],
                    'requested_role_id' => (int) $trainerRole['id'],
                    'status'            => 'submitted',
                    'reason'            => 'Telah memiliki Sertifikat Kompetensi PBJ Ahli Muda LKPP dan ditugaskan sebagai fasilitator pendampingan E-Katalog V6 di lingkungan Pemprov Lampung.',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
                echo "  [SEED] role_requests: 1 sampel permohonan role Trainer.\n";
            }
        }

        // 4e. Assignment Submissions & Feedbacks jika kosong
        $firstAssignment = $db->table('assignments')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        $firstEnrollment = $db->table('enrollments')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();

        if ($firstAssignment && $firstEnrollment && $db->table('assignment_submissions')->countAllResults() === 0) {
            $db->table('assignment_submissions')->insert([
                'assignment_id'  => (int) $firstAssignment['id'],
                'enrollment_id'  => (int) $firstEnrollment['id'],
                'attempt_number' => 1,
                'status'         => 'graded',
                'content'        => 'Analisis penyusunan Spesifikasi Teknis dan HPS paket pengadaan peralatan teknologi informasi menggunakan metode E-Purchasing Katalog Elektronik V6 sesuai Perpres 12/2021.',
                'submitted_at'   => date('Y-m-d H:i:s', strtotime('-2 days')),
                'graded_at'      => date('Y-m-d H:i:s', strtotime('-1 day')),
                'graded_by'      => $adminId,
                'score'          => 92.50,
                'feedback'       => 'Sangat baik! Struktur KAK, identifikasi kebutuhan, dan referensi TKDN/PDN telah sesuai dengan ketentuan terbaru.',
                'created_at'     => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
            ]);
            echo "  [SEED] assignment_submissions: 1 sampel pengumpulan & penilaian tugas.\n";
        }

        if ($firstEnrollment && $db->table('feedbacks')->countAllResults() === 0) {
            $db->table('feedbacks')->insert([
                'enrollment_id' => (int) $firstEnrollment['id'],
                'rating'        => 5,
                'comment'       => 'Materi pelatihan sangat sistematis, studi kasus nyata di Pemprov Lampung mudah dipahami, dan sertifikat langsung terbit.',
                'status'        => 'submitted',
                'submitted_at'  => $now,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            echo "  [SEED] feedbacks: 1 sampel ulasan kelas peserta.\n";
        }

        // 4f. User Procurement Roles & Bookmarks jika kosong
        $procRole = $db->table('procurement_roles')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
        if ($procRole && $firstEnrollment && $db->table('user_procurement_roles')->countAllResults() === 0) {
            $db->table('user_procurement_roles')->insert([
                'user_id'             => (int) $firstEnrollment['user_id'],
                'procurement_role_id' => (int) $procRole['id'],
                'verification_status' => 'verified',
                'verified_by'         => $adminId,
                'verified_at'         => $now,
                'assignment_start_at' => '2026-01-02 08:00:00',
                'assignment_end_at'   => '2026-12-31 17:00:00',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            echo "  [SEED] user_procurement_roles: 1 sampel penugasan peran pengadaan.\n";
        }

        if ($firstEnrollment && $db->table('bookmarks')->countAllResults() === 0) {
            $uid = (int) $firstEnrollment['user_id'];
            $art = $db->table('knowledge_articles')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $reg = $db->table('regulations')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $cls = $db->table('learning_classes')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $bms = [];
            if ($art) {
                $bms[] = ['user_id' => $uid, 'bookmarkable_type' => 'knowledge_article', 'bookmarkable_id' => (int) $art['id'], 'created_at' => $now];
            }
            if ($reg) {
                $bms[] = ['user_id' => $uid, 'bookmarkable_type' => 'regulation', 'bookmarkable_id' => (int) $reg['id'], 'created_at' => $now];
            }
            if ($cls) {
                $bms[] = ['user_id' => $uid, 'bookmarkable_type' => 'learning_class', 'bookmarkable_id' => (int) $cls['id'], 'created_at' => $now];
            }
            if (! empty($bms)) {
                $db->table('bookmarks')->insertBatch($bms);
                echo "  [SEED] bookmarks: " . count($bms) . " sampel bookmark pengguna.\n";
            }
        }

        // 4g. Regulation Competencies link jika kosong
        if ($db->table('regulation_competencies')->countAllResults() === 0) {
            $reg = $db->table('regulations')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            $cmp = $db->table('competencies')->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
            if ($reg && $cmp) {
                $db->table('regulation_competencies')->insert([
                    'regulation_id' => (int) $reg['id'],
                    'competency_id' => (int) $cmp['id'],
                ]);
                echo "  [SEED] regulation_competencies: 1 relasi kompetensi regulasi.\n";
            }
        }

        // 4h. Knowledge Tags & Categories link jika kosong
        if ($db->table('knowledge_tags')->countAllResults() === 0) {
            $tags = [
                ['name' => 'E-Katalog V6', 'slug' => 'e-katalog-v6', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Perpres 12/2021', 'slug' => 'perpres-12-2021', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'TKDN & PDN', 'slug' => 'tkdn-pdn', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Manajemen Kontrak', 'slug' => 'manajemen-kontrak', 'created_at' => $now, 'updated_at' => $now],
            ];
            $db->table('knowledge_tags')->insertBatch($tags);
            echo "  [SEED] knowledge_tags: 4 tag pengetahuan.\n";
        }

        echo "\n=== SELESAI: Database berhasil dibersihkan & dilengkapi tanpa mengubah skema/kolom! ===\n";
    }
}
