<?php

namespace App\Services;

class ContentMediaService
{
    /**
     * Memproses HTML konten untuk mengekstrak seluruh inline Base64 images
     * dan menyimpannya sebagai file gambar fisik di folder public/uploads/content/{subfolder}/.
     * Mengganti atribut src="data:image/...;base64,..." dengan URL fisik bersih.
     *
     * Hal ini mencegah error MySQL "Got a packet bigger than 'max_allowed_packet' bytes"
     * ketika pengguna menempelkan (paste) gambar/screenshot berukuran megabytes ke textarea.
     *
     * @param string $html Konten HTML yang akan diproses
     * @param string $subfolder Subdirektori penyimpanan (mis. 'articles', 'news', 'lessons')
     * @return string Konten HTML bersih dengan tautan gambar fisik
     */
    public static function processInlineImages(string $html, string $subfolder = 'general'): string
    {
        if (! str_contains($html, 'data:image/')) {
            return $html;
        }

        $subfolder = preg_replace('/[^a-zA-Z0-9_-]/', '', $subfolder) ?: 'general';
        $targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . $subfolder;

        if (! is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }

        // Pattern untuk mendeteksi data URI gambar base64 di tag <img>
        $pattern = '/<img([^>]*?)src=["\'](data:image\/(png|jpeg|jpg|webp|gif|svg\+xml);base64,([A-Za-z0-9+\/=\s]+))["\']([^>]*?)>/i';

        return preg_replace_callback($pattern, static function ($matches) use ($targetDir, $subfolder) {
            $prefixAttr = $matches[1];
            $rawMime    = strtolower($matches[3]);
            $base64Data = preg_replace('/\s+/', '', $matches[4]);
            $suffixAttr = $matches[5];

            // Tentukan ekstensi file yang valid
            $ext = match ($rawMime) {
                'png'                  => 'png',
                'jpeg', 'jpg'          => 'jpg',
                'webp'                 => 'webp',
                'gif'                  => 'gif',
                'svg+xml'              => 'svg',
                default                => 'png',
            };

            $binaryData = base64_decode($base64Data, true);
            if ($binaryData === false || strlen($binaryData) < 10) {
                // Jika corrupt atau bukan gambar valid, kembalikan tag asli
                return $matches[0];
            }

            // Nama file unik
            $filename = 'inline_' . date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
            $filePath = $targetDir . DIRECTORY_SEPARATOR . $filename;

            // Simpan binary gambar ke disk
            if (@file_put_contents($filePath, $binaryData) === false) {
                log_message('error', "[ContentMediaService] Gagal menyimpan inline image ke: {$filePath}");
                return $matches[0];
            }

            @chmod($filePath, 0664);

            $publicUrl = function_exists('base_url')
                ? base_url('uploads/content/' . $subfolder . '/' . $filename)
                : ('/uploads/content/' . $subfolder . '/' . $filename);

            // Daftarkan ke tabel file_assets jika ada
            try {
                if (function_exists('db_connect')) {
                    $db = db_connect();
                    if ($db->tableExists('file_assets')) {
                        $userId = function_exists('session') ? session()->get('user_id') : null;
                        $now    = date('Y-m-d H:i:s');
                        $db->table('file_assets')->insert([
                            'disk'             => 'local',
                            'path'             => 'uploads/content/' . $subfolder . '/' . $filename,
                            'original_name'    => $filename,
                            'stored_name'      => $filename,
                            'extension'        => $ext,
                            'mime_type'        => 'image/' . ($ext === 'jpg' ? 'jpeg' : $ext),
                            'size_bytes'       => strlen($binaryData),
                            'visibility'       => 'public',
                            'lifecycle_status' => 'available',
                            'resource_type'    => 'content_inline_image',
                            'resource_id'      => null,
                            'uploaded_by'      => $userId ? (int) $userId : null,
                            'available_at'     => $now,
                            'created_at'       => $now,
                            'updated_at'       => $now,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                log_message('warning', '[ContentMediaService] file_assets registration warning: ' . $e->getMessage());
            }

            return "<img{$prefixAttr}src=\"{$publicUrl}\"{$suffixAttr}>";
        }, $html);
    }
}
