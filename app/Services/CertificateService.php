<?php

namespace App\Services;

use App\Models\ApprovalModel;
use App\Models\CertificateModel;
use App\Models\EnrollmentModel;
use App\Libraries\PdfGenerator;
use App\Libraries\QRGenerator;
use RuntimeException;
use Throwable;

class CertificateService
{
    private ApprovalModel $approvalModel;
    private CertificateModel $certificateModel;
    private EnrollmentModel $enrollmentModel;
    private ApprovalService $approvalService;
    private AuditService $auditService;

    public function __construct()
    {
        $this->approvalModel    = new ApprovalModel();
        $this->certificateModel = new CertificateModel();
        $this->enrollmentModel  = new EnrollmentModel();
        $this->approvalService  = new ApprovalService();
        $this->auditService     = new AuditService();
    }

    public function requestIfEligible(int $enrollmentId): void
    {
        if ($this->certificateModel->findByEnrollment($enrollmentId)) {
            return;
        }

        if ($this->approvalModel->hasActiveApproval('certificate', $enrollmentId)) {
            return;
        }

        $enrollment = $this->enrollmentModel->find($enrollmentId);

        if (! $enrollment || $enrollment['final_status'] !== 'passed') {
            return;
        }

        $this->approvalService->createApproval('certificate', $enrollmentId, (int) $enrollment['user_id']);
    }

    public function approve(int $enrollmentId, int $actedBy, ?string $notes = null): void
    {
        $enrollment = $this->enrollmentModel->find($enrollmentId);

        if (! $enrollment || $enrollment['final_status'] !== 'passed') {
            throw new RuntimeException('Peserta belum memenuhi syarat penerbitan sertifikat.');
        }

        if ($this->certificateModel->findByEnrollment($enrollmentId)) {
            throw new RuntimeException('Sertifikat untuk enrollment ini sudah diterbitkan.');
        }

        $this->approvalService->approve('certificate', $enrollmentId, $actedBy, $notes, function (int $enrollmentId) use ($actedBy) {
            $this->generate($enrollmentId, $actedBy);
        });
    }

    public function reject(int $enrollmentId, int $actedBy, ?string $notes = null): void
    {
        $this->approvalService->reject('certificate', $enrollmentId, $actedBy, $notes, function () {
            // enrollment tetap 'passed', bisa diajukan ulang manual bila perlu
        });
    }

    private function generate(int $enrollmentId, int $issuedBy): void
    {
        $year   = (int) date('Y');
        $number = $this->reserveCertificateNumber($year);
        $certificateNumber = sprintf('SERT/%s/%05d', $year, $number);
        $qrToken = bin2hex(random_bytes(16));
        $uploadPath = WRITEPATH . 'certificates';

        if (! is_dir($uploadPath) && ! mkdir($uploadPath, 0750, true)) {
            throw new RuntimeException('Direktori sertifikat tidak tersedia.');
        }

        if (! is_writable($uploadPath)) {
            throw new RuntimeException('Direktori sertifikat tidak dapat ditulis.');
        }

        $certificateId = $this->certificateModel->insert([
            'enrollment_id'      => $enrollmentId,
            'certificate_number' => $certificateNumber,
            'issued_date'        => date('Y-m-d'),
            'status'             => 'issued',
            'qr_token'           => $qrToken,
        ], true);

        if (! $certificateId) {
            $dbError = db_connect()->error();
            $detail = $dbError['message'] ?? 'kesalahan database tidak diketahui';

            throw new RuntimeException('Sertifikat gagal dibuat: ' . $detail);
        }

        $fileName = 'certificate-' . $certificateId . '.pdf';
        $fullPath = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

        try {
            $certificate = $this->certificateModel->withDetail($certificateId);

            if (! $certificate) {
                throw new RuntimeException('Data sertifikat tidak ditemukan setelah dibuat.');
            }

            $verifyUrl = site_url('sertifikat/verifikasi/' . $qrToken);
            $qrDataUri = (new QRGenerator())->generateDataUri($verifyUrl);

            $html = view('certificate/pdf_template', [
                'certificate' => $certificate,
                'qrDataUri'   => $qrDataUri,
            ]);

            (new PdfGenerator())->generateToFile($html, $fullPath);

            if (! is_file($fullPath)) {
                throw new RuntimeException('File sertifikat gagal dibuat.');
            }

            if (! $this->certificateModel->update($certificateId, ['file_path' => 'certificates/' . $fileName])) {
                throw new RuntimeException('Lokasi file sertifikat gagal disimpan.');
            }

            $this->auditService->log($issuedBy, 'generate_certificate', 'certificate', 'certificates', $certificateId, [
                'certificate_number' => $certificateNumber,
            ]);
        } catch (Throwable $e) {
            if (is_file($fullPath)) {
                unlink($fullPath);
            }

            $this->certificateModel->delete($certificateId);

            throw $e;
        }
    }

    private function reserveCertificateNumber(int $year): int
    {
        $db = db_connect();
        $latest = $db->query(
            "SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(certificate_number, '/', -1) AS UNSIGNED)), 0) AS last_number
             FROM certificates
             WHERE certificate_number LIKE ?",
            ['SERT/' . $year . '/%']
        )->getRowArray();
        $nextNumber = ((int) ($latest['last_number'] ?? 0)) + 1;

        $db->query(
            'INSERT INTO certificate_sequences (year, last_number)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE last_number = GREATEST(last_number + 1, VALUES(last_number))',
            [$year, $nextNumber]
        );

        $row = $db->query(
            'SELECT last_number FROM certificate_sequences WHERE year = ?',
            [$year]
        )->getRowArray();

        if (! $row) {
            throw new RuntimeException('Nomor sertifikat gagal dibuat.');
        }

        return (int) $row['last_number'];
    }

    public function revoke(int $certificateId, int $actedBy, string $reason): void
    {
        $certificate = $this->certificateModel->find($certificateId);

        if (! $certificate) {
            throw new RuntimeException('Sertifikat tidak ditemukan.');
        }

        if ($certificate['status'] !== 'issued') {
            throw new RuntimeException('Sertifikat sudah tidak aktif.');
        }

        $this->certificateModel->update($certificateId, ['status' => 'revoked']);
        $this->auditService->log($actedBy, 'revoke_certificate', 'certificate', 'certificates', $certificateId, ['reason' => $reason]);
    }
}
