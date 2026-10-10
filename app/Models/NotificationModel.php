<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'channel',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'sent_at',
        'created_at',
    ];

    protected $useTimestamps = false;
}
