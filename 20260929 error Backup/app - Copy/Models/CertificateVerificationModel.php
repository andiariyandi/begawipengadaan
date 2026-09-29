<?php

namespace App\Models;

use CodeIgniter\Model;

class CertificateVerificationModel extends Model
{
    protected $table         = 'certificate_verifications';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = ['certificate_id', 'verified_at', 'ip_address'];
}
