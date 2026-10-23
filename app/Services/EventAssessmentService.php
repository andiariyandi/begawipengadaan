<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;

class EventAssessmentService
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    /**
     * Get assessment configuration for an event (Pre-test and Post-test).
     */
    public function getAssessmentConfig(int $eventId): array
    {
        $defaultConfig = [
            'pre_test' => [
                'enabled'          => false,
                'title'            => 'Pre-Test Pemahaman Materi',
                'instructions'     => 'Kerjakan pre-test ini sebelum pemaparan dimulai guna mengukur pemahaman awal terhadap topik kegiatan.',
                'duration_minutes' => 15,
                'passing_score'    => 70,
                'questions'        => [],
            ],
            'post_test' => [
                'enabled'          => false,
                'title'            => 'Post-Test Evaluasi Kegiatan',
                'instructions'     => 'Kerjakan post-test ini setelah menyimak paparan materi untuk mengevaluasi pemahaman akhir Anda.',
                'duration_minutes' => 15,
                'passing_score'    => 70,
                'questions'        => [],
            ],
            'self_paced' => [
                'enabled'             => false,
                'recording_url'       => '',
                'recording_title'     => 'Rekaman Sesi Paparan Narasumber',
                'extended_until'      => null,
                'min_post_test_score' => 70,
                'require_pre_test'    => true,
                'require_materials'   => true,
                'require_post_test'   => true,
                'instructions'        => 'Kegiatan ini telah selesai diselenggarakan dan dibuka kembali untuk Pembelajaran Mandiri. Simak rekaman sesi paparan, pelajari dokumen materi, serta selesaikan pre-test dan post-test untuk mengklaim E-Sertifikat resmi.',
            ],
        ];

        // 1. Check file_assets table
        $row = $this->db->table('file_assets')
            ->where('resource_type', 'event_assessment')
            ->where('resource_id', $eventId)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        if ($row && ! empty($row['metadata'])) {
            $parsed = is_string($row['metadata']) ? json_decode($row['metadata'], true) : $row['metadata'];
            if (is_array($parsed)) {
                return $this->normalizeConfig($parsed, $defaultConfig);
            }
        }

        // 2. Fallback to filesystem if any
        $filePath = WRITEPATH . 'uploads/events/assessments/assessment_' . $eventId . '.json';
        if (is_file($filePath)) {
            $content = @file_get_contents($filePath);
            if ($content !== false) {
                $parsed = json_decode($content, true);
                if (is_array($parsed)) {
                    return $this->normalizeConfig($parsed, $defaultConfig);
                }
            }
        }

        return $defaultConfig;
    }

    /**
     * Normalize and guarantee structure of assessment config.
     */
    protected function normalizeConfig(array $data, array $default): array
    {
        $result = $default;

        foreach (['pre_test', 'post_test'] as $type) {
            if (isset($data[$type]) && is_array($data[$type])) {
                $src = $data[$type];
                $result[$type]['enabled'] = ! empty($src['enabled']);
                $result[$type]['title'] = trim((string) ($src['title'] ?? $default[$type]['title']));
                $result[$type]['instructions'] = trim((string) ($src['instructions'] ?? $default[$type]['instructions']));
                $result[$type]['duration_minutes'] = max(1, (int) ($src['duration_minutes'] ?? $default[$type]['duration_minutes']));
                $result[$type]['passing_score'] = max(1, min(100, (int) ($src['passing_score'] ?? $default[$type]['passing_score'])));

                $questions = [];
                if (! empty($src['questions']) && is_array($src['questions'])) {
                    $qIdx = 1;
                    foreach ($src['questions'] as $q) {
                        if (! is_array($q)) {
                            continue;
                        }
                        $questionText = trim((string) ($q['question'] ?? ''));
                        if ($questionText === '') {
                            continue;
                        }
                        $options = [];
                        if (isset($q['options']) && is_array($q['options'])) {
                            foreach ($q['options'] as $opt) {
                                $optText = trim((string) $opt);
                                if ($optText !== '') {
                                    $options[] = $optText;
                                }
                            }
                        }
                        if (count($options) < 2) {
                            continue;
                        }

                        $answer = (int) ($q['answer'] ?? 0);
                        if ($answer < 0 || $answer >= count($options)) {
                            $answer = 0;
                        }

                        $questions[] = [
                            'id'          => $qIdx++,
                            'question'    => $questionText,
                            'options'     => array_values($options),
                            'answer'      => $answer,
                            'explanation' => trim((string) ($q['explanation'] ?? '')),
                        ];
                    }
                }
                $result[$type]['questions'] = $questions;
            }
        }

        if (isset($data['self_paced']) && is_array($data['self_paced'])) {
            $sp = $data['self_paced'];
            $result['self_paced']['enabled'] = ! empty($sp['enabled']);
            $result['self_paced']['recording_url'] = trim((string) ($sp['recording_url'] ?? ''));
            $result['self_paced']['recording_title'] = trim((string) ($sp['recording_title'] ?? $default['self_paced']['recording_title']));
            $result['self_paced']['extended_until'] = ! empty($sp['extended_until']) ? trim((string) $sp['extended_until']) : null;
            $result['self_paced']['min_post_test_score'] = max(1, min(100, (int) ($sp['min_post_test_score'] ?? $default['self_paced']['min_post_test_score'])));
            $result['self_paced']['require_pre_test'] = array_key_exists('require_pre_test', $sp) ? ! empty($sp['require_pre_test']) : true;
            $result['self_paced']['require_materials'] = array_key_exists('require_materials', $sp) ? ! empty($sp['require_materials']) : true;
            $result['self_paced']['require_post_test'] = array_key_exists('require_post_test', $sp) ? ! empty($sp['require_post_test']) : true;
            $result['self_paced']['instructions'] = trim((string) ($sp['instructions'] ?? $default['self_paced']['instructions']));
        }

        return $result;
    }

    /**
     * Save assessment configuration for an event.
     */
    public function saveAssessmentConfig(int $eventId, array $config, ?int $actorId = null): bool
    {
        $normalized = $this->normalizeConfig($config, $this->getAssessmentConfig($eventId));
        $json = json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $now = date('Y-m-d H:i:s');

        // Check existing file_assets row
        $existing = $this->db->table('file_assets')
            ->where('resource_type', 'event_assessment')
            ->where('resource_id', $eventId)
            ->where('deleted_at IS NULL')
            ->get()->getRowArray();

        $dir = WRITEPATH . 'uploads/events/assessments';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        $filename = 'assessment_' . $eventId . '.json';
        $fullPath = $dir . DIRECTORY_SEPARATOR . $filename;
        @file_put_contents($fullPath, $json);

        if ($existing) {
            $this->db->table('file_assets')
                ->where('id', $existing['id'])
                ->update([
                    'metadata'   => $json,
                    'size_bytes' => strlen($json),
                    'updated_at' => $now,
                ]);
        } else {
            $this->db->table('file_assets')->insert([
                'disk'             => 'local',
                'path'             => 'uploads/events/assessments/' . $filename,
                'original_name'    => $filename,
                'stored_name'      => $filename,
                'extension'        => 'json',
                'mime_type'        => 'application/json',
                'size_bytes'       => strlen($json),
                'visibility'       => 'restricted',
                'lifecycle_status' => 'available',
                'resource_type'    => 'event_assessment',
                'resource_id'      => $eventId,
                'uploaded_by'      => $this->sanitizeUserId($actorId),
                'metadata'         => $json,
                'available_at'     => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        return true;
    }

    /**
     * Get list of materials uploaded for an event.
     */
    public function getMaterials(int $eventId): array
    {
        $rows = $this->db->table('file_assets')
            ->where('resource_type', 'event_material')
            ->where('resource_id', $eventId)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $materials = [];
        foreach ($rows as $row) {
            $meta = [];
            if (! empty($row['metadata'])) {
                $meta = is_string($row['metadata']) ? json_decode($row['metadata'], true) : $row['metadata'];
            }
            if (! is_array($meta)) {
                $meta = [];
            }

            $ext = strtolower((string) ($row['extension'] ?? pathinfo($row['original_name'], PATHINFO_EXTENSION)));
            $icon = match ($ext) {
                'pdf'           => 'fas fa-file-pdf text-danger',
                'ppt', 'pptx'   => 'fas fa-file-powerpoint text-warning',
                'xls', 'xlsx'   => 'fas fa-file-excel text-success',
                'doc', 'docx'   => 'fas fa-file-word text-primary',
                default         => 'fas fa-file-alt text-secondary',
            };

            $materials[] = [
                'id'            => (int) $row['id'],
                'event_id'      => $eventId,
                'title'         => $meta['title'] ?? pathinfo($row['original_name'], PATHINFO_FILENAME),
                'speaker'       => $meta['speaker'] ?? '',
                'notes'         => $meta['notes'] ?? '',
                'category'      => $meta['category'] ?? 'materi_tayang',
                'original_name' => $row['original_name'],
                'stored_name'   => $row['stored_name'],
                'extension'     => $ext,
                'mime_type'     => $row['mime_type'],
                'size_bytes'    => (int) $row['size_bytes'],
                'size_human'    => $this->formatBytes((int) $row['size_bytes']),
                'icon'          => $icon,
                'created_at'    => $row['created_at'],
                'url'           => site_url('file/' . (int) $row['id']),
                'download_url'  => site_url('file/' . (int) $row['id'] . '?download=1'),
            ];
        }

        return $materials;
    }

    /**
     * Upload an event presentation or handout material.
     */
    public function uploadMaterial(int $eventId, UploadedFile $file, array $metadata = [], ?int $actorId = null): ?int
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedExtensions = ['pdf', 'ppt', 'pptx', 'xls', 'xlsx', 'doc', 'docx'];
        $ext = strtolower($file->getClientExtension() ?: $file->guessExtension() ?: '');
        if (! in_array($ext, $allowedExtensions, true)) {
            return null;
        }

        $dir = WRITEPATH . 'uploads/events/materials';
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return null;
        }

        $storedName = $file->getRandomName();
        if (! $file->move($dir, $storedName)) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        $relativePath = 'uploads/events/materials/' . $storedName;

        $metaPayload = [
            'title'       => trim((string) ($metadata['title'] ?? pathinfo($file->getClientName(), PATHINFO_FILENAME))),
            'speaker'     => trim((string) ($metadata['speaker'] ?? '')),
            'notes'       => trim((string) ($metadata['notes'] ?? '')),
            'category'    => trim((string) ($metadata['category'] ?? 'materi_tayang')),
            'uploaded_at' => $now,
        ];

        $this->db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $file->getClientName() ?: $storedName,
            'stored_name'      => $storedName,
            'extension'        => $ext,
            'mime_type'        => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes'       => (int) ($file->getSize() ?: 0),
            'visibility'       => 'public',
            'lifecycle_status' => 'available',
            'resource_type'    => 'event_material',
            'resource_id'      => $eventId,
            'uploaded_by'      => $this->sanitizeUserId($actorId),
            'metadata'         => json_encode($metaPayload, JSON_UNESCAPED_UNICODE),
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        return (int) $this->db->insertID();
    }

    /**
     * Delete an event material.
     */
    public function deleteMaterial(int $fileAssetId, int $eventId): bool
    {
        $row = $this->db->table('file_assets')
            ->where('id', $fileAssetId)
            ->where('resource_type', 'event_material')
            ->where('resource_id', $eventId)
            ->get()->getRowArray();

        if (! $row) {
            return false;
        }

        // Soft delete and physically remove file
        $this->db->table('file_assets')
            ->where('id', $fileAssetId)
            ->update([
                'deleted_at'       => date('Y-m-d H:i:s'),
                'lifecycle_status' => 'deleted',
            ]);

        $filePath = WRITEPATH . ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $row['path']), DIRECTORY_SEPARATOR);
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        return true;
    }

    /**
     * Submit a quiz attempt (pre-test or post-test).
     */
    public function submitQuizAttempt(int $eventId, int $userId, int $registrationId, string $quizType, array $submittedAnswers): array
    {
        if (! in_array($quizType, ['pre_test', 'post_test'], true)) {
            throw new \InvalidArgumentException('Tipe kuis tidak valid.');
        }

        $config = $this->getAssessmentConfig($eventId);
        $quiz = $config[$quizType] ?? null;

        if (! $quiz || empty($quiz['enabled']) || empty($quiz['questions'])) {
            throw new \RuntimeException('Kuis ' . ($quizType === 'pre_test' ? 'Pre-Test' : 'Post-Test') . ' tidak aktif pada event ini.');
        }

        $questions = $quiz['questions'];
        $totalQuestions = count($questions);
        $correctCount = 0;
        $review = [];

        foreach ($questions as $idx => $q) {
            $qId = (string) $q['id'];
            $userAns = null;
            if (array_key_exists($qId, $submittedAnswers)) {
                $userAns = (int) $submittedAnswers[$qId];
            } elseif (array_key_exists((int) $qId, $submittedAnswers)) {
                $userAns = (int) $submittedAnswers[(int) $qId];
            } elseif (array_key_exists($idx, $submittedAnswers)) {
                $userAns = (int) $submittedAnswers[$idx];
            }
            $correctAns = (int) $q['answer'];
            $isCorrect = ($userAns !== null && $userAns === $correctAns);

            if ($isCorrect) {
                $correctCount++;
            }

            $review[] = [
                'id'             => $q['id'],
                'question'       => $q['question'],
                'options'        => $q['options'],
                'user_answer'    => $userAns,
                'correct_answer' => $correctAns,
                'is_correct'     => $isCorrect,
                'explanation'    => $q['explanation'] ?? '',
            ];
        }

        $score = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
        $passingScore = (int) ($quiz['passing_score'] ?? 70);
        $isPassed = ($score >= $passingScore);
        $now = date('Y-m-d H:i:s');

        $submissionData = [
            'registration_id' => $registrationId,
            'user_id'         => $userId,
            'quiz_type'       => $quizType,
            'quiz_title'      => $quiz['title'],
            'score'           => $score,
            'correct_count'   => $correctCount,
            'total_questions' => $totalQuestions,
            'passing_score'   => $passingScore,
            'is_passed'       => $isPassed,
            'submitted_answers' => $submittedAnswers,
            'review'          => $review,
            'submitted_at'    => $now,
        ];

        $json = json_encode($submissionData, JSON_UNESCAPED_UNICODE);

        // Store into file_assets table
        $this->db->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => 'uploads/events/submissions/evt_' . $eventId . '_u' . $userId . '_' . $quizType . '.json',
            'original_name'    => 'submission_' . $quizType . '_' . $userId . '.json',
            'stored_name'      => 'submission_' . $quizType . '_' . $userId . '_' . time() . '.json',
            'extension'        => 'json',
            'mime_type'        => 'application/json',
            'size_bytes'       => strlen($json),
            'visibility'       => 'restricted',
            'lifecycle_status' => 'available',
            'resource_type'    => 'event_quiz_submission',
            'resource_id'      => $eventId,
            'uploaded_by'      => $this->sanitizeUserId($userId),
            'metadata'         => $json,
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        return [
            'success'         => true,
            'quiz_type'       => $quizType,
            'quiz_title'      => $quiz['title'],
            'score'           => $score,
            'correct_count'   => $correctCount,
            'total_questions' => $totalQuestions,
            'passing_score'   => $passingScore,
            'is_passed'       => $isPassed,
            'submitted_at'    => $now,
            'review'          => $review,
        ];
    }

    /**
     * Get participant's latest attempt for a quiz type.
     */
    public function getUserAttempt(int $eventId, int $userId, string $quizType): ?array
    {
        $row = $this->db->table('file_assets')
            ->where('resource_type', 'event_quiz_submission')
            ->where('resource_id', $eventId)
            ->where('uploaded_by', $userId)
            ->where('deleted_at IS NULL')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        foreach ($row as $r) {
            if (empty($r['metadata'])) {
                continue;
            }
            $meta = is_string($r['metadata']) ? json_decode($r['metadata'], true) : $r['metadata'];
            if (is_array($meta) && ($meta['quiz_type'] ?? '') === $quizType) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * Get assessment statistics for an event (for administrator view).
     */
    public function getAssessmentStats(int $eventId): array
    {
        $rows = $this->db->table('file_assets')
            ->where('resource_type', 'event_quiz_submission')
            ->where('resource_id', $eventId)
            ->where('deleted_at IS NULL')
            ->get()->getResultArray();

        $stats = [
            'pre_test_count'    => 0,
            'pre_test_passed'   => 0,
            'pre_test_avg'      => 0,
            'post_test_count'   => 0,
            'post_test_passed'  => 0,
            'post_test_avg'     => 0,
            'pre_test_scores'   => [],
            'post_test_scores'  => [],
        ];

        foreach ($rows as $r) {
            if (empty($r['metadata'])) {
                continue;
            }
            $meta = is_string($r['metadata']) ? json_decode($r['metadata'], true) : $r['metadata'];
            if (! is_array($meta)) {
                continue;
            }
            $type = $meta['quiz_type'] ?? '';
            $score = (int) ($meta['score'] ?? 0);
            $isPassed = ! empty($meta['is_passed']);

            if ($type === 'pre_test') {
                $stats['pre_test_count']++;
                $stats['pre_test_scores'][] = $score;
                if ($isPassed) {
                    $stats['pre_test_passed']++;
                }
            } elseif ($type === 'post_test') {
                $stats['post_test_count']++;
                $stats['post_test_scores'][] = $score;
                if ($isPassed) {
                    $stats['post_test_passed']++;
                }
            }
        }

        if ($stats['pre_test_count'] > 0) {
            $stats['pre_test_avg'] = round(array_sum($stats['pre_test_scores']) / $stats['pre_test_count'], 1);
        }
        if ($stats['post_test_count'] > 0) {
            $stats['post_test_avg'] = round(array_sum($stats['post_test_scores']) / $stats['post_test_count'], 1);
        }

        return $stats;
    }

    /**
     * Check self-paced learning progress for a participant.
     */
    public function getSelfPacedProgress(int $eventId, int $userId, int $registrationId, bool $isPast = false): array
    {
        $config    = $this->getAssessmentConfig($eventId);
        $selfPaced = $config['self_paced'] ?? [];
        $preTest   = $config['pre_test'] ?? [];
        $postTest  = $config['post_test'] ?? [];

        $preAttempt  = $this->getUserAttempt($eventId, $userId, 'pre_test');
        $postAttempt = $this->getUserAttempt($eventId, $userId, 'post_test');
        $materials   = $this->getMaterials($eventId);

        $isSelfPacedEnabled = ! empty($selfPaced['enabled']);

        // Requirements for self-paced certification
        $isPreTestReq = ! empty($preTest['enabled']) && ! empty($preTest['questions']) && ! empty($selfPaced['require_pre_test']);
        $isPostTestReq = ! empty($postTest['enabled']) && ! empty($postTest['questions']) && ! empty($selfPaced['require_post_test']);
        $isMaterialsReq = ! empty($materials) || ! empty($selfPaced['recording_url']);

        $hasCompletedPreTest = (! $isPreTestReq) || (! empty($preAttempt));

        $minScore = (int) ($selfPaced['min_post_test_score'] ?? ($postTest['passing_score'] ?? 70));
        $postScore = (int) ($postAttempt['score'] ?? 0);
        $hasPassedPostTest = (! $isPostTestReq) || (! empty($postAttempt) && $postScore >= $minScore);

        $hasAccessedMaterials = true; // Materials and recording are available for open self-study

        $isEligible = ($isSelfPacedEnabled && $hasCompletedPreTest && $hasPassedPostTest && $hasAccessedMaterials);

        // Check existing certificate
        $cert = $this->db->table('certificates')
            ->where('source_type', 'event_registration')
            ->where('source_id', $registrationId)
            ->get()->getRowArray();

        $hasCertificate = (! empty($cert) && ($cert['status'] ?? '') === 'issued');

        return [
            'is_self_paced_enabled'   => $isSelfPacedEnabled,
            'is_past'                 => $isPast,
            'recording_url'           => $selfPaced['recording_url'] ?? '',
            'recording_title'         => $selfPaced['recording_title'] ?? 'Rekaman Sesi Paparan Narasumber',
            'recording_embed_url'     => self::getYoutubeEmbedUrl($selfPaced['recording_url'] ?? ''),
            'instructions'            => $selfPaced['instructions'] ?? '',
            'min_post_test_score'     => $minScore,
            'is_pre_test_required'    => $isPreTestReq,
            'has_completed_pre_test'  => $hasCompletedPreTest,
            'pre_test_attempt'        => $preAttempt,
            'is_materials_required'   => $isMaterialsReq,
            'has_accessed_materials'  => $hasAccessedMaterials,
            'is_post_test_required'   => $isPostTestReq,
            'has_passed_post_test'    => $hasPassedPostTest,
            'post_test_attempt'       => $postAttempt,
            'is_eligible_for_cert'    => $isEligible,
            'has_certificate'         => $hasCertificate,
            'certificate'             => $cert,
        ];
    }

    /**
     * Claim E-Certificate for self-paced participant after fulfilling activities.
     */
    public function claimSelfPacedCertificate(int $eventId, int $userId, int $registrationId, ?int $actorId = null): array
    {
        $progress = $this->getSelfPacedProgress($eventId, $userId, $registrationId);

        if (! $progress['is_self_paced_enabled']) {
            throw new \RuntimeException('Mode Pembelajaran Mandiri tidak diaktifkan pada agenda ini.');
        }

        if (! $progress['is_eligible_for_cert']) {
            $reasons = [];
            if (! $progress['has_completed_pre_test']) {
                $reasons[] = 'Selesaikan Pre-Test terlebih dahulu';
            }
            if (! $progress['has_passed_post_test']) {
                $reasons[] = 'Lulus Post-Test dengan nilai minimal ' . $progress['min_post_test_score'];
            }
            throw new \RuntimeException('Syarat sertifikat belum terpenuhi: ' . implode(', ', $reasons) . '.');
        }

        if ($progress['has_certificate']) {
            return [
                'success'            => true,
                'message'            => 'Sertifikat telah terbit sebelumnya.',
                'certificate_number' => $progress['certificate']['certificate_number'],
                'certificate_id'     => $progress['certificate']['id'],
            ];
        }

        $now = date('Y-m-d H:i:s');
        $preScore = $progress['pre_test_attempt']['score'] ?? '-';
        $postScore = $progress['post_test_attempt']['score'] ?? '-';
        $noteText = 'Hadir via Pembelajaran Mandiri (Pre-Test: ' . $preScore . ', Post-Test: ' . $postScore . ')';

        $attActor = $this->sanitizeUserId($actorId) ?: $this->sanitizeUserId($userId);

        $this->db->transStart();

        // Update or insert attendance record marked as present
        $existingAtt = $this->db->table('attendances')
            ->where('event_registration_id', $registrationId)
            ->get()->getRowArray();

        if ($existingAtt) {
            $this->db->table('attendances')->where('id', $existingAtt['id'])->update([
                'status'      => 'present',
                'recorded_by' => $attActor,
                'recorded_at' => $now,
                'notes'       => $noteText,
                'updated_at'  => $now,
            ]);
        } else {
            $this->db->table('attendances')->insert([
                'event_registration_id' => $registrationId,
                'status'                => 'present',
                'recorded_by'           => $attActor,
                'recorded_at'           => $now,
                'notes'                 => $noteText,
                'created_at'            => $now,
                'updated_at'            => $now,
            ]);
        }

        // Generate certificate using official CertificateGenerator
        $generator = new \App\Services\CertificateGenerator();
        $adminUser = $this->db->table('users')->where('deleted_at IS NULL')->orderBy('id', 'ASC')->get()->getRowArray();
        $generatorActor = $attActor ?: ($adminUser ? (int) $adminUser['id'] : null);

        $certResult = $generator->generateDirect('event_registration', $registrationId, $generatorActor);

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new \RuntimeException('Gagal memproses kehadiran dan penerbitan sertifikat mandiri.');
        }

        return [
            'success'            => true,
            'message'            => 'E-Sertifikat Pembelajaran Mandiri berhasil diterbitkan.',
            'certificate_number' => $certResult['certificate_number'] ?? '',
            'certificate_id'     => $certResult['certificate_id'] ?? null,
        ];
    }

    /**
     * Helper to extract YouTube video embed URL.
     */
    public static function getYoutubeEmbedUrl(string $url): ?string
    {
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        return null;
    }

    /**
     * Helper to format bytes to human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 1): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Verify that user exists in users table before setting uploaded_by foreign key.
     */
    protected function sanitizeUserId(?int $userId): ?int
    {
        if ($userId === null || $userId <= 0) {
            return null;
        }
        $exists = $this->db->table('users')->where('id', $userId)->countAllResults();
        return $exists > 0 ? $userId : null;
    }
}
