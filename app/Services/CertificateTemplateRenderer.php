<?php

namespace App\Services;

use CodeIgniter\Database\ConnectionInterface;
use RuntimeException;

/**
 * CertificateTemplateRenderer — render HTML sertifikat.
 *
 * Placeholder yang didukung:
 *   {{participant_name}}
 *   {{certificate_number}}
 *   {{certificate_title}}
 *   {{issued_date}}
 *   {{issuer_name}}
 *   {{verification_url}}
 *
 * Sumber template: certificate_templates.template_payload (JSON)
 *
 * Bentuk payload JSON:
 *   {
 *     "body_html":    "<h1>...</h1>..."   // opsional; jika kosong → default layout
 *     "css":          "string css"        // opsional; tambahan styling
 *     "issuer_name":  "BegawiPBJ"         // opsional override
 *     "qr_position":  "right"|"bottom"    // opsional; default "right"
 *   }
 *
 * Sumber data (parameter $certificateData):
 *   participant_name, certificate_number, certificate_title, issued_date,
 *   issuer_name, verification_url, source_label, ...
 */
class CertificateTemplateRenderer
{
    private const PLACEHOLDERS = [
        '{{participant_name}}',
        '{{certificate_number}}',
        '{{certificate_title}}',
        '{{issued_date}}',
        '{{issuer_name}}',
        '{{verification_url}}',
        '{{source_label}}',
    ];

    /** @var array<string, string> */
    private array $placeholders = [];

    public function __construct()
    {
        // tidak butuh DB — service murni rendering.
    }

    /**
     * Render HTML sertifikat.
     *
     * @param array<string, mixed> $certificateData
     * @param array<string, mixed> $template  row certificate_templates
     */
    public function render(array $certificateData, array $template): string
    {
        $this->placeholders = $this->buildPlaceholderMap($certificateData, $template);

        $payload   = $this->decodePayload($template['template_payload'] ?? null);
        $bodyHtml  = isset($payload['body_html']) && is_string($payload['body_html']) && trim($payload['body_html']) !== ''
            ? $payload['body_html']
            : null;

        $css = '';
        if (isset($payload['css']) && is_string($payload['css'])) {
            $css = '<style>' . $payload['css'] . '</style>';
        } else {
            $css = $this->defaultCss();
        }

        // Background image — via certificate_templates.background_file_id → file_assets.path
        $bgStyle = $this->resolveBackgroundStyle($template);

        $body = $bodyHtml ?? $this->defaultBody();

        $body    = $this->applyPlaceholders($body);
        $qrBlock = $this->renderQrBlock($payload, $certificateData);

        $title = htmlspecialchars((string) ($certificateData['certificate_title'] ?? 'Sertifikat'), ENT_QUOTES, 'UTF-8');
        $headerBar = '<div class="cert-header"><span class="cert-app">BegawiPBJ</span><span class="cert-type">' . $title . '</span></div>';

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

    // ===============================================================
    // Placeholders
    // ===============================================================

    /**
     * @param array<string, mixed> $certificateData
     * @param array<string, mixed> $template
     * @return array<string, string>
     */
    private function buildPlaceholderMap(array $certificateData, array $template): array
    {
        $issuedAtRaw = $certificateData['issued_at'] ?? null;
        $issuedDate  = $issuedAtRaw ? date('d F Y', strtotime((string) $issuedAtRaw)) : date('d F Y');

        return [
            '{{participant_name}}'    => htmlspecialchars((string) ($certificateData['participant_name']    ?? '—'), ENT_QUOTES, 'UTF-8'),
            '{{certificate_number}}'  => htmlspecialchars((string) ($certificateData['certificate_number']  ?? '—'), ENT_QUOTES, 'UTF-8'),
            '{{certificate_title}}'   => htmlspecialchars((string) ($certificateData['certificate_title']   ?? '—'), ENT_QUOTES, 'UTF-8'),
            '{{issued_date}}'         => $issuedDate,
            '{{issuer_name}}'         => htmlspecialchars((string) ($certificateData['issuer_name']         ?? 'BegawiPBJ — Pemerintah Provinsi Lampung'), ENT_QUOTES, 'UTF-8'),
            '{{verification_url}}'    => htmlspecialchars((string) ($certificateData['verification_url']    ?? ''), ENT_QUOTES, 'UTF-8'),
            '{{source_label}}'        => htmlspecialchars((string) ($certificateData['source_label']        ?? '—'), ENT_QUOTES, 'UTF-8'),
        ];
    }

    private function applyPlaceholders(string $html): string
    {
        return strtr($html, $this->placeholders);
    }

    // ===============================================================
    // Default layout (tanpa body_html)
    // ===============================================================

    private function defaultCss(): string
    {
        return <<<CSS
<style>
  @page { margin: 0; }
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; }
  body {
    font-family: Helvetica, Arial, sans-serif;
    color: #1f2937;
    background: #fff;
    padding: 36px 48px;
    min-height: 100vh;
  }
  .cert-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 4px double #14532d;
    padding-bottom: 12px;
    margin-bottom: 24px;
  }
  .cert-app { font-size: 18px; font-weight: bold; letter-spacing: 2px; color: #14532d; }
  .cert-type { font-size: 11px; color: #6b7280; letter-spacing: 2px; text-transform: uppercase; }
  .cert h1 {
    text-align: center; font-size: 36px; margin: 12px 0 8px;
    letter-spacing: 6px; color: #14532d;
  }
  .cert-intro {
    text-align: center; font-size: 12px; color: #4b5563;
    margin: 0 0 28px;
  }
  .cert-participant-name {
    text-align: center; font-size: 30px; font-weight: bold;
    margin: 8px 0 4px; color: #0f172a;
  }
  .cert-event-title {
    text-align: center; font-size: 16px; margin: 0 0 32px; color: #1f2937;
  }
  .cert-event-title strong { font-size: 17px; color: #14532d; }
  .cert-meta {
    width: 100%; display: table; border-spacing: 8px;
  }
  .cert-meta-cell { display: table-cell; vertical-align: top; }
  .cert-meta-cell.label { width: 65%; padding-right: 24px; }
  .cert-meta-cell.qr   { width: 35%; text-align: right; }
  .cert-meta-label { font-size: 10px; color: #6b7280; margin: 0; letter-spacing: 1px; text-transform: uppercase; }
  .cert-meta-value { font-size: 13px; color: #1f2937; margin: 0 0 14px; }
  .cert-meta-value.cert-number { font-size: 15px; font-weight: bold; color: #14532d; letter-spacing: 2px; }
  .cert-verify { font-size: 10px; color: #6b7280; text-align: center; margin-top: 16px; }
  .cert-verify a { color: #14532d; text-decoration: none; word-break: break-all; }
  .cert-footer {
    position: fixed; bottom: 16px; left: 0; right: 0;
    text-align: center; font-size: 9px; color: #9ca3af;
  }
</style>
CSS;
    }

    private function defaultBody(): string
    {
        return <<<HTML
<div class="cert">
  <h1>SERTIFIKAT</h1>
  <p class="cert-intro">Diberikan kepada:</p>
  <p class="cert-participant-name">{{participant_name}}</p>
  <p class="cert-event-title">atas partisipasinya pada:<br><strong>{{certificate_title}}</strong></p>

  <div class="cert-meta">
    <div class="cert-meta-cell label">
      <p class="cert-meta-label">Nomor Sertifikat</p>
      <p class="cert-meta-value cert-number">{{certificate_number}}</p>
      <p class="cert-meta-label">Tanggal Terbit</p>
      <p class="cert-meta-value">{{issued_date}}</p>
      <p class="cert-meta-label">Penerbit</p>
      <p class="cert-meta-value">{{issuer_name}}</p>
    </div>
    <div class="cert-meta-cell qr">
      __QR_BLOCK__
      <p class="cert-verify">Scan QR atau buka:<br><a href="{{verification_url}}">{{verification_url}}</a></p>
    </div>
  </div>
</div>
HTML;
    }

    // ===============================================================
    // QR block — QR akan di-inject oleh CertificateGenerator setelah
    // builder ini return HTML. Di sini kita sisipkan placeholder yang
    // akan dicari oleh generator.
    // ===============================================================

    /**
     * Tanda yang akan diganti CertificateGenerator dengan <img src="data:..">.
     * Disimpan dalam JSON payload 'qr_position' untuk variasi layout.
     *
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $certificateData
     */
    private function renderQrBlock(array $payload, array $certificateData): string
    {
        $position = (string) ($payload['qr_position'] ?? 'right');
        // QR di-inject oleh CertificateGenerator; di sini kita letakkan placeholder.
        $img = '<!--CERTIFICATE_QR_PLACEHOLDER-->';

        if ($position === 'bottom') {
            return '<div style="text-align:center;margin-top:32px;">' . $img . '</div>'
                . '<p class="cert-verify" style="margin-top:8px;">Verifikasi publik: <a href="{{verification_url}}">{{verification_url}}</a></p>';
        }
        // default: 'right' / inline — placeholder disimpan dalam body via __QR_BLOCK__
        // Body akan dimodifikasi lewat str_replace setelah ini dipanggil.
        return '';
    }

    // ===============================================================
    // Background dari certificate_templates.background_file_id
    // ===============================================================

    /**
     * Resolve background image menjadi CSS string. URL file diambil
     * dari file_assets.path — disk=local, lokasi writable/uploads/...
     */
    private function resolveBackgroundStyle(array $template): string
    {
        $bgFileId = isset($template['background_file_id']) ? (int) $template['background_file_id'] : 0;
        if ($bgFileId <= 0) {
            return '';
        }

        try {
            $db = db_connect();
            $row = $db->table('file_assets')
                ->select(['disk', 'path', 'lifecycle_status', 'visibility'])
                ->where('id', $bgFileId)
                ->get()
                ->getRowArray();
        } catch (\Throwable $e) {
            return '';
        }

        if (! $row) {
            return '';
        }
        if (($row['lifecycle_status'] ?? '') !== 'available') {
            return '';
        }

        // Untuk Dompdf kita pakai absolute filesystem path jika possible.
        $relative = ltrim((string) $row['path'], '/');
        $abs      = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, WRITEPATH . $relative);
        if (! is_file($abs)) {
            return '';
        }

        $url = 'file:///' . str_replace('\\', '/', $abs);
        return '<style>body{background-image:url("' . $url . '");background-size:cover;background-repeat:no-repeat;}</style>';
    }

    // ===============================================================
    // Payload parsing
    // ===============================================================

    /**
     * @return array<string, mixed>
     */
    private function decodePayload($raw): array
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
