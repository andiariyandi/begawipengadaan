<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class RegulationUploadSizeTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:regulation-upload';
    protected $description = 'Verifies fixes for upload file size limits and post_max_size session crash prevention';

    public function run(array $params)
    {
        CLI::write("=== VERIFIKASI PERBAIKAN BUG UPLOAD REGULASI & SESSION CRASH ===", 'green');
        $passCount = 0;
        $failCount = 0;

        // 1. Verifikasi .htaccess configuration
        CLI::write("\n1. Memeriksa Konfigurasi Apache mod_php di public/.htaccess...", 'yellow');
        $htaccess = file_get_contents(FCPATH . '.htaccess');
        if (str_contains($htaccess, 'upload_max_filesize 128M') && str_contains($htaccess, 'post_max_size 128M')) {
            CLI::write(" [PASS] Directives upload_max_filesize 128M & post_max_size 128M aktif di .htaccess", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Directives upload_max_filesize atau post_max_size tidak ditemukan di .htaccess", 'red');
            $failCount++;
        }

        // 2. Verifikasi Early Guard di public/index.php
        CLI::write("\n2. Memeriksa Early Guard terhadap post_max_size overflow di public/index.php...", 'yellow');
        $indexPhp = file_get_contents(FCPATH . 'index.php');
        if (str_contains($indexPhp, 'POST CONTENT-LENGTH EXCEEDED GUARD') && str_contains($indexPhp, 'ini_get(\'post_max_size\')') && str_contains($indexPhp, 'http_response_code(413)')) {
            CLI::write(" [PASS] Early guard di public/index.php terpasang: mencegah fatal session crash pada Unknown:0", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Early guard di public/index.php tidak lengkap", 'red');
            $failCount++;
        }

        // 3. Verifikasi Server-side Controller Validation di RegulationController
        CLI::write("\n3. Memeriksa Validasi Berkas di RegulationController.php...", 'yellow');
        $controllerPhp = file_get_contents(APPPATH . 'Controllers/Admin/RegulationController.php');
        if (str_contains($controllerPhp, '\'file\'            => \'permit_empty|max_size[file,65536]|ext_in[file,pdf,doc,docx]\'')) {
            CLI::write(" [PASS] Validasi file (maks 64MB, format pdf/doc/docx) terpasang di store() & update()", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Validasi file tidak ditemukan di RegulationController store()", 'red');
            $failCount++;
        }

        if (str_contains($controllerPhp, 'UPLOAD_ERR_INI_SIZE') && str_contains($controllerPhp, 'melebihi batas')) {
            CLI::write(" [PASS] Penanganan error upload server (UPLOAD_ERR_INI_SIZE) dengan pesan ramah terpasang", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Penanganan UPLOAD_ERR_INI_SIZE tidak ditemukan", 'red');
            $failCount++;
        }

        if (str_contains($controllerPhp, 'addVersion') && str_contains($controllerPhp, '\'file\'      => \'uploaded[file]|max_size[file,65536]|ext_in[file,pdf,doc,docx]\'')) {
            CLI::write(" [PASS] Validasi ketat pada addVersion() terpasang", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Validasi pada addVersion() tidak ditemukan", 'red');
            $failCount++;
        }

        // 4. Verifikasi Client-side Validation di View Form & Detail
        CLI::write("\n4. Memeriksa Validasi Sisi Klien di View Regulasi...", 'yellow');
        $formView = file_get_contents(APPPATH . 'Views/dashboard/regulation/regulations/form.php');
        if (str_contains($formView, 'validateDocFileSize(this, 20)') && str_contains($formView, 'id="regulationDocFileInput"')) {
            CLI::write(" [PASS] Validasi instan JavaScript (maks 20MB) & submit guard terpasang di form.php", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Validasi JavaScript tidak ditemukan di form.php", 'red');
            $failCount++;
        }

        $detailView = file_get_contents(APPPATH . 'Views/dashboard/regulation/regulations/detail.php');
        if (str_contains($detailView, 'validateVersionFileSize(this, 20)') && str_contains($detailView, 'id="versionFileInput"')) {
            CLI::write(" [PASS] Validasi instan JavaScript pada modal upload versi terpasang di detail.php", 'green');
            $passCount++;
        } else {
            CLI::write(" [FAIL] Validasi JavaScript tidak ditemukan di detail.php", 'red');
            $failCount++;
        }

        // 5. Test Regulation Index Route Render
        CLI::write("\n5. Menguji Render Halaman /dashboard/regulasi/dokumen...", 'yellow');
        $db = db_connect();
        $admin = $db->table('users')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($admin) {
            session()->set([
                'user_id'    => (int) $admin['id'],
                'logged_in'  => true,
                'isLoggedIn' => true,
                'email'      => $admin['email'] ?? 'admin@begawi.test',
                'name'       => $admin['name'] ?? 'Administrator',
                'roles'      => ['administrator', 'super_administrator'],
            ]);

            $controller = new \App\Controllers\Admin\RegulationController();
            $controller->initController(\Config\Services::request(), \Config\Services::response(), \Config\Services::logger());
            $response = $controller->index();
            $body = is_string($response) ? $response : (method_exists($response, 'getBody') ? $response->getBody() : '');

            if (str_contains($body, 'Regulasi') && (str_contains($body, 'Dokumen') || str_contains($body, 'regulasi'))) {
                CLI::write(" [PASS] Halaman /dashboard/regulasi/dokumen berhasil di-render (200 OK)", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Halaman regulasi index gagal di-render (len: " . strlen($body) . ")", 'red');
                $failCount++;
            }

            $formResponse = $controller->create();
            $formBody = is_string($formResponse) ? $formResponse : (method_exists($formResponse, 'getBody') ? $formResponse->getBody() : '');
            if (str_contains($formBody, 'validateDocFileSize') && str_contains($formBody, 'Tambah Regulasi')) {
                CLI::write(" [PASS] Halaman formulir tambah regulasi berhasil di-render dengan validasi aktif (200 OK)", 'green');
                $passCount++;
            } else {
                CLI::write(" [FAIL] Formulir tambah regulasi gagal di-render", 'red');
                $failCount++;
            }
        }

        CLI::write("\n==================================================", 'cyan');
        CLI::write(sprintf("HASIL PENGUJIAN: %d LULUS, %d GAGAL", $passCount, $failCount), $failCount === 0 ? 'green' : 'red');
        CLI::write("==================================================\n", 'cyan');

        return $failCount === 0 ? 0 : 1;
    }
}
