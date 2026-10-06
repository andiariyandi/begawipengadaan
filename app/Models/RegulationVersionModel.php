<?php

namespace App\Models;

use CodeIgniter\Model;

class RegulationVersionModel extends Model
{
    protected $table         = 'regulation_versions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['regulation_id', 'version_number', 'file_path', 'change_notes', 'uploaded_by', 'created_at'];

    public function historyFor(int $regulationId): array
    {
        return $this->select('regulation_versions.*, users.username AS uploader_name')
            ->join('users', 'users.id = regulation_versions.uploaded_by')
            ->where('regulation_id', $regulationId)
            ->orderBy('version_number', 'DESC')
            ->findAll();
    }

    public function latestVersionNumber(int $regulationId): int
    {
        $row = $this->where('regulation_id', $regulationId)->orderBy('version_number', 'DESC')->first();
        return $row ? (int) $row['version_number'] : 0;
    }
}
