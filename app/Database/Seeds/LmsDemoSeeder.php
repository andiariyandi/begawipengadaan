<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder Demo LMS BegawiPBJ.
 *
 * Menghasilkan data dummy realistis untuk menguji alur lengkap:
 *
 *   Landing → Program → Course → Kelas → Enrollment
 *     → Admin Approval → Workspace Peserta
 *     → Workspace Trainer → Lesson → Quiz ABCD → Auto Grading
 *
 * Schema yang digunakan sesuai dokumen/schema-fresh-v4.sql.
 * Tabel prerequisite (roles) diinsert dengan kolom `key` sesuai schema V4
 * (RoleSeeder bawaan menggunakan kolom `slug` yang tidak ada di V4).
 *
 * Aman dijalankan berulang: gunakan firstOrInsertBy() dengan where key.
 */
class LmsDemoSeeder extends Seeder
{
    private string $passwordHash;

    public function run()
    {
        $this->passwordHash = password_hash('Password123!', PASSWORD_DEFAULT);
        $now = date('Y-m-d H:i:s');

        $operator = $this->ensureUser([
            'name'         => 'Operator BegawiPBJ',
            'username'     => 'operator.pbj',
            'email'        => 'operator.pbj@begawipbj.test',
            'phone'        => '081200000001',
            'profile_type' => 'public',
            'account_status' => 'active',
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);
        $trainerRoleId = $this->ensureRole('trainer', 'Trainer');
        $participantRoleId = $this->ensureRole('peserta', 'Peserta');
        $operatorRoleId = $this->ensureRole('operator', 'Operator');

        $this->ensureUserRole($operator['id'], $operatorRoleId, $now);

        // 3 Trainer
        $trainers = [];
        $trainerSpecs = [
            ['name' => 'Budi Santoso',    'username' => 'trainer.budi',   'email' => 'trainer.budi@begawipbj.test',   'phone' => '081200010001', 'profile_type' => 'asn'],
            ['name' => 'Siti Rahmawati',  'username' => 'trainer.siti',   'email' => 'trainer.siti@begawipbj.test',   'phone' => '081200010002', 'profile_type' => 'academic'],
            ['name' => 'Ahmad Fauzi',     'username' => 'trainer.ahmad',  'email' => 'trainer.ahmad@begawipbj.test',  'phone' => '081200010003', 'profile_type' => 'provider'],
        ];
        foreach ($trainerSpecs as $spec) {
            $u = $this->ensureUser(array_merge($spec, [
                'account_status' => 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]));
            $this->ensureUserRole($u['id'], $trainerRoleId, $now);
            $trainers[$spec['username']] = $u;
        }

        // 5 Peserta
        $pesertaSpecs = [
            ['name' => 'Andi Pratama',   'username' => 'peserta.andi',  'email' => 'peserta.andi@begawipbj.test',  'phone' => '081200020001', 'profile_type' => 'asn'],
            ['name' => 'Rina Maharani',  'username' => 'peserta.rina',  'email' => 'peserta.rina@begawipbj.test',  'phone' => '081200020002', 'profile_type' => 'asn'],
            ['name' => 'Dewi Lestari',   'username' => 'peserta.dewi',  'email' => 'peserta.dewi@begawipbj.test',  'phone' => '081200020003', 'profile_type' => 'asn'],
            ['name' => 'Fajar Nugroho',  'username' => 'peserta.fajar', 'email' => 'peserta.fajar@begawipbj.test', 'phone' => '081200020004', 'profile_type' => 'asn'],
            ['name' => 'Agus Setiawan',  'username' => 'peserta.agus',  'email' => 'peserta.agus@begawipbj.test',  'phone' => '081200020005', 'profile_type' => 'public'],
        ];
        $peserta = [];
        foreach ($pesertaSpecs as $spec) {
            $u = $this->ensureUser(array_merge($spec, [
                'account_status' => 'active',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]));
            $this->ensureUserRole($u['id'], $participantRoleId, $now);
            $peserta[$spec['username']] = $u;
        }

        // 2 Program
        $programs = [
            'dasar' => $this->ensureProgram([
                'title'       => 'Pengadaan Barang/Jasa Pemerintah Dasar',
                'slug'        => 'pbj-dasar',
                'summary'     => 'Program pembelajaran dasar PBJ.',
                'description' => 'Program pembelajaran dasar mengenai konsep, regulasi, perencanaan, dan proses pengadaan pemerintah.',
                'status'      => 'published',
                'visibility'  => 'public',
                'featured'    => 1,
                'created_by'  => $operator['id'],
                'updated_by'  => $operator['id'],
                'published_by'=> $operator['id'],
                'published_at'=> $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]),
            'lanjutan' => $this->ensureProgram([
                'title'       => 'Pengadaan Barang/Jasa Pemerintah Lanjutan',
                'slug'        => 'pbj-lanjutan',
                'summary'     => 'Program lanjutan PBJ.',
                'description' => 'Program lanjutan untuk meningkatkan kompetensi pengelolaan pengadaan strategis.',
                'status'      => 'published',
                'visibility'  => 'public',
                'featured'    => 0,
                'created_by'  => $operator['id'],
                'updated_by'  => $operator['id'],
                'published_by'=> $operator['id'],
                'published_at'=> $now,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]),
        ];

        // Course specs (4 per program)
        $courseSpecs = [
            'dasar' => [
                ['title' => 'Pengantar Pengadaan Barang/Jasa Pemerintah', 'slug' => 'pbj-dasar-pengantar',            'level' => 'basic'],
                ['title' => 'Perencanaan Pengadaan Pemerintah',           'slug' => 'pbj-dasar-perencanaan',          'level' => 'basic'],
                ['title' => 'Pemilihan Penyedia Barang/Jasa',             'slug' => 'pbj-dasar-pemilihan',            'level' => 'intermediate'],
                ['title' => 'Kontrak dan Pelaksanaan Pengadaan',          'slug' => 'pbj-dasar-kontrak-pelaksanaan',  'level' => 'intermediate'],
            ],
            'lanjutan' => [
                ['title' => 'Strategi Pengadaan Modern',                   'slug' => 'pbj-lanjutan-strategi-modern',   'level' => 'advanced'],
                ['title' => 'Manajemen Risiko Pengadaan',                 'slug' => 'pbj-lanjutan-manajemen-risiko',   'level' => 'advanced'],
                ['title' => 'Monitoring dan Evaluasi Kontrak',            'slug' => 'pbj-lanjutan-monitoring-evaluasi','level' => 'advanced'],
                ['title' => 'Pengadaan Berkelanjutan',                     'slug' => 'pbj-lanjutan-berkelanjutan',      'level' => 'advanced'],
            ],
        ];

        $courseIds = [];
        foreach ($courseSpecs as $programKey => $specs) {
            foreach ($specs as $spec) {
                $courseIds[$programKey . ':' . $spec['slug']] = $this->ensureCourse([
                    'program_id'   => $programs[$programKey]['id'],
                    'title'        => $spec['title'],
                    'slug'         => $spec['slug'],
                    'summary'      => $spec['title'],
                    'description'  => 'Course ' . $spec['title'],
                    'level'        => $spec['level'],
                    'duration_minutes' => 240,
                    'status'       => 'published',
                    'visibility'   => 'public',
                    'created_by'   => $operator['id'],
                    'updated_by'   => $operator['id'],
                    'published_by' => $operator['id'],
                    'published_at' => $now,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);
            }
        }

        // 24 Kelas (3 per course) + module/lesson/quiz
        $start = new \DateTime('2026-01-05');
        $classes = [];
        $i = 0;
        foreach ($courseSpecs as $programKey => $specs) {
            foreach ($specs as $spec) {
                $courseId = $courseIds[$programKey . ':' . $spec['slug']]['id'];
                for ($k = 1; $k <= 3; $k++) {
                    $i++;
                    $classStart = (clone $start)->modify('+' . ($i * 7) . ' days');
                    $classEnd   = (clone $classStart)->modify('+5 weeks');
                    $code = sprintf('CLS-%s-%02d', strtoupper(substr($spec['slug'], -10)), $k);
                    $name = sprintf('%s — Kelas %d', $spec['title'], $k);

                    $class = $this->ensureClass($courseId, $code, [
                        'name'               => $name,
                        'status'             => 'active',
                        'registration_open_at' => (clone $classStart)->modify('-2 weeks')->format('Y-m-d H:i:s'),
                        'registration_close_at'=> $classStart->format('Y-m-d H:i:s'),
                        'start_at'           => $classStart->format('Y-m-d H:i:s'),
                        'end_at'             => $classEnd->format('Y-m-d H:i:s'),
                        'capacity'           => 40,
                        'certificate_enabled'=> 1,
                        'passing_grade'      => 70.00,
                        'quiz_weight'        => 40.00,
                        'assignment_weight'  => 0.00,
                        'post_test_weight'   => 60.00,
                        'created_by'         => $operator['id'],
                        'updated_by'         => $operator['id'],
                        'created_at'         => $now,
                        'updated_at'         => $now,
                    ]);

                    // Assign trainer — round-robin 3 trainer
                    $trainerOrder = ['trainer.budi', 'trainer.siti', 'trainer.ahmad'];
                    $trainerUser = $trainers[$trainerOrder[$i % 3]];
                    $this->ensureClassTrainer($class['id'], $trainerUser['id'], $operator['id'], $now);

                    // 3 Module × 1 Lesson each
                    $moduleTitles = $this->moduleTitlesFor($spec['title'], $k);
                    $lessonIds = [];
                    foreach ($moduleTitles as $order => $moduleTitle) {
                        $module = $this->ensureModule($class['id'], $order + 1, [
                            'title'       => $moduleTitle,
                            'description' => 'Modul ' . ($order + 1) . ' pada ' . $name,
                            'sort_order'  => $order + 1,
                            'is_required' => 1,
                            'status'      => 'published',
                            'created_at'  => $now,
                            'updated_at'  => $now,
                        ]);
                        $lessonTitle = $this->lessonTitleFor($moduleTitle);
                        $lesson = $this->ensureLesson($module['id'], 1, [
                            'title'        => $lessonTitle,
                            'slug'         => url_title($lessonTitle, '-', true) . '-cls-' . $class['id'],
                            'content_type' => 'text',
                            'content'      => 'Materi ' . $lessonTitle . ' membahas konsep dan penerapan pada konteks PBJ: ' . $moduleTitle . '.',
                            'sort_order'   => 1,
                            'is_required'  => 1,
                            'status'       => 'published',
                            'visibility'   => 'internal',
                            'created_by'   => $trainerUser['id'],
                            'updated_by'   => $trainerUser['id'],
                            'created_at'   => $now,
                            'updated_at'   => $now,
                        ]);
                        $lessonIds[$order] = $lesson['id'];
                    }

                    // Quiz: 1 pre_test (10 soal), 3 lesson_quiz (10 soal each, tied ke lesson), 1 post_test (10 soal)
                    $this->buildQuiz($class['id'], null, 'pre_test', 'Pre-test ' . $name, 10, false, $now);
                    foreach ($lessonIds as $idx => $lid) {
                        $this->buildQuiz($class['id'], $lid, 'lesson_quiz', sprintf('Quiz Modul %d — %s', $idx + 1, $name), 10, true, $now);
                    }
                    $this->buildQuiz($class['id'], null, 'post_test', 'Post-test ' . $name, 10, true, $now);

                    $classes[$i] = $class;
                }
            }
        }

        // Enrollment
        // Andi → 2 kelas aktif
        $this->ensureEnrollment($classes[1]['id'], $peserta['peserta.andi']['id'], 'active', $now);
        $this->ensureEnrollment($classes[2]['id'], $peserta['peserta.andi']['id'], 'active', $now);
        // Rina → 1 aktif + 1 pending
        $this->ensureEnrollment($classes[3]['id'], $peserta['peserta.rina']['id'], 'active', $now);
        $this->ensureEnrollment($classes[4]['id'], $peserta['peserta.rina']['id'], 'pending', $now);
        // Dewi → 1 pending
        $this->ensureEnrollment($classes[5]['id'], $peserta['peserta.dewi']['id'], 'pending', $now);
        // Fajar → 1 cancelled
        $this->ensureEnrollment($classes[6]['id'], $peserta['peserta.fajar']['id'], 'cancelled', $now);
        // Agus: tidak ada enrollment

        echo "LMS Demo Seeder selesai. Total classes: " . count($classes) . PHP_EOL;
    }

    private function ensureUser(array $data): array
    {
        $row = $this->db->table('users')->where('email', $data['email'])->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $data['password_hash'] = $this->passwordHash;
        $data['session_version'] = 1;
        $this->db->table('users')->insert($data);
        return $this->db->table('users')->where('email', $data['email'])->get()->getRowArray();
    }

    private function ensureRole(string $key, string $name): int
    {
        $row = $this->db->table('roles')->where('`key`', $key)->get()->getRowArray();
        if ($row) {
            return (int) $row['id'];
        }
        $this->db->table('roles')->insert([
            'key'         => $key,
            'name'        => $name,
            'description' => $name,
            'is_system'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
        return (int) $this->db->insertID();
    }

    private function ensureUserRole(int $userId, int $roleId, string $now): void
    {
        $exists = $this->db->table('user_roles')
            ->where(['user_id' => $userId, 'role_id' => $roleId])
            ->get()->getRowArray();
        if ($exists) {
            return;
        }
        $this->db->table('user_roles')->insert([
            'user_id'    => $userId,
            'role_id'    => $roleId,
            'status'     => 'active',
            'granted_by' => null,
            'granted_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function ensureProgram(array $data): array
    {
        $row = $this->db->table('programs')->where('slug', $data['slug'])->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $this->db->table('programs')->insert($data);
        return $this->db->table('programs')->where('slug', $data['slug'])->get()->getRowArray();
    }

    private function ensureCourse(array $data): array
    {
        $row = $this->db->table('courses')->where('slug', $data['slug'])->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $this->db->table('courses')->insert($data);
        return $this->db->table('courses')->where('slug', $data['slug'])->get()->getRowArray();
    }

    private function ensureClass(int $courseId, string $code, array $data): array
    {
        $row = $this->db->table('learning_classes')
            ->where(['course_id' => $courseId, 'code' => $code])
            ->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $data['course_id'] = $courseId;
        $data['code']      = $code;
        $this->db->table('learning_classes')->insert($data);
        return $this->db->table('learning_classes')
            ->where(['course_id' => $courseId, 'code' => $code])
            ->get()->getRowArray();
    }

    private function ensureClassTrainer(int $classId, int $userId, int $assignedBy, string $now): void
    {
        $exists = $this->db->table('class_trainers')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if ($exists) {
            return;
        }
        $this->db->table('class_trainers')->insert([
            'class_id'    => $classId,
            'user_id'     => $userId,
            'assigned_by' => $assignedBy,
            'assigned_at' => $now,
            'status'      => 'active',
        ]);
    }

    private function ensureModule(int $classId, int $sortOrder, array $data): array
    {
        $row = $this->db->table('learning_modules')
            ->where(['class_id' => $classId, 'sort_order' => $sortOrder])
            ->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $data['class_id']   = $classId;
        $data['sort_order'] = $sortOrder;
        $this->db->table('learning_modules')->insert($data);
        return $this->db->table('learning_modules')
            ->where(['class_id' => $classId, 'sort_order' => $sortOrder])
            ->get()->getRowArray();
    }

    private function ensureLesson(int $moduleId, int $sortOrder, array $data): array
    {
        $row = $this->db->table('lessons')
            ->where(['module_id' => $moduleId, 'sort_order' => $sortOrder])
            ->get()->getRowArray();
        if ($row) {
            return $row;
        }
        $data['module_id'] = $moduleId;
        $data['sort_order']= $sortOrder;
        $this->db->table('lessons')->insert($data);
        return $this->db->table('lessons')
            ->where(['module_id' => $moduleId, 'sort_order' => $sortOrder])
            ->get()->getRowArray();
    }

    private function ensureEnrollment(int $classId, int $userId, string $status, string $now): void
    {
        $exists = $this->db->table('enrollments')
            ->where(['class_id' => $classId, 'user_id' => $userId])
            ->get()->getRowArray();
        if ($exists) {
            return;
        }
        $payload = [
            'class_id'    => $classId,
            'user_id'     => $userId,
            'status'      => $status,
            'enrolled_at' => $now,
            'created_at'  => $now,
            'updated_at'  => $now,
        ];
        if ($status === 'active') {
            $payload['activated_at'] = $now;
        } elseif ($status === 'cancelled') {
            $payload['cancelled_at'] = $now;
        }
        $this->db->table('enrollments')->insert($payload);
    }

    private function buildQuiz(int $classId, ?int $lessonId, string $type, string $title, int $questionCount, bool $required, string $now): void
    {
        $existing = $this->db->table('quizzes')
            ->where(['class_id' => $classId, 'type' => $type, 'title' => $title])
            ->get()->getRowArray();
        if ($existing) {
            return;
        }
        $this->db->table('quizzes')->insert([
            'class_id'      => $classId,
            'lesson_id'     => $lessonId,
            'type'          => $type,
            'title'         => $title,
            'instructions'  => 'Pilih satu jawaban paling tepat.',
            'attempt_limit' => 1,
            'is_required'   => $required ? 1 : 0,
            'status'        => 'published',
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
        $quizId = (int) $this->db->insertID();

        for ($order = 1; $order <= $questionCount; $order++) {
            $q = $this->makeQuestion($type, $order);
            $this->db->table('quiz_questions')->insert([
                'quiz_id'    => $quizId,
                'type'       => 'multiple_choice',
                'prompt'     => $q['prompt'],
                'points'     => 1.00,
                'sort_order' => $order,
                'explanation'=> $q['explanation'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $questionId = (int) $this->db->insertID();
            foreach ($q['options'] as $idx => $text) {
                $this->db->table('quiz_options')->insert([
                    'question_id' => $questionId,
                    'option_text' => $text,
                    'is_correct'  => $idx === $q['correct'] ? 1 : 0,
                    'sort_order'  => $idx + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }

    private function makeQuestion(string $type, int $order): array
    {
        $bank = [
            'pre_test' => [
                ['prompt' => 'Apakah tujuan utama Peraturan Presiden Nomor 12 Tahun 2021?', 'options' => ['Mengatur tata cara pengadaan barang/jasa pemerintah', 'Menetapkan anggaran belanja negara', 'Mengawasi kinerja ASN', 'Mengelola utang negara'], 'correct' => 0, 'explanation' => 'Perpres 12/2021 mengatur pengadaan barang/jasa pemerintah.'],
                ['prompt' => 'Lembaga yang berwenang menetapkan kebijakan pengadaan barang/jasa adalah?', 'options' => ['LKPP', 'BPKP', 'BPK', 'BI'], 'correct' => 0, 'explanation' => 'LKPP menyelenggarakan kebijakan di bidang PBJ.'],
                ['prompt' => 'Apa yang dimaksud dengan RUP?', 'options' => ['Rencana Umum Pengadaan', 'Rencana Usaha Pemerintah', 'Rasio Utang Publik', 'Registrasi Unit Penyedia'], 'correct' => 0, 'explanation' => 'RUP adalah Rencana Umum Pengadaan.'],
                ['prompt' => 'Metode pemilihan penyedia yang utama untuk tender di atas Rp5 miliar adalah?', 'options' => ['Seleksi', 'Tender', 'e-Purchasing', 'Penunjukan langsung'], 'correct' => 1, 'explanation' => 'Tender adalah metode utama untuk nilai besar.'],
                ['prompt' => 'Pejabat yang bertanggung jawab atas proses PBJ di K/L/PD adalah?', 'options' => ['PA/KPA', 'PPK', 'Pokja', 'Bendahara'], 'correct' => 0, 'explanation' => 'PA/KPA bertanggung jawab pada proses PBJ.'],
                ['prompt' => 'Dokumen yang menjadi dasar penyusunan kontrak adalah?', 'options' => ['RKAP', 'RKA-KL', 'BA Penetapan', 'Surat Permintaan'], 'correct' => 2, 'explanation' => 'Berita Acara Penetapan digunakan sebagai dasar kontrak.'],
                ['prompt' => 'Jaminan penawaran disediakan oleh?', 'options' => ['Penyedia', 'PPK', 'Pokja', 'Bendahara'], 'correct' => 0, 'explanation' => 'Jaminan penawaran disediakan oleh peserta pemilihan.'],
                ['prompt' => 'Yang bukan tahapan tender adalah?', 'options' => ['Pengumuman', 'Pendaftaran', 'Audit BPK', 'Evaluasi'], 'correct' => 2, 'explanation' => 'Audit BPK bukan bagian dari tahapan tender PBJ.'],
                ['prompt' => 'HPS bermanfaat untuk?', 'options' => ['Acuan harga sah penawaran', 'Bukti audit', 'Dasar pembayaran', 'Acuan sanksi'], 'correct' => 0, 'explanation' => 'HPS menjadi acuan kewajaran harga.'],
                ['prompt' => 'Tender gagal dapat diulang dengan metode?', 'options' => ['Tender ulang atau seleksi', 'Penunjukan langsung tanpa syarat', 'e-Purchasing', 'Swakelola'], 'correct' => 0, 'explanation' => 'Tender gagal dapat diulang dengan metode tender ulang atau seleksi.'],
            ],
            'lesson_quiz' => [
                ['prompt' => 'Langkah pertama dalam identifikasi kebutuhan adalah?', 'options' => ['Analisis kebutuhan pengguna', 'Memesan barang', 'Menunjuk penyedia', 'Mencatat anggaran'], 'correct' => 0, 'explanation' => 'Identifikasi kebutuhan dimulai dari analisis kebutuhan pengguna.'],
                ['prompt' => 'Metode pemilihan yang digunakan untuk jasa konsultansi adalah?', 'options' => ['Seleksi', 'Tender', 'Penunjukan langsung', 'e-Purchasing'], 'correct' => 0, 'explanation' => 'Seleksi adalah metode untuk jasa konsultansi.'],
                ['prompt' => 'Dokumen kontrak setelah penandatangan harus?', 'options' => ['Disimpan oleh PPK', 'Diumumkan publik', 'Diserahkan ke BPK', 'Dihapus'], 'correct' => 0, 'explanation' => 'PPK menyimpan dokumen kontrak.'],
                ['prompt' => 'Risiko utama dalam kontrak PBJ adalah?', 'options' => ['Keterlambatan penyelesaian', 'Terlalu banyak penyedia', 'Terlalu sedikit dana', 'Tidak ada HPS'], 'correct' => 0, 'explanation' => 'Keterlambatan penyelesaian adalah risiko klasik kontrak PBJ.'],
                ['prompt' => 'Pemantauan kontrak dilakukan oleh?', 'options' => ['PPTK/PPK', 'Bendahara', 'Pokja', 'Auditor independen'], 'correct' => 0, 'explanation' => 'Pemantauan kontrak oleh PPTK/PPK.'],
                ['prompt' => 'Bukti pembayaran sah harus?', 'options' => ['Sesuai kontrak & berita acara', 'Diisi manual', 'Tidak perlu bukti', 'Dilaporkan ke publik'], 'correct' => 0, 'explanation' => 'Pembayaran harus sesuai kontrak dan berita acara.'],
                ['prompt' => 'Kontrak payung digunakan untuk?', 'options' => ['Pengadaan berulang dalam periode', 'Pengadaan darurat', 'Pengadaan kecil', 'Pengadaan jasa'], 'correct' => 0, 'explanation' => 'Kontrak payung untuk kebutuhan berulang.'],
                ['prompt' => 'Serah terima pekerjaan harus?', 'options' => ['Dibuat berita acara', 'Dilaporkan ke BPKP', 'Diumumkan', 'Dihapus'], 'correct' => 0, 'explanation' => 'Serah terima dibuat berita acara.'],
                ['prompt' => 'Yang bukan sanksi bagi penyedia?', 'options' => ['Penghargaan', 'Daftar hitam', 'Denda', 'Pemutusan kontrak'], 'correct' => 0, 'explanation' => 'Penghargaan bukan sanksi.'],
                ['prompt' => 'Pemutusan kontrak dapat dilakukan jika?', 'options' => ['Penyedia wanprestasi berat', 'Penyedia terlambat bayar', 'PPK salah hitung', 'PPK cuti'], 'correct' => 0, 'explanation' => 'Wanprestasi berat menjadi dasar pemutusan kontrak.'],
            ],
            'post_test' => [
                ['prompt' => 'Penilaian akhir peserta menggunakan bobot post-test sebesar?', 'options' => ['40%', '50%', '60%', '100%'], 'correct' => 2, 'explanation' => 'Sesuai schema, post_test_weight=60.00.'],
                ['prompt' => 'Standar kelulusan mengikuti?', 'options' => ['Nilai akhir >= passing_grade', 'Selalu 80', 'Selalu 50', 'Tidak ada'], 'correct' => 0, 'explanation' => 'Kelulusan berdasarkan passing_grade.'],
                ['prompt' => 'Setelah enrollment aktif, peserta dapat?', 'options' => ['Mengakses learning room', 'Langsung lulus', 'Membuat course', 'Mengatur jadwal trainer'], 'correct' => 0, 'explanation' => 'Akses learning room terbuka setelah enrollment aktif.'],
                ['prompt' => 'Quiz wajib harus?', 'options' => ['Diselesaikan sampai graded', 'Boleh dilewati', 'Tidak dihitung', 'Otomatis lulus'], 'correct' => 0, 'explanation' => 'Quiz wajib harus diselesaikan sampai graded.'],
                ['prompt' => 'Auto grading menilai jawaban berdasarkan?', 'options' => ['quiz_options.is_correct', 'Nama peserta', 'Urutan pengerjaan', 'Waktu pengerjaan'], 'correct' => 0, 'explanation' => 'Auto grading membaca is_correct.'],
                ['prompt' => 'Sertifikat hanya diterbitkan jika?', 'options' => ['enrollment.passed_at terisi', 'Peserta login', 'Quiz selesai', 'Trainer acc'], 'correct' => 0, 'explanation' => 'passed_at menjadi syarat sertifikat.'],
                ['prompt' => 'Batas attempt sebuah quiz ditentukan oleh?', 'options' => ['attempt_limit', 'Kelas', 'Trainer', 'Peserta'], 'correct' => 0, 'explanation' => 'attempt_limit menentukan batas attempt.'],
                ['prompt' => 'Mengerjakan post-test memicu?', 'options' => ['evaluateCompletion', 'Pendaftaran ulang', 'Pembatalan otomatis'], 'correct' => 0, 'explanation' => 'submitQuiz post-test memicu evaluateCompletion.'],
                ['prompt' => 'Completion berlaku bila?', 'options' => ['Seluruh lesson wajib selesai dan graded', 'Hanya 1 lesson', 'Login saja', 'Trainer menyetujui'], 'correct' => 0, 'explanation' => 'Completion butuh seluruh lesson wajib dan graded.'],
                ['prompt' => 'Setelah evaluateCompletion, status enrollment menjadi?', 'options' => ['completed', 'pending', 'cancelled', 'suspended'], 'correct' => 0, 'explanation' => 'Service set status=completed.'],
            ],
        ];
        $bucket = $bank[$type] ?? $bank['lesson_quiz'];
        return $bucket[($order - 1) % count($bucket)];
    }

    private function moduleTitlesFor(string $courseTitle, int $classIdx): array
    {
        $map = [
            'Pengantar Pengadaan Barang/Jasa Pemerintah' => [
                'Konsep Dasar PBJ',
                'Regulasi PBJ',
                'Pelaku Pengadaan',
            ],
            'Perencanaan Pengadaan Pemerintah' => [
                'Identifikasi Kebutuhan dan Penyusunan RUP',
                'Penyusunan HPS',
                'Strategi Pengadaan Tahunan',
            ],
            'Pemilihan Penyedia Barang/Jasa' => [
                'Metode Pemilihan Penyedia',
                'Dokumen dan Evaluasi Penawaran',
                'Penetapan dan Pengumuman Pemenang',
            ],
            'Kontrak dan Pelaksanaan Pengadaan' => [
                'Penyusunan Kontrak',
                'Pelaksanaan Kontrak',
                'Serah Terima dan Pembayaran',
            ],
            'Strategi Pengadaan Modern' => [
                'Digitalisasi Pengadaan',
                'Konsolidasi Pengadaan',
                'Pengadaan Inovatif',
            ],
            'Manajemen Risiko Pengadaan' => [
                'Identifikasi Risiko PBJ',
                'Mitigasi Risiko',
                'Pemantauan Risiko',
            ],
            'Monitoring dan Evaluasi Kontrak' => [
                'Indikator Kinerja Kontrak',
                'Pemantauan Pelaksanaan',
                'Evaluasi dan Pelaporan',
            ],
            'Pengadaan Berkelanjutan' => [
                'Prinsip Pengadaan Berkelanjutan',
                'Kriteria Lingkungan Sosial',
                'Pengadaan Hijau',
            ],
        ];
        return $map[$courseTitle] ?? ['Modul 1', 'Modul 2', 'Modul 3'];
    }

    private function lessonTitleFor(string $moduleTitle): string
    {
        return 'Pengantar ' . $moduleTitle;
    }
}