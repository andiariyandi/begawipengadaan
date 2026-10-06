<?php
namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_id','class_id','module_id',
        'title','description','max_score',
        'deadline','status'
    ];
}
