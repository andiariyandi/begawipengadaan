<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgressModel extends Model
{
    protected $table         = 'progress';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false; // hanya created_at di skema; completed_at dikelola manual
    protected $allowedFields = ['enrollment_id', 'lesson_id', 'completed_at', 'score', 'created_at'];

    public function isLessonCompleted(int $enrollmentId, int $lessonId): bool
    {
        $row = $this->where('enrollment_id', $enrollmentId)->where('lesson_id', $lessonId)->first();
        return (bool) ($row['completed_at'] ?? false);
    }

    public function completedLessonIds(int $enrollmentId): array
    {
        $rows = $this->where('enrollment_id', $enrollmentId)->where('completed_at IS NOT NULL')->findAll();
        return array_column($rows, 'lesson_id');
    }

    /**
     * Tandai lesson selesai (idempotent — upsert manual karena tidak
     * ada unique key bawaan CI4 upsert yang cocok dengan skema ini).
     */
    public function markComplete(int $enrollmentId, int $lessonId): void
    {
        $existing = $this->where('enrollment_id', $enrollmentId)->where('lesson_id', $lessonId)->first();

        if ($existing) {
            if (empty($existing['completed_at'])) {
                $this->update($existing['id'], ['completed_at' => date('Y-m-d H:i:s')]);
            }
            return;
        }

        $this->insert([
            'enrollment_id' => $enrollmentId,
            'lesson_id'     => $lessonId,
            'completed_at'  => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
