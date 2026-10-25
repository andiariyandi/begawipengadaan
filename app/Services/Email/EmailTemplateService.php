<?php

namespace App\Services\Email;

/**
 * Service to generate formal, responsive HTML email templates
 * compliant with Government of Lampung Province branding.
 */
class EmailTemplateService
{
    /**
     * Primary layout wrapper with official Pemprov Lampung identity.
     */
    public function wrapLayout(string $title, string $contentHtml, array $options = []): string
    {
        $appName     = (string) setting('general.app_name', 'BegawiPengadaan');
        $agencyName  = (string) setting('organization.agency_name', 'Biro Pengadaan Barang dan Jasa Provinsi Lampung');
        $address     = (string) setting('organization.office_address', 'Kompleks Kantor Gubernur Lampung, Jl. Wolter Monginsidi No. 69, Telukbetung Utara, Bandar Lampung');
        $helpdeskWa  = (string) setting('helpdesk.whatsapp_number', '08117905556');
        $year        = date('Y');

        $actionBtnHtml = '';
        if (! empty($options['action_url']) && ! empty($options['action_text'])) {
            $actionUrl  = esc($options['action_url']);
            $actionText = esc($options['action_text']);
            $actionBtnHtml = <<<HTML
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="margin: 28px 0;">
                <tr>
                    <td align="center">
                        <a href="{$actionUrl}" target="_blank" style="display: inline-block; background-color: #4f46e5; color: #ffffff; text-decoration: none; padding: 14px 28px; border-radius: 8px; font-weight: 600; font-size: 15px; letter-spacing: 0.3px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);">
                            {$actionText}
                        </a>
                    </td>
                </tr>
            </table>
            <p style="margin: 0 0 16px; font-size: 12px; color: #64748b; line-height: 1.5;">
                Jika tombol di atas tidak dapat diklik, salin dan tempel tautan berikut ke peramban web Anda:<br>
                <a href="{$actionUrl}" style="color: #4f46e5; word-break: break-all;">{$actionUrl}</a>
            </p>
HTML;
        }

        $badgeHtml = '';
        if (! empty($options['badge'])) {
            $badgeText  = esc($options['badge']);
            $badgeColor = $options['badge_color'] ?? '#e0e7ff';
            $textColor  = $options['badge_text_color'] ?? '#3730a3';
            $badgeHtml  = "<span style=\"display: inline-block; background: {$badgeColor}; color: {$textColor}; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 12px;\">{$badgeText}</span><br>";
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; line-height: 1.6;">
    <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" width="100%" max-width="600" border="0" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02); border: 1px solid #e2e8f0;">
                    
                    <!-- Official Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); padding: 28px 32px; text-align: left; border-bottom: 3px solid #f59e0b;">
                            <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <div style="font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; color: #fbbf24; font-weight: 700; margin-bottom: 4px;">
                                            PEMERINTAH PROVINSI LAMPUNG
                                        </div>
                                        <div style="font-size: 20px; font-weight: 700; color: #ffffff; letter-spacing: -0.3px;">
                                            {$appName}
                                        </div>
                                        <div style="font-size: 13px; color: #cbd5e1; margin-top: 2px;">
                                            {$agencyName}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 32px; background-color: #ffffff;">
                            {$badgeHtml}
                            <h1 style="margin: 0 0 20px; font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1.4;">
                                {$title}
                            </h1>
                            <div style="font-size: 15px; color: #334155; line-height: 1.7;">
                                {$contentHtml}
                            </div>
                            {$actionBtnHtml}
                        </td>
                    </tr>

                    <!-- Official Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 24px 32px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #64748b; line-height: 1.6;">
                            <p style="margin: 0 0 8px; font-weight: 600; color: #475569;">
                                {$agencyName}
                            </p>
                            <p style="margin: 0 0 12px;">
                                {$address}
                            </p>
                            <div style="border-top: 1px dashed #cbd5e1; padding-top: 12px; margin-top: 12px; font-size: 11px; color: #94a3b8;">
                                Pesan ini dikirimkan melalui sistem resmi <strong>{$appName}</strong> Provinsi Lampung.<br>
                                Jika membutuhkan bantuan resmi pengadaan, hubungi Helpdesk Biro PBJ di WhatsApp: <a href="https://wa.me/{$helpdeskWa}" style="color: #4f46e5; text-decoration: none; font-weight: 600;">{$helpdeskWa}</a>.
                            </div>
                        </td>
                    </tr>
                </table>

                <!-- Bottom Subtext -->
                <table role="presentation" width="100%" max-width="600" border="0" cellpadding="0" cellspacing="0" style="max-width: 600px; margin-top: 16px;">
                    <tr>
                        <td align="center" style="font-size: 11px; color: #94a3b8; padding: 0 8px;">
                            &copy; {$year} Biro Pengadaan Barang dan Jasa Provinsi Lampung. Seluruh hak cipta dilindungi.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
    }

    /**
     * Password reset email template.
     */
    public function renderPasswordReset(string $userName, string $resetUrl, int $validMinutes = 60): string
    {
        $title = 'Permintaan Pengaturan Ulang Kata Sandi Akun';
        $safeName = esc($userName ?: 'Pengguna BegawiPengadaan');

        $content = <<<HTML
        <p>Yth. <strong>{$safeName}</strong>,</p>
        <p>Kami menerima permohonan untuk mengatur ulang kata sandi akun Anda pada portal <strong>BegawiPengadaan Provinsi Lampung</strong>.</p>
        <p>Untuk membuat kata sandi baru, silakan klik tombol verifikasi di bawah ini:</p>
        <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; margin: 20px 0; border-radius: 0 8px 8px 0;">
            <p style="margin: 0; font-size: 13px; color: #92400e;">
                <strong>Perhatian Keamanan:</strong> Tautan ini hanya berlaku selama <strong>{$validMinutes} menit</strong> dan hanya dapat digunakan <strong>1 (satu) kali</strong> demi menjaga keamanan akun Anda.
            </p>
        </div>
        <p>Jika Anda tidak merasa mengajukan permohonan ini, abaikan pesan email ini. Kata sandi akun Anda akan tetap aman dan tidak mengalami perubahan.</p>
HTML;

        return $this->wrapLayout($title, $content, [
            'badge'            => 'Keamanan Akun',
            'badge_color'      => '#fee2e2',
            'badge_text_color' => '#991b1b',
            'action_url'       => $resetUrl,
            'action_text'      => 'Atur Ulang Kata Sandi Saya',
        ]);
    }

    /**
     * Account approved notification email template.
     */
    public function renderAccountApproved(string $userName, string $loginUrl): string
    {
        $title = 'Akun Anda Telah Disetujui dan Siap Digunakan';
        $safeName = esc($userName ?: 'Pengguna BegawiPengadaan');

        $content = <<<HTML
        <p>Yth. <strong>{$safeName}</strong>,</p>
        <p>Kabar baik! Permohonan pendaftaran akun Anda pada portal <strong>BegawiPengadaan Biro Pengadaan Barang dan Jasa Provinsi Lampung</strong> telah berhasil diverifikasi dan <strong>disetujui</strong> oleh tim Administrator/Operator.</p>
        <div style="background-color: #ecfdf5; border-left: 4px solid #10b981; padding: 12px 16px; margin: 20px 0; border-radius: 0 8px 8px 0;">
            <p style="margin: 0; font-size: 13.5px; color: #065f46;">
                <strong>Status Akun:</strong> <span style="display: inline-block; background-color: #10b981; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 11px;">AKTIF</span> &bull; Akun Anda kini memiliki akses penuh ke fitur Ruang Kerja (Workspace), kurikulum Arena Belajar, event pembinaan PBJ, dan layanan helpdesk konsultasi.
            </p>
        </div>
        <p>Silakan masuk ke platform menggunakan alamat email dan kata sandi yang Anda daftarkan dengan mengeklik tombol di bawah ini:</p>
HTML;

        return $this->wrapLayout($title, $content, [
            'badge'            => 'Akun Terverifikasi',
            'badge_color'      => '#d1fae5',
            'badge_text_color' => '#065f46',
            'action_url'       => $loginUrl,
            'action_text'      => 'Masuk ke Akun Saya Sekarang',
        ]);
    }

    /**
     * Broadcast mass email template.
     */
    public function renderBroadcast(string $title, string $messageBody, array $options = []): string
    {
        $badge = ! empty($options['audience_label']) ? ('Siaran: ' . $options['audience_label']) : 'Pengumuman Resmi';
        
        return $this->wrapLayout($title, $messageBody, [
            'badge'            => $badge,
            'badge_color'      => '#dbeafe',
            'badge_text_color' => '#1e40af',
            'action_url'       => $options['action_url'] ?? null,
            'action_text'      => $options['action_text'] ?? null,
        ]);
    }

    /**
     * Personal / Transactional Send-to-One email template.
     */
    public function renderPersonal(string $recipientName, string $title, string $messageBody, array $options = []): string
    {
        $safeName = esc($recipientName ?: 'Bapak/Ibu');
        $content = "<p>Yth. <strong>{$safeName}</strong>,</p>" . $messageBody;

        return $this->wrapLayout($title, $content, [
            'badge'            => $options['badge'] ?? 'Pemberitahuan Khusus',
            'badge_color'      => '#fef3c7',
            'badge_text_color' => '#92400e',
            'action_url'       => $options['action_url'] ?? null,
            'action_text'      => $options['action_text'] ?? null,
        ]);
    }

    /**
     * Test connection email template.
     */
    public function renderTestEmail(string $adminName, array $configDetails): string
    {
        $title = 'Uji Coba Pengiriman Google Mail SMTP Berhasil';
        $safeAdmin = esc($adminName);
        $timestamp = date('d F Y, H:i:s') . ' WIB';

        $smtpHost   = esc($configDetails['smtp_host'] ?? 'smtp.gmail.com');
        $smtpPort   = esc($configDetails['smtp_port'] ?? '587');
        $smtpCrypto = esc($configDetails['smtp_crypto'] ?? 'tls');
        $smtpUser   = esc($configDetails['smtp_user'] ?? '-');
        $fromName   = esc($configDetails['from_name'] ?? 'BegawiPengadaan');

        $content = <<<HTML
        <p>Halo <strong>{$safeAdmin}</strong>,</p>
        <p>Selamat! Handshake SMTP ke server <strong>Google Mail</strong> telah berhasil dilakukan dari Admin Dashboard BegawiPengadaan Provinsi Lampung.</p>
        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin: 20px 0;">
            <p style="margin: 0 0 8px; font-weight: 700; color: #1e293b; font-size: 14px;">Ringkasan Konfigurasi Aktif:</p>
            <table role="presentation" border="0" cellpadding="4" cellspacing="0" style="width: 100%; font-size: 13px; color: #475569;">
                <tr><td width="35%"><strong>Server SMTP:</strong></td><td>{$smtpHost}:{$smtpPort} ({$smtpCrypto})</td></tr>
                <tr><td><strong>Akun Gmail:</strong></td><td>{$smtpUser}</td></tr>
                <tr><td><strong>Nama Pengirim:</strong></td><td>{$fromName}</td></tr>
                <tr><td><strong>Waktu Uji Coba:</strong></td><td>{$timestamp}</td></tr>
            </table>
        </div>
        <p style="color: #166534; font-weight: 600;">✓ Sistem email BegawiPengadaan kini siap digunakan untuk pengiriman email broadcast, personal, dan reset kata sandi secara manual.</p>
HTML;

        return $this->wrapLayout($title, $content, [
            'badge'            => 'Uji Coba Sistem',
            'badge_color'      => '#dcfce7',
            'badge_text_color' => '#166534',
        ]);
    }
}
