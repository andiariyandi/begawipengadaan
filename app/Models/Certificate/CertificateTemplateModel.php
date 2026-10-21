<?php

namespace App\Models\Certificate;

use CodeIgniter\Model;

/**
 * certificate_templates (v4).
 *
 * Kolom yang dipakai:
 *  - name           VARCHAR(255)
 *  - source_type    ENUM('learning','event')         -- sumber sertifikat
 *  - template_payload JSON                            -- payload teks/atribut
 *  - background_file_id BIGINT NULL → file_assets.id
 *  - is_active      TINYINT(1) DEFAULT 1
 *  - created_by, updated_by → users.id (NULL)
 *
 * TIDAK memakai kolom legacy seperti file_path.
 */
class CertificateTemplateModel extends Model
{
    protected $table         = 'certificate_templates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'source_type',
        'template_payload',
        'background_file_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * Dropdown sumber template: hanya yang aktif, diurut naik.
     */
    public function listActive(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Ambil template default yang aktif dan cocok dengan source_type.
     */
    public function findDefaultForSource(string $sourceType): ?array
    {
        $row = $this->where('is_active', 1)
            ->where('source_type', $sourceType)
            ->orderBy('id', 'ASC')
            ->first();
        if ($row) {
            return $row;
        }

        // Fallback to any active template so generation is robust
        return $this->where('is_active', 1)->orderBy('id', 'ASC')->first();
    }

    /**
     * Peta lock hirarki Arena Pembelajaran (Program -> Course -> Class).
     * Mencegah tumpang tindih template:
     * - Program diset: semua Course & Kelas di bawahnya terkunci otomatis memakai template program.
     * - Course diset: semua Kelas di bawah Course tersebut terkunci otomatis memakai template course.
     * - Kelas diset: hanya bisa jika Program & Course belum memiliki template (kosong).
     */
    public function getLearningHierarchyLockInfo(?int $excludeTemplateId = null): array
    {
        $db = $this->db;

        // Ambil semua template active learning
        $builder = $this->where('is_active', 1)->where('source_type', 'learning');
        if ($excludeTemplateId !== null && $excludeTemplateId > 0) {
            $builder->where('id !=', $excludeTemplateId);
        }
        $templates = $builder->findAll();

        $lockedPrograms = []; // [prog_id => template]
        $lockedCourses  = []; // [course_id => template]
        $lockedClasses  = []; // [class_id => template]

        foreach ($templates as $t) {
            $payload = json_decode((string) ($t['template_payload'] ?? ''), true);
            if (! is_array($payload)) {
                continue;
            }
            $scope = $payload['target_scope'] ?? 'global';
            if ($scope === 'program' && ! empty($payload['program_id'])) {
                $lockedPrograms[(int) $payload['program_id']] = [
                    'template_id'   => (int) $t['id'],
                    'template_name' => (string) $t['name'],
                    'scope'         => 'program',
                ];
            } elseif ($scope === 'course' && ! empty($payload['course_id'])) {
                $lockedCourses[(int) $payload['course_id']] = [
                    'template_id'   => (int) $t['id'],
                    'template_name' => (string) $t['name'],
                    'scope'         => 'course',
                ];
            } elseif ($scope === 'class' && ! empty($payload['class_id'])) {
                $lockedClasses[(int) $payload['class_id']] = [
                    'template_id'   => (int) $t['id'],
                    'template_name' => (string) $t['name'],
                    'scope'         => 'class',
                ];
            }
        }

        // Ambil relasi Courses
        $courses = $db->table('courses')
            ->select('id, program_id, title')
            ->where('deleted_at IS NULL')
            ->get()
            ->getResultArray();

        // Ambil relasi Classes
        $classes = $db->table('learning_classes lc')
            ->select('lc.id, lc.course_id, lc.name, co.program_id, co.title AS course_title')
            ->join('courses co', 'co.id = lc.course_id', 'left')
            ->where('lc.deleted_at IS NULL')
            ->get()
            ->getResultArray();

        $courseLocks = []; // course_id => lock info
        foreach ($courses as $c) {
            $cId = (int) $c['id'];
            $pId = (int) ($c['program_id'] ?? 0);
            if ($pId > 0 && isset($lockedPrograms[$pId])) {
                $courseLocks[$cId] = [
                    'locked_by'     => 'program',
                    'program_id'    => $pId,
                    'template_id'   => $lockedPrograms[$pId]['template_id'],
                    'template_name' => $lockedPrograms[$pId]['template_name'],
                ];
            } elseif (isset($lockedCourses[$cId])) {
                $courseLocks[$cId] = [
                    'locked_by'     => 'course',
                    'course_id'     => $cId,
                    'template_id'   => $lockedCourses[$cId]['template_id'],
                    'template_name' => $lockedCourses[$cId]['template_name'],
                ];
            }
        }

        $classLocks = []; // class_id => lock info
        foreach ($classes as $cl) {
            $clId = (int) $cl['id'];
            $cId  = (int) ($cl['course_id'] ?? 0);
            $pId  = (int) ($cl['program_id'] ?? 0);

            if ($pId > 0 && isset($lockedPrograms[$pId])) {
                $classLocks[$clId] = [
                    'locked_by'     => 'program',
                    'program_id'    => $pId,
                    'template_id'   => $lockedPrograms[$pId]['template_id'],
                    'template_name' => $lockedPrograms[$pId]['template_name'],
                ];
            } elseif ($cId > 0 && isset($lockedCourses[$cId])) {
                $classLocks[$clId] = [
                    'locked_by'     => 'course',
                    'course_id'     => $cId,
                    'template_id'   => $lockedCourses[$cId]['template_id'],
                    'template_name' => $lockedCourses[$cId]['template_name'],
                ];
            } elseif (isset($lockedClasses[$clId])) {
                $classLocks[$clId] = [
                    'locked_by'     => 'class',
                    'class_id'      => $clId,
                    'template_id'   => $lockedClasses[$clId]['template_id'],
                    'template_name' => $lockedClasses[$clId]['template_name'],
                ];
            }
        }

        return [
            'locked_programs' => $lockedPrograms,
            'locked_courses'  => $lockedCourses,
            'locked_classes'  => $lockedClasses,
            'course_locks'    => $courseLocks,
            'class_locks'     => $classLocks,
        ];
    }

    /**
     * Peta template aktif untuk Event / Agenda (Setiap Event bersifat Unik 1:1).
     * @return array<int, array{template_id: int, template_name: string}>
     */
    public function getAssignedEventMap(?int $excludeTemplateId = null): array
    {
        $builder = $this->where('is_active', 1)->where('source_type', 'event');
        if ($excludeTemplateId !== null && $excludeTemplateId > 0) {
            $builder->where('id !=', $excludeTemplateId);
        }
        $templates = $builder->findAll();

        $eventMap = [];
        foreach ($templates as $t) {
            $payload = json_decode((string) ($t['template_payload'] ?? ''), true);
            if (! is_array($payload) || empty($payload['event_id'])) {
                continue;
            }
            $eventId = (int) $payload['event_id'];
            $eventMap[$eventId] = [
                'template_id'   => (int) $t['id'],
                'template_name' => (string) $t['name'],
            ];
        }

        return $eventMap;
    }

    /**
     * Cari template khusus yang dikonfigurasi untuk Event tertentu.
     */
    public function findForEvent(int $eventId): ?array
    {
        if ($eventId < 1) {
            return null;
        }

        $templates = $this->where('is_active', 1)
            ->where('source_type', 'event')
            ->findAll();

        foreach ($templates as $t) {
            $payload = json_decode((string) ($t['template_payload'] ?? ''), true);
            if (is_array($payload) && (int) ($payload['event_id'] ?? 0) === $eventId) {
                return $t;
            }
        }

        return null;
    }

    /**
     * Cari template sertifikat Arena Pembelajaran secara berjenjang (Program -> Course -> Class).
     */
    public function findForLearning(int $classId, int $courseId = 0, int $programId = 0): ?array
    {
        $db = $this->db;

        // Lengkapi course_id & program_id jika belum terisi
        if ($classId > 0 && ($courseId <= 0 || $programId <= 0)) {
            $row = $db->table('learning_classes lc')
                ->select('lc.course_id, co.program_id')
                ->join('courses co', 'co.id = lc.course_id', 'left')
                ->where('lc.id', $classId)
                ->get()
                ->getRowArray();
            if ($row) {
                if ($courseId <= 0) {
                    $courseId = (int) ($row['course_id'] ?? 0);
                }
                if ($programId <= 0) {
                    $programId = (int) ($row['program_id'] ?? 0);
                }
            }
        }

        $templates = $this->where('is_active', 1)
            ->where('source_type', 'learning')
            ->findAll();

        $byProgram = null;
        $byCourse  = null;
        $byClass   = null;
        $global    = null;

        foreach ($templates as $t) {
            $p = json_decode((string) ($t['template_payload'] ?? ''), true);
            if (! is_array($p)) {
                continue;
            }
            $scope = $p['target_scope'] ?? 'global';
            if ($scope === 'program' && $programId > 0 && (int) ($p['program_id'] ?? 0) === $programId) {
                $byProgram = $t;
            } elseif ($scope === 'course' && $courseId > 0 && (int) ($p['course_id'] ?? 0) === $courseId) {
                $byCourse = $t;
            } elseif ($scope === 'class' && $classId > 0 && (int) ($p['class_id'] ?? 0) === $classId) {
                $byClass = $t;
            } elseif ($scope === 'global' && $global === null) {
                $global = $t;
            }
        }

        // Resolusi bertingkat sesuai aturan anti-tumpang-tindih:
        // 1. Jika Program diset -> Course & Kelas terkunci memakai Program
        if ($byProgram !== null) {
            return $byProgram;
        }
        // 2. Jika Course diset -> Kelas terkunci memakai Course
        if ($byCourse !== null) {
            return $byCourse;
        }
        // 3. Jika Program & Course kosong -> Pakai template khusus Kelas
        if ($byClass !== null) {
            return $byClass;
        }
        // 4. Fallback global / default
        if ($global !== null) {
            return $global;
        }

        return $this->findDefaultForSource('learning');
    }

    /**
     * Resolusi template pintar untuk proses generate sertifikat:
     * - Event: Wajib spesifik ke event tersebut
     * - Learning: Resolusi hirarkis Program -> Course -> Class
     */
    public function resolveTemplate(string $sourceType, array $sourceData, ?int $explicitTemplateId = null): ?array
    {
        if ($explicitTemplateId !== null && $explicitTemplateId > 0) {
            $found = $this->find($explicitTemplateId);
            if ($found && (int) $found['is_active'] === 1) {
                return $found;
            }
        }

        if ($sourceType === 'event_registration' || $sourceType === 'event') {
            $eventId = (int) ($sourceData['event_id'] ?? ($sourceData['id_kegiatan'] ?? 0));
            $template = $this->findForEvent($eventId);
            if ($template !== null) {
                return $template;
            }
            return $this->findDefaultForSource('event');
        }

        if ($sourceType === 'learning_enrollment' || $sourceType === 'learning') {
            $classId   = (int) ($sourceData['class_id'] ?? ($sourceData['id_kegiatan'] ?? 0));
            $courseId  = (int) ($sourceData['course_id'] ?? 0);
            $programId = (int) ($sourceData['program_id'] ?? 0);

            return $this->findForLearning($classId, $courseId, $programId);
        }

        return $this->findDefaultForSource('learning');
    }
}
