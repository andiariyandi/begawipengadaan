<?php
namespace App\Models;

use CodeIgniter\Model;

class AssignmentGradeModel extends Model
{
    protected $table = 'assignment_grades';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'submission_id',
        'evaluator_id',
        'score',
        'feedback',
        'graded_at'
    ];
}
