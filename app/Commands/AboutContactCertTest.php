<?php

namespace App\Commands;

use App\Controllers\Landing\CertificateVerificationController;
use App\Controllers\Landing\HomeController;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class AboutContactCertTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'about:contact:cert:test';
    protected $description = 'Comprehensive test suite for Tentang, Kontak, and Verifikasi Sertifikat Landing Pages';
    protected $usage       = 'about:contact:cert:test';

    private int $passed = 0;
    private int $failed = 0;

    public function run(array $params)
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPBJ TENTANG, KONTAK & VERIFIKASI SERTIFIKAT TEST SUITE   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // ==========================================
        // 1. TEST TENTANG KAMI (/tentang)
        // ==========================================
        $aboutHtml = '';
        $this->testModule("Tentang Controller Execution", function () use (&$aboutHtml) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'tentang');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));

            $aboutHtml = $controller->about();
            $this->assert(is_string($aboutHtml) && strlen($aboutHtml) > 1000, "Tentang HTML produced (> 1000 chars)");
        });

        $this->testModule("Tentang Visi, Misi & Filosofi Begawi", function () use ($aboutHtml) {
            $this->assert(str_contains($aboutHtml, 'Biro Pengadaan Barang dan Jasa') || str_contains($aboutHtml, 'Tentang Kami'), "Title or Biro reference present");
            $this->assert(str_contains($aboutHtml, 'Visi'), "Visi section present");
            $this->assert(str_contains($aboutHtml, 'Misi'), "Misi section present");
            $this->assert(str_contains($aboutHtml, 'Filosofi &amp; Makna') || str_contains($aboutHtml, 'Begawi'), "Filosofi Begawi present");
        });

        $this->testModule("Tentang 5 Pilar, PATIK & Struktur Organisasi", function () use ($aboutHtml) {
            $this->assert(str_contains($aboutHtml, '5 Pilar Ekosistem'), "5 Pilar title present");
            $this->assert(str_contains($aboutHtml, 'Arena Belajar') || str_contains($aboutHtml, 'LMS'), "Pilar 1 present");
            $this->assert(str_contains($aboutHtml, 'Bunga Rampai') || str_contains($aboutHtml, 'Pusat Pengetahuan') || str_contains($aboutHtml, 'Pengetahuan'), "Pilar 2 present");
            $this->assert(str_contains($aboutHtml, 'Regulasi'), "Pilar 3 present");
            $this->assert(str_contains($aboutHtml, 'Verifikasi'), "Pilar 4 present");
            $this->assert(str_contains($aboutHtml, 'Government Intelligence') || str_contains($aboutHtml, 'Intelligence'), "Pilar 5 present");
            $this->assert(str_contains($aboutHtml, 'Budaya Kerja') && str_contains($aboutHtml, 'P-A-T-I-K'), "Budaya PATIK present");
            $this->assert(str_contains($aboutHtml, 'Struktur Organisasi'), "Struktur Organisasi present");
            $this->assert(str_contains($aboutHtml, 'Maklumat Pelayanan UKPBJ') || str_contains($aboutHtml, 'Janji Integritas Pelayanan'), "Maklumat Pelayanan present");
        });

        // ==========================================
        // 2. TEST KONTAK (/kontak)
        // ==========================================
        $contactHtml = '';
        $this->testModule("Kontak Controller Execution", function () use (&$contactHtml) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'kontak');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new HomeController();
            $controller->initController($request, service('response'), service('logger'));

            $contactHtml = $controller->contact();
            $this->assert(is_string($contactHtml) && strlen($contactHtml) > 1000, "Kontak HTML produced (> 1000 chars)");
        });

        $this->testModule("Kontak 4 Info Cards & Lokasi Kantor", function () use ($contactHtml) {
            $this->assert(str_contains($contactHtml, 'Biro Pengadaan Barang dan Jasa') || str_contains($contactHtml, 'Alamat Kantor'), "Address card present");
            $this->assert(str_contains($contactHtml, 'pengadaan@lampungprov.go.id') || str_contains($contactHtml, 'email') || str_contains($contactHtml, 'Surel Resmi'), "Email card present");
            $this->assert(str_contains($contactHtml, 'Jam Pelayanan') || str_contains($contactHtml, '08:00'), "Working hours present");
        });

        $this->testModule("Kontak Form Konsultasi & Google Maps", function () use ($contactHtml) {
            $this->assert(str_contains($contactHtml, 'name="name"') && str_contains($contactHtml, 'name="email"'), "Form input fields present");
            $this->assert(str_contains($contactHtml, 'name="service_type"'), "Service category dropdown present");
            $this->assert(str_contains($contactHtml, 'google.com/maps/embed'), "Google Maps iframe embed present");
            $this->assert(str_contains($contactHtml, 'Pertanyaan Umum Terkait Helpdesk'), "FAQ Helpdesk present");
        });

        // ==========================================
        // 3. TEST VERIFIKASI SERTIFIKAT (/verifikasi-sertifikat)
        // ==========================================
        $certIndexHtml = '';
        $this->testModule("Verifikasi Sertifikat Index & Anti-Spam Captcha", function () use (&$certIndexHtml) {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $certIndexHtml = $controller->index();
            $this->assert(is_string($certIndexHtml) && strlen($certIndexHtml) > 1000, "Verifikasi index HTML produced (> 1000 chars)");
            $this->assert(str_contains($certIndexHtml, 'Verifikasi Keabsahan') && str_contains($certIndexHtml, 'Sertifikat Digital'), "Hero title present");
            $this->assert(str_contains($certIndexHtml, 'cert-search-card'), "Search card container present");
            $this->assert(str_contains($certIndexHtml, 'Sertifikat Resmi Diterbitkan'), "KPI Issued present");
            $this->assert(str_contains($certIndexHtml, 'Keabsahan Kriptografi SHA-256'), "KPI SHA-256 present");

            // Validasi Proteksi Anti-Spam & Honeypot
            $this->assert(str_contains($certIndexHtml, 'name="captcha_answer"'), "Math captcha answer input present");
            $this->assert(str_contains($certIndexHtml, 'name="website_url"'), "Anti-bot honeypot present");
            $this->assert(str_contains($certIndexHtml, 'cert-captcha-math') && str_contains($certIndexHtml, 'cert-captcha-box'), "High-contrast captcha widget container present");
            $this->assert((bool) preg_match('/\d+\s*\+\s*\d+\s*=\s*\?/', $certIndexHtml), "Captcha math question text rendered cleanly in HTML");
            $this->assert(session()->get('cert_verify_captcha_answer') !== null, "Math captcha answer registered in session");
        });

        $this->testModule("Verifikasi Sertifikat Privacy Hardening (No Public Directory & No Quick Samples)", function () use ($certIndexHtml) {
            // Memastikan direktori publik sertifikat dan tombol quick sample dihapus demi keamanan & privasi data
            $this->assert(! str_contains($certIndexHtml, 'Direktori Publik Sertifikat Terdaftar'), "Direktori publik removed for privacy");
            $this->assert(! str_contains($certIndexHtml, 'Coba Nomor Terbit Resmi'), "Quick sample buttons removed for privacy");
            $this->assert(! str_contains($certIndexHtml, 'begawi-pagination'), "Public directory pagination removed");
        });

        // ==========================================
        // 4. TEST VERIFIKASI HASIL (VALID & NOT FOUND & ANTI-SPAM)
        // ==========================================
        $this->testModule("Verifikasi Lookup Valid Certificate Number with Captcha", function () {
            // Gunakan nomor sertifikat riil dari DB
            $db = db_connect();
            $cert = $db->table('certificates')->where('status', 'issued')->orderBy('id', 'DESC')->get()->getRowArray();
            $this->assert($cert !== null, "At least one issued certificate found in DB");

            // Set session captcha
            $expectedCaptcha = 15;
            session()->set('cert_verify_captcha_answer', $expectedCaptcha);

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/hasil');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request->setMethod('POST');
            $request->setGlobal('post', [
                'certificate_number' => $cert['certificate_number'],
                'captcha_answer'     => (string) $expectedCaptcha,
            ]);
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $resultHtml = $controller->result();
            $this->assert(is_string($resultHtml), "Result returned string");
            $this->assert(str_contains($resultHtml, 'Hasil Otentikasi Sertifikat Digital'), "Result header present");
            $this->assert(str_contains($resultHtml, 'STATUS KEABSAHAN: VALID') || str_contains($resultHtml, 'STATUS KEABSAHAN:'), "Status ribbon present");
            $this->assert(str_contains($resultHtml, $cert['certificate_number']), "Certificate number displayed in result");
            $this->assert(str_contains($resultHtml, 'Cetak Bukti Verifikasi'), "Print action button present");
        });

        $this->testModule("Verifikasi Rejection on Invalid Captcha Answer", function () {
            session()->set('cert_verify_captcha_answer', 15);

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/hasil');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request->setMethod('POST');
            $request->setGlobal('post', [
                'certificate_number' => 'SERT/2026/00001',
                'captcha_answer'     => '999', // salah
            ]);
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $response = $controller->result();
            $this->assert($response instanceof \CodeIgniter\HTTP\RedirectResponse, "Redirected on invalid captcha");
            $flashErr = session()->getFlashdata('error');
            $this->assert(! empty($flashErr) && (str_contains($flashErr, 'Captcha') || str_contains($flashErr, 'keamanan')), "Captcha error flash message set");
        });

        $this->testModule("Verifikasi Lookup Non-Existent Certificate Number", function () {
            // Set session captcha
            $expectedCaptcha = 20;
            session()->set('cert_verify_captcha_answer', $expectedCaptcha);

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/hasil');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request->setMethod('POST');
            $request->setGlobal('post', [
                'certificate_number' => 'SERT/9999/INVALID',
                'captcha_answer'     => (string) $expectedCaptcha,
            ]);
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $errorHtml = $controller->result();
            $this->assert(str_contains($errorHtml, 'Sertifikat Tidak Ditemukan'), "Error title present");
            $this->assert(str_contains($errorHtml, 'Panduan Pemeriksaan'), "Panduan pemeriksaan present");
            $this->assert(str_contains($errorHtml, 'Hubungi Helpdesk PBJ'), "Helpdesk CTA present");
        });

        $this->testModule("Verifikasi Direct Route by Number (/verifikasi-sertifikat/{number})", function () {
            $db = db_connect();
            $cert = $db->table('certificates')->where('status', 'issued')->orderBy('id', 'DESC')->get()->getRowArray();
            $this->assert($cert !== null, "At least one issued certificate found in DB");

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/' . $cert['certificate_number']);
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $detailHtml = $controller->detail($cert['certificate_number']);
            $this->assert(str_contains($detailHtml, 'Hasil Otentikasi Sertifikat Digital'), "Direct detail result title present");
            $this->assert(str_contains($detailHtml, $cert['certificate_number']), "Direct detail displays certificate number");
        });

        // ==========================================
        // 5. TEST NAVIGATION SUB-MENU SYNCHRONIZATION
        // ==========================================
        $this->testModule("Navigation Header Synchronization", function () {
            $headerFile = APPPATH . 'Views/landing/components/header.php';
            $content = file_get_contents($headerFile);

            $this->assert(str_contains($content, 'site_url(\'tentang\')'), "Tentang menu link present");
            $this->assert(str_contains($content, 'Visi &amp; Misi 2024–2029') || str_contains($content, 'visi-misi'), "Tentang Visi Misi sub-menu present");
            $this->assert(str_contains($content, '5 Pilar Ekosistem BegawiPengadaan') || str_contains($content, 'pilar'), "Tentang 5 Pilar sub-menu present");
            $this->assert(str_contains($content, 'site_url(\'verifikasi-sertifikat\')'), "Verifikasi Sertifikat link in header present");
            $this->assert(str_contains($content, 'site_url(\'kontak\')'), "Kontak link in header present");
        });

        // Final Score
        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(sprintf("   TEST SUMMARY: %d PASSED, %d FAILED", $this->passed, $this->failed), $this->failed === 0 ? "green" : "red");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        return $this->failed === 0 ? 0 : 1;
    }

    private function testModule(string $name, callable $callback): void
    {
        try {
            $callback();
            CLI::write(" [PASS] " . $name, "green");
            $this->passed++;
        } catch (\Throwable $e) {
            CLI::write(" [FAIL] " . $name . " -> " . $e->getMessage(), "red");
            $this->failed++;
        }
    }

    private function assert(bool $condition, string $message): void
    {
        if (! $condition) {
            throw new \RuntimeException($message);
        }
    }
}
