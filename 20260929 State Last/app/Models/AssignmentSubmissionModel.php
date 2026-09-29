<?php
namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'assignment_id','user_id',
        'file','answer',
        'submitted_at','status'
    ];
}
