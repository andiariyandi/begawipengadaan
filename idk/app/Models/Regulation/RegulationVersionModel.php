<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationVersionModel extends Model
{
    protected $table         = 'regulation_versions';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'regulation_id', 'version_number', 'file_id', 'changelog',
        'is_current', 'uploaded_by', 'created_at',
    ];

    public function historyFor(int $regulationId): array
    {
        return $this->select('regulation_versions.*, users.username AS uploader_name,
                              fa.original_name AS file_original_name, fa.size_bytes AS file_size')
            ->join('users', 'users.id = regulation_versions.uploaded_by', 'left')
            ->join('file_assets fa', 'fa.id = regulation_versions.file_id', 'left')
            ->where('regulation_id', $regulationId)
            ->orderBy('version_number', 'DESC')
            ->findAll();
    }

    public function latestVersionNumber(int $regulationId): int
    {
        $row = $this->where('regulation_id', $regulationId)->orderBy('version_number', 'DESC')->first();
        return $row ? (int) $row['version_number'] : 0;
    }

    /**
     * Mark one version as current and demote all siblings. Wrap in a
     * transaction so the current_version_id on regulations stays consistent.
     */
    public function setCurrent(int $regulationId, int $versionId): bool
    {
        $db = db_connect();
        $db->transStart();
        $this->where('regulation_id', $regulationId)->set(['is_current' => 0])->update();
        $ok = (bool) $this->update($versionId, ['is_current' => 1]);
        if ($ok) {
            (new RegulationModel())->update($regulationId, ['current_version_id' => $versionId]);
        }
        $db->transComplete();
        return (bool) $db->transStatus();
    }
}