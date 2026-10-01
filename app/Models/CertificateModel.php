<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateModel extends Model
{
    protected $table         = 'certificates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['enrollment_id', 'certificate_number', 'issued_date', 'status', 'file_path', 'qr_token'];

    public function findByEnrollment(int $enrollmentId): ?array
    {
        return $this->where('enrollment_id', $enrollmentId)->first();
    }

    private function detailQuery()
    {
        return $this->select('certificates.*, users.id as user_id, users.username, courses.title as course_title')
            ->join('enrollments', 'enrollments.id = certificates.enrollment_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->join('classes', 'classes.id = enrollments.class_id')
            ->join('courses', 'courses.id = classes.course_id');
    }

    public function withDetail(int $id): ?array
    {
        return $this->detailQuery()->where('certificates.id', $id)->first();
    }

    public function withDetailByToken(string $token): ?array
    {
        return $this->detailQuery()->where('certificates.qr_token', $token)->first();
    }

    public function myCertificates(int $userId): array
    {
        return $this->detailQuery()->where('enrollments.user_id', $userId)->findAll();
    }

    /**
     * Semua sertifikat (issued + revoked), untuk halaman "Kelola Sertifikat"
     * Administrator/Evaluator — beda dari myCertificates() yang di-scope per user.
     */
    public function allWithDetail(): array
    {
        return $this->detailQuery()->orderBy('certificates.created_at', 'DESC')->findAll();
    }

    public function countIssuedThisYear(string $year): int
    {
        return $this->where('YEAR(issued_date)', $year)->countAllResults();
    }
}
