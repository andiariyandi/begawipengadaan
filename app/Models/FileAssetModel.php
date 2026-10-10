<?php

namespace App\Models;

use CodeIgniter\Model;

class FileAssetModel extends Model
{
    protected $table            = 'file_assets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'disk',
        'path',
        'original_name',
        'stored_name',
        'extension',
        'mime_type',
        'size_bytes',
        'checksum',
        'visibility',
        'lifecycle_status',
        'resource_type',
        'resource_id',
        'uploaded_by',
        'available_at',
        'expires_at',
        'metadata',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
