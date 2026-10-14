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

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("   BEGAWIPBJ TENTANG, KONTAK & VERIFIKASI SERTIFIKAT TEST SUITE   ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // ==========================================
        // 1. TEST TENTANG (/tentang)
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
            $this->assert(str_contains($aboutHtml, 'Membangun Ekosistem Pengadaan Digital'), "Hero title present");
            $this->assert(str_contains($aboutHtml, 'Makna') && str_contains($aboutHtml, 'Begawi'), "Filosofi Begawi present");
            $this->assert(str_contains($aboutHtml, 'Visi &amp; Misi') || str_contains($aboutHtml, 'Visi & Misi'), "Visi Misi title present");
            $this->assert(str_contains($aboutHtml, '5 Misi Strategis'), "5 Misi Strategis present");
        });

        $this->testModule("Tentang 5 Pilar, PATIK & Struktur Organisasi", function () use ($aboutHtml) {
            $this->assert(str_contains($aboutHtml, '5 Pilar Ekosistem'), "5 Pilar title present");
            $this->assert(str_contains($aboutHtml, 'Arena Belajar LMS'), "Pilar 1 present");
            $this->assert(str_contains($aboutHtml, 'Bunga Rampai PBJ'), "Pilar 2 present");
            $this->assert(str_contains($aboutHtml, 'Pusat Regulasi PBJ'), "Pilar 3 present");
            $this->assert(str_contains($aboutHtml, 'Verifikasi Sertifikat Digital'), "Pilar 4 present");
            $this->assert(str_contains($aboutHtml, 'Government Intelligence PBJ'), "Pilar 5 present");
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
            $this->assert(str_contains($contactHtml, 'Hubungi Biro PBJ') && str_contains($contactHtml, 'Helpdesk LPSE'), "Hero title present");
            $this->assert(str_contains($contactHtml, 'Lokasi Kantor'), "Info Card Lokasi present");
            $this->assert(str_contains($contactHtml, 'WhatsApp Helpdesk'), "Info Card WhatsApp present");
            $this->assert(str_contains($contactHtml, 'Surat Elektronik'), "Info Card Email present");
            $this->assert(str_contains($contactHtml, 'Jam Pelayanan'), "Info Card Jam Pelayanan present");
            $this->assert(str_contains($contactHtml, 'Wolter Monginsidi'), "Address text present");
        });

        $this->testModule("Kontak Form Konsultasi & Google Maps", function () use ($contactHtml) {
            $this->assert(str_contains($contactHtml, 'Formulir Layanan &amp; Konsultasi') || str_contains($contactHtml, 'Formulir Layanan & Konsultasi'), "Form heading present");
            $this->assert(str_contains($contactHtml, 'id="form-pesan"'), "Form container id present");
            $this->assert(str_contains($contactHtml, 'name="name"') && str_contains($contactHtml, 'name="email"'), "Form input fields present");
            $this->assert(str_contains($contactHtml, 'name="service_type"'), "Service category dropdown present");
            $this->assert(str_contains($contactHtml, 'google.com/maps/embed'), "Google Maps iframe embed present");
            $this->assert(str_contains($contactHtml, 'Pertanyaan Umum Terkait Helpdesk'), "FAQ Helpdesk present");
        });

        // ==========================================
        // 3. TEST VERIFIKASI SERTIFIKAT (/verifikasi-sertifikat)
        // ==========================================
        $certIndexHtml = '';
        $this->testModule("Verifikasi Sertifikat Index & Direktori", function () use (&$certIndexHtml) {
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
            $this->assert(str_contains($certIndexHtml, 'Direktori Publik Sertifikat Terdaftar'), "Direktori title present");
            $this->assert(str_contains($certIndexHtml, 'cert-card'), "Certificate card elements present");
        });

        $this->testModule("Verifikasi Sertifikat Pagination & Quick Samples", function () use ($certIndexHtml) {
            $this->assert(str_contains($certIndexHtml, 'Coba Nomor Terbit Resmi') || str_contains($certIndexHtml, 'btn-sample-fill'), "Quick sample buttons present");
            $this->assert(str_contains($certIndexHtml, 'begawi-pagination') || str_contains($certIndexHtml, 'Menampilkan'), "Pagination container or count present");
        });

        // ==========================================
        // 4. TEST VERIFIKASI HASIL (VALID & NOT FOUND)
        // ==========================================
        $this->testModule("Verifikasi Lookup Valid Certificate Number", function () {
            // Gunakan nomor sertifikat riil dari DB
            $db = db_connect();
            $cert = $db->table('certificates')->where('status', 'issued')->orderBy('id', 'DESC')->get()->getRowArray();
            $this->assert($cert !== null, "At least one issued certificate found in DB");

            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/hasil');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request->setMethod('POST');
            $request->setGlobal('post', ['certificate_number' => $cert['certificate_number']]);
            \Config\Services::injectMock('request', $request);

            $controller = new CertificateVerificationController();
            $controller->initController($request, service('response'), service('logger'));

            $resultHtml = $controller->result();
            $this->assert(str_contains($resultHtml, 'Hasil Otentikasi Sertifikat Digital'), "Result header present");
            $this->assert(str_contains($resultHtml, 'STATUS KEABSAHAN: VALID') || str_contains($resultHtml, 'STATUS KEABSAHAN:'), "Status ribbon present");
            $this->assert(str_contains($resultHtml, $cert['certificate_number']), "Certificate number displayed in result");
            $this->assert(str_contains($resultHtml, 'Cetak Bukti Verifikasi'), "Print action button present");
        });

        $this->testModule("Verifikasi Lookup Non-Existent Certificate Number", function () {
            $siteUri = new \CodeIgniter\HTTP\SiteURI(config('App'), 'verifikasi-sertifikat/hasil');
            $request = new \CodeIgniter\HTTP\IncomingRequest(config('App'), $siteUri, null, new \CodeIgniter\HTTP\UserAgent());
            $request->setMethod('POST');
            $request->setGlobal('post', ['certificate_number' => 'SERT/9999/INVALID']);
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
            $this->assert(str_contains($content, '5 Pilar Ekosistem BegawiPBJ') || str_contains($content, 'pilar'), "Tentang 5 Pilar sub-menu present");
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
