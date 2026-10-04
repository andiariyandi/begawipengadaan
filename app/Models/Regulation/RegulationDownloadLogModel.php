<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationDownloadLogModel extends Model
{
    protected $table         = 'regulation_downloads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'regulation_id', 'version_id', 'file_id', 'user_id',
        'access_type', 'created_at',
    ];

    public function browse(array $filters = []): array
    {
        $builder = $this->select('regulation_downloads.*, users.username, users.name AS user_name,
                                  r.title AS regulation_title, r.document_number,
                                  v.version_number')
            ->join('users', 'users.id = regulation_downloads.user_id', 'left')
            ->join('regulations r', 'r.id = regulation_downloads.regulation_id', 'left')
            ->join('regulation_versions v', 'v.id = regulation_downloads.version_id', 'left')
            ->orderBy('regulation_downloads.created_at', 'DESC');

        if (! empty($filters['user_id'])) {
            $builder->where('regulation_downloads.user_id', (int) $filters['user_id']);
        }
        if (! empty($filters['regulation_id'])) {
            $builder->where('regulation_downloads.regulation_id', (int) $filters['regulation_id']);
        }
        if (! empty($filters['from'])) {
            $builder->where('regulation_downloads.created_at >=', $filters['from'] . ' 00:00:00');
        }
        if (! empty($filters['to'])) {
            $builder->where('regulation_downloads.created_at <=', $filters['to'] . ' 23:59:59');
        }

        return $builder->findAll();
    }
}