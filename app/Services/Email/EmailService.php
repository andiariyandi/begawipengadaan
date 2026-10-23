<?php

namespace App\Services\Email;

use App\Services\AuditService;
use App\Services\SystemSettingService;
use CodeIgniter\Email\Email;

/**
 * Core Email Service wrapping CodeIgniter 4 Email driver for Google Mail SMTP.
 */
class EmailService
{
    private EmailTemplateService $templateService;
    private SystemSettingService $settingService;
    private AuditService $auditService;

    public function __construct(
        ?EmailTemplateService $templateService = null,
        ?SystemSettingService $settingService = null,
        ?AuditService $auditService = null
    ) {
        $this->templateService = $templateService ?? new EmailTemplateService();
        $this->settingService  = $settingService ?? new SystemSettingService();
        $this->auditService    = $auditService ?? new AuditService();
    }

    /**
     * Retrieve active Gmail SMTP configuration.
     * Priority: site_settings -> .env -> sensible Gmail defaults.
     */
    public function getConfig(): array
    {
        $smtpUser = (string) (
            setting('email.smtp_user')
            ?: setting('notification.sender_email')
            ?: env('email.SMTPUser')
            ?: ''
        );

        $smtpPassRaw = (string) (
            setting('email.smtp_pass')
            ?: env('email.SMTPPass')
            ?: ''
        );

        // Try decrypting if stored encrypted
        $smtpPass = $smtpPassRaw;
        if (! empty($smtpPassRaw)) {
            try {
                $decrypted = service('encrypter')->decrypt(base64_decode($smtpPassRaw, true) ?: $smtpPassRaw);
                if (! empty($decrypted)) {
                    $smtpPass = $decrypted;
                }
            } catch (\Throwable) {
                // If not encrypted or decryption fails, use as plain string
                $smtpPass = $smtpPassRaw;
            }
        }

        // Clean spaces from Google App Password (e.g. "abcd efgh ijkl mnop" -> "abcdefghijklmnop")
        $smtpPass = str_replace(' ', '', trim($smtpPass));

        $smtpHost   = (string) (setting('email.smtp_host') ?: env('email.SMTPHost') ?: 'smtp.gmail.com');
        $smtpPort   = (int) (setting('email.smtp_port') ?: env('email.SMTPPort') ?: 587);
        $smtpCrypto = (string) (setting('email.smtp_crypto') ?: env('email.SMTPCrypto') ?: 'tls');

        $fromName   = (string) (
            setting('email.from_name')
            ?: setting('notification.sender_name')
            ?: setting('general.app_name')
            ?: 'BegawiPengadaan Provinsi Lampung'
        );

        $fromEmail  = (string) (
            setting('email.from_email')
            ?: $smtpUser
            ?: 'noreply@begawipbj.lampungprov.go.id'
        );

        $enabled    = (string) (setting('notification.email_enabled', '1')) === '1';

        return [
            'smtp_host'   => $smtpHost,
            'smtp_port'   => $smtpPort,
            'smtp_crypto' => $smtpCrypto,
            'smtp_user'   => $smtpUser,
            'smtp_pass'   => $smtpPass,
            'from_name'   => $fromName,
            'from_email'  => $fromEmail,
            'enabled'     => $enabled,
        ];
    }

    /**
     * Save/update Gmail SMTP settings in site_settings.
     */
    public function saveConfig(array $data): bool
    {
        $group = 'email';
        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();

        $upsertSetting = function (string $key, ?string $value, string $description = '') use ($db, $group, $now) {
            $existing = $db->table('site_settings')
                ->where('setting_group', $group)
                ->where('setting_key', $key)
                ->get()->getRowArray();

            if ($existing) {
                $db->table('site_settings')
                    ->where('id', $existing['id'])
                    ->update([
                        'setting_value' => $value,
                        'description'   => $description ?: $existing['description'],
                        'updated_at'    => $now,
                    ]);
            } else {
                $db->table('site_settings')->insert([
                    'setting_group' => $group,
                    'setting_key'   => $key,
                    'setting_value' => $value,
                    'field_type'    => 'text',
                    'description'   => $description,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        };

        if (isset($data['smtp_user'])) {
            $upsertSetting('smtp_user', trim((string) $data['smtp_user']), 'Google Mail Account Address');
        }

        if (isset($data['smtp_pass']) && trim((string) $data['smtp_pass']) !== '') {
            $cleanedPass = str_replace(' ', '', trim((string) $data['smtp_pass']));
            try {
                $enc = base64_encode(service('encrypter')->encrypt($cleanedPass));
            } catch (\Throwable) {
                $enc = $cleanedPass;
            }
            $upsertSetting('smtp_pass', $enc, 'Google App Password 16-Char');
        }

        if (isset($data['smtp_host'])) {
            $upsertSetting('smtp_host', trim((string) $data['smtp_host']) ?: 'smtp.gmail.com', 'SMTP Host');
        }

        if (isset($data['smtp_port'])) {
            $upsertSetting('smtp_port', (string) ((int) $data['smtp_port'] ?: 587), 'SMTP Port (587 TLS / 465 SSL)');
        }

        if (isset($data['smtp_crypto'])) {
            $upsertSetting('smtp_crypto', trim((string) $data['smtp_crypto']) ?: 'tls', 'SMTP Crypto (tls / ssl)');
        }

        if (isset($data['from_name'])) {
            $upsertSetting('from_name', trim((string) $data['from_name']), 'Nama Pengirim Resmi');
        }

        if (isset($data['from_email'])) {
            $upsertSetting('from_email', trim((string) $data['from_email']), 'Alamat Email Pengirim');
        }

        // Also sync notification.sender_email and notification.sender_name for system coherence
        if (! empty($data['smtp_user'])) {
            $db->table('site_settings')
                ->where('setting_group', 'notification')
                ->where('setting_key', 'sender_email')
                ->update(['setting_value' => trim((string) $data['smtp_user']), 'updated_at' => $now]);
        }
        if (! empty($data['from_name'])) {
            $db->table('site_settings')
                ->where('setting_group', 'notification')
                ->where('setting_key', 'sender_name')
                ->update(['setting_value' => trim((string) $data['from_name']), 'updated_at' => $now]);
        }

        return true;
    }

    /**
     * Send email directly via Google Mail SMTP.
     *
     * @param string|array $to Single email or array of emails
     * @return array{success: bool, message: string, debugger: ?string}
     */
    public function sendDirect($to, string $subject, string $htmlBody, array $options = []): array
    {
        $config = $this->getConfig();

        if (empty($config['smtp_user'])) {
            return [
                'success'  => false,
                'message'  => 'Alamat akun Google Mail (smtp_user) belum dikonfigurasi di Pengaturan Notifikasi.',
                'debugger' => null,
            ];
        }

        if (empty($config['smtp_pass'])) {
            return [
                'success'  => false,
                'message'  => 'Google App Password belum diisi di Pengaturan Notifikasi.',
                'debugger' => null,
            ];
        }

        $email = service('email');
        $email->clear(true);

        $initParams = [
            'protocol'      => 'smtp',
            'SMTPHost'      => $config['smtp_host'],
            'SMTPUser'      => $config['smtp_user'],
            'SMTPPass'      => $config['smtp_pass'],
            'SMTPPort'      => (int) $config['smtp_port'],
            'SMTPCrypto'    => $config['smtp_crypto'],
            'SMTPTimeout'   => 15,
            'SMTPKeepAlive' => false,
            'mailType'      => 'html',
            'charset'       => 'UTF-8',
            'wordWrap'      => true,
            'priority'      => $options['priority'] ?? 3,
            'newline'       => "\r\n",
            'CRLF'          => "\r\n",
            'validate'      => false,
        ];

        $email->initialize($initParams);

        $fromAddress = ! empty($options['from_email']) ? $options['from_email'] : ($config['from_email'] ?: $config['smtp_user']);
        $fromName    = ! empty($options['from_name']) ? $options['from_name'] : $config['from_name'];

        $email->setFrom($fromAddress, $fromName);
        $email->setTo($to);

        if (! empty($options['cc'])) {
            $email->setCC($options['cc']);
        }
        if (! empty($options['bcc'])) {
            $email->setBCC($options['bcc']);
        }
        if (! empty($options['reply_to'])) {
            $email->setReplyTo($options['reply_to'], $options['reply_to_name'] ?? '');
        }

        $email->setSubject($subject);
        $email->setMessage($htmlBody);

        // Attachment support
        if (! empty($options['attachments']) && is_array($options['attachments'])) {
            foreach ($options['attachments'] as $att) {
                if (is_file($att)) {
                    $email->attach($att);
                }
            }
        }

        try {
            $sent = $email->send(false);
            if ($sent) {
                return [
                    'success'  => true,
                    'message'  => 'Email berhasil dikirimkan via Google Mail.',
                    'debugger' => null,
                ];
            }

            $debugger = $email->printDebugger(['headers', 'subject']);
            return [
                'success'  => false,
                'message'  => 'Gagal mengirim email via server Google Mail SMTP.',
                'debugger' => $this->cleanDebuggerString((string) $debugger),
            ];
        } catch (\Throwable $e) {
            return [
                'success'  => false,
                'message'  => 'Koneksi SMTP Error: ' . $e->getMessage(),
                'debugger' => $e->getTraceAsString(),
            ];
        }
    }

    /**
     * Send test connection email to verify Google SMTP handshake.
     */
    public function testConnection(string $targetEmail, string $adminName = 'Administrator'): array
    {
        $config = $this->getConfig();
        $html = $this->templateService->renderTestEmail($adminName, $config);
        $subject = '[UJI COBA] Koneksi Google Mail SMTP BegawiPengadaan';

        $result = $this->sendDirect($targetEmail, $subject, $html);

        try {
            $this->auditService->log(
                (int) (session()->get('user_id') ?: 1),
                'email.test_connection',
                'email_service',
                $targetEmail,
                $result['success'] ? 1 : 0,
                [
                    'target_email' => $targetEmail,
                    'smtp_host'    => $config['smtp_host'],
                    'smtp_user'    => $config['smtp_user'],
                    'result'       => $result['success'] ? 'success' : 'failed',
                    'error'        => $result['message'],
                ]
            );
        } catch (\Throwable) {
        }

        return $result;
    }

    /**
     * Send password reset email directly.
     */
    public function sendPasswordReset(string $toEmail, string $userName, string $token): array
    {
        $resetUrl = site_url('lupa-sandi/reset/' . $token);
        $html = $this->templateService->renderPasswordReset($userName, $resetUrl, 60);
        $subject = 'Atur Ulang Kata Sandi Akun - BegawiPengadaan Provinsi Lampung';

        return $this->sendDirect($toEmail, $subject, $html, [
            'priority' => 1, // Highest priority
        ]);
    }

    /**
     * Send a single personal/transactional email.
     */
    public function sendPersonal(string $toEmail, string $recipientName, string $subject, string $bodyHtml, array $options = []): array
    {
        $html = $this->templateService->renderPersonal($recipientName, $subject, $bodyHtml, $options);
        return $this->sendDirect($toEmail, $subject, $html, $options);
    }

    /**
     * Clean debug string to avoid leaking plain passwords in logs.
     */
    private function cleanDebuggerString(string $raw): string
    {
        return preg_replace('/AUTH LOGIN.*?\n/s', 'AUTH LOGIN [REDACTED]\n', $raw);
    }
}
