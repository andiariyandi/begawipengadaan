<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ArenaDemoSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'arena:seed:demo';
    protected $description = 'Seed authentic Indonesian PBJ demo class (Pengantar Pengadaan Barang dan Jasa), trainer, operator, participants, quizzes, and assignment with PDF guide.';
    protected $usage       = 'arena:seed:demo';

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        CLI::write("==================================================================", "cyan");
        CLI::write("    ARENA BELAJAR LMS — SEEDING AUTHENTIC PBJ DEMO ENVIRONMENT   ", "yellow");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        // -------------------------------------------------------------
        // STEP 1: ROLES & PERMISSIONS CHECK
        // -------------------------------------------------------------
        CLI::write(">>> [1/7] Ensuring Required Roles Exist...", "yellow");
        $requiredRoles = [
            ['key' => 'super_administrator', 'name' => 'Super Administrator', 'description' => 'Akses penuh seluruh modul sistem'],
            ['key' => 'administrator',       'name' => 'Administrator PBJ',   'description' => 'Akses manajerial modul pengadaan'],
            ['key' => 'operator',            'name' => 'Operator LMS',        'description' => 'Operator pengelola kelas & kurikulum LMS'],
            ['key' => 'trainer',             'name' => 'Trainer / Instruktur','description' => 'Fasilitator pengajar dan penilai tugas'],
            ['key' => 'peserta',             'name' => 'Peserta Pelatihan',   'description' => 'Pengguna umum / peserta pembelajaran'],
        ];

        $roleIds = [];
        foreach ($requiredRoles as $r) {
            $existing = $db->table('roles')->where('key', $r['key'])->get()->getRowArray();
            if ($existing) {
                $roleIds[$r['key']] = (int) $existing['id'];
            } else {
                $db->table('roles')->insert([
                    'key'         => $r['key'],
                    'name'        => $r['name'],
                    'description' => $r['description'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $roleIds[$r['key']] = (int) $db->insertID();
            }
        }
        CLI::write("  [OK] Roles verified: " . implode(', ', array_keys($roleIds)), "green");

        // -------------------------------------------------------------
        // STEP 2: USERS SEEDING (TRAINER, OPERATOR, PESERTA)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [2/7] Seeding Authentic Users...", "yellow");
        $defaultPasswordHash = password_hash('Password123!', PASSWORD_BCRYPT);

        $demoUsers = [
            [
                'role_key' => 'trainer',
                'email'    => 'trainer.pbj@lampungprov.go.id',
                'username' => 'trainer.pbj',
                'name'     => 'Dr. Hendra Gunawan, S.T., M.T.',
                'title'    => 'Fasilitator Utama PBJ Pemerintah (TOC Certified)',
                'nip'      => '197805122002121003',
                'inst'     => 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
            ],
            [
                'role_key' => 'operator',
                'email'    => 'operator.lms@lampungprov.go.id',
                'username' => 'operator.lms',
                'name'     => 'Rina Wijaya, S.Kom.',
                'title'    => 'Operator Kurikulum & Administrasi LMS',
                'nip'      => '198904202012012005',
                'inst'     => 'Biro Pengadaan Barang dan Jasa Provinsi Lampung',
            ],
            [
                'role_key' => 'peserta',
                'email'    => 'ani.peserta@lampungprov.go.id',
                'username' => 'ani.peserta',
                'name'     => 'Ani Lestari, S.AP.',
                'title'    => 'Pejabat Pengadaan Muda (Status: Aktif)',
                'nip'      => '199208152015022001',
                'inst'     => 'Dinas Bina Marga dan Bina Konstruksi Provinsi Lampung',
                'enroll_status' => 'active',
                'final_score'   => null,
                'progress_pct'  => 50,
            ],
            [
                'role_key' => 'peserta',
                'email'    => 'budi.peserta@lampungprov.go.id',
                'username' => 'budi.peserta',
                'name'     => 'Budi Pratama, S.E.',
                'title'    => 'Staf Perencanaan Pengadaan (Status: Aktif)',
                'nip'      => '199011032014031002',
                'inst'     => 'Dinas Pendidikan dan Kebudayaan Provinsi Lampung',
                'enroll_status' => 'active',
                'final_score'   => null,
                'progress_pct'  => 0,
            ],
            [
                'role_key' => 'peserta',
                'email'    => 'citra.peserta@lampungprov.go.id',
                'username' => 'citra.peserta',
                'name'     => 'Citra Dewi, S.H.',
                'title'    => 'Analis Hukum Kontrak (Status: Pending)',
                'nip'      => '199503252019012004',
                'inst'     => 'Biro Hukum Setda Provinsi Lampung',
                'enroll_status' => 'pending',
                'final_score'   => null,
                'progress_pct'  => 0,
            ],
            [
                'role_key' => 'peserta',
                'email'    => 'doni.peserta@lampungprov.go.id',
                'username' => 'doni.peserta',
                'name'     => 'Doni Kusuma, S.T.',
                'title'    => 'Pengawas Lapangan (Status: Dikeluarkan / Withdrawn)',
                'nip'      => '198807192011011006',
                'inst'     => 'Dinas Pengelolaan Sumber Daya Air Provinsi Lampung',
                'enroll_status' => 'withdrawn',
                'final_score'   => null,
                'progress_pct'  => 25,
            ],
        ];

        $userIds = [];
        foreach ($demoUsers as $u) {
            $userRow = $db->table('users')->where('email', $u['email'])->get()->getRowArray();
            if (! $userRow) {
                $db->table('users')->insert([
                    'name'           => $u['name'],
                    'email'          => $u['email'],
                    'username'       => $u['username'],
                    'password_hash'  => $defaultPasswordHash,
                    'account_status' => 'active',
                    'profile_type'   => 'asn',
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ]);
                $uId = (int) $db->insertID();
                CLI::write("  [CREATED] User: {$u['name']} ({$u['email']})", "green");
            } else {
                $uId = (int) $userRow['id'];
                $db->table('users')->where('id', $uId)->update([
                    'name'           => $u['name'],
                    'username'       => $u['username'],
                    'account_status' => 'active',
                    'updated_at'     => $now,
                ]);
                CLI::write("  [EXISTING] User: {$u['name']} (ID {$uId})", "cyan");
            }
            $userIds[$u['username']] = $uId;

            // User Role
            $roleId = $roleIds[$u['role_key']] ?? $roleIds['peserta'];
            $existingUr = $db->table('user_roles')
                ->where('user_id', $uId)
                ->where('role_id', $roleId)
                ->get()->getRowArray();
            if (! $existingUr) {
                $db->table('user_roles')->insert([
                    'user_id'    => $uId,
                    'role_id'    => $roleId,
                    'status'     => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // User Profile
            $existingProfile = $db->table('user_profiles')->where('user_id', $uId)->get()->getRowArray();
            if (! $existingProfile) {
                $db->table('user_profiles')->insert([
                    'user_id'                   => $uId,
                    'address_line'              => 'Bandar Lampung, Provinsi Lampung',
                    'province_code'             => '18',
                    'regency_code'              => '18.71',
                    'district_code'             => '18.71.01',
                    'profile_completion_status' => 'complete',
                    'created_at'                => $now,
                    'updated_at'                => $now,
                ]);
            }

            // ASN Profile
            $existingAsn = $db->table('asn_profiles')->where('user_id', $uId)->get()->getRowArray();
            if (! $existingAsn) {
                $db->table('asn_profiles')->insert([
                    'user_id'             => $uId,
                    'employee_identifier' => $u['nip'],
                    'employment_status'   => 'pns',
                    'origin_type'         => 'pemprov_lampung',
                    'institution_name'    => 'Pemerintah Provinsi Lampung',
                    'unit_name'           => $u['inst'],
                    'position_name'       => $u['title'],
                    'official_email'      => $u['email'],
                    'created_at'          => $now,
                    'updated_at'          => $now,
                ]);
            }
        }

        // -------------------------------------------------------------
        // STEP 3: PROGRAM & COURSE SEEDING
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [3/7] Seeding Program & Course (Pengantar PBJ - 24 JP)...", "yellow");

        $programSlug = 'pengembangan-kompetensi-pbj';
        $programRow = $db->table('programs')->where('slug', $programSlug)->get()->getRowArray();
        if (! $programRow) {
            $db->table('programs')->insert([
                'title'               => 'Program Pengembangan Kompetensi PBJ Pemerintah',
                'slug'                => $programSlug,
                'summary'             => 'Program sertifikasi terpadu peningkatan kapasitas SDM Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung.',
                'description'         => 'Program sertifikasi terpadu peningkatan kapasitas sumber daya manusia Pengadaan Barang dan Jasa Pemerintah Provinsi Lampung mengacu pada standar kompetensi LKPP RI.',
                'target_audience'     => "Pejabat Pembuat Komitmen (PPK)\nKelompok Kerja (Pokja) Pemilihan UKPBJ\nPejabat Pengadaan (PP)\nAparatur Pengawas Internal Pemerintah (APIP)",
                'prerequisites'       => "Aparatur Sipil Negara (PNS/PPPK) atau Pegawai BUMD/BLUD aktif di lingkungan Pemprov Lampung.",
                'learning_objectives' => "Memahami kerangka regulasi Perpres 16/2018 jo Perpres 12/2021 beserta aturan turunannya.\nMampu menyusun dokumen perencanaan, HPS, dan spesifikasi teknis yang akuntabel.\nMenguasai tata cara pemilihan penyedia secara elektronik dan mitigasi risiko sanggah.",
                'status'              => 'published',
                'visibility'          => 'public',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
            $programId = (int) $db->insertID();
            CLI::write("  [CREATED] Program: Program Pengembangan Kompetensi PBJ Pemerintah (ID {$programId})", "green");
        } else {
            $programId = (int) $programRow['id'];
            $db->table('programs')->where('id', $programId)->update(['status' => 'published', 'visibility' => 'public', 'updated_at' => $now]);
            CLI::write("  [EXISTING] Program: Program Pengembangan Kompetensi PBJ Pemerintah (ID {$programId})", "cyan");
        }

        $courseSlug = 'pengantar-pbj';
        $courseRow = $db->table('courses')->where('slug', $courseSlug)->get()->getRowArray();
        $courseData = [
            'program_id'          => $programId,
            'title'               => 'Pengantar Pengadaan Barang dan Jasa Pemerintah',
            'slug'                => $courseSlug,
            'summary'             => 'Pelatihan dasar pengenalan ekosistem pengadaan barang dan jasa pemerintah (24 JP).',
            'level'               => 'dasar',
            'duration_minutes'    => 1440, // 24 JP (1 JP = 60 menit)
            'description'         => 'Pelatihan dasar pengenalan ekosistem pengadaan barang dan jasa pemerintah. Membahas prinsip, etika, para pihak, tata cara perencanaan, hingga serah terima hasil pekerjaan.',
            'target_audience'     => "ASN Calon Pengelola Pengadaan\nPPK dan Pejabat Pengadaan Pemula\nStaf Teknis Pendukung Pengadaan",
            'prerequisites'       => "Tidak ada prasyarat khusus (terbuka untuk seluruh jenjang aparatur).",
            'learning_objectives' => "Memahami definisi, ruang lingkup, dan prinsip dasar PBJ.\nMengenali peran dan wewenang para pelaku pengadaan.\nMampu membaca dan menelaah struktur dokumen pengadaan.",
            'status'              => 'published',
            'visibility'          => 'public',
            'created_at'          => $now,
            'updated_at'          => $now,
        ];

        if (! $courseRow) {
            $db->table('courses')->insert($courseData);
            $courseId = (int) $db->insertID();
            CLI::write("  [CREATED] Course: Pengantar PBJ (ID {$courseId}, 24 JP)", "green");
        } else {
            $courseId = (int) $courseRow['id'];
            $db->table('courses')->where('id', $courseId)->update(array_merge($courseData, ['updated_at' => $now]));
            CLI::write("  [UPDATED] Course: Pengantar PBJ (ID {$courseId}, 24 JP)", "cyan");
        }

        // -------------------------------------------------------------
        // STEP 4: CLASS / BATCH SEEDING (WITH DYNAMIC BATCH STAGES)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [4/7] Seeding Class Batch 1 with Dynamic Batch Stages...", "yellow");

        $batchStagesJson = json_encode([
            'stages' => [
                [
                    'step'        => 1,
                    'title'       => 'Orientasi & Pre-Test',
                    'description' => 'Pengenalan silabus dan asesmen diagnostik awal guna memetakan pemahaman dasar peserta sebelum memasuki modul materi.',
                ],
                [
                    'step'        => 2,
                    'title'       => 'Modul Pembelajaran & Kuis',
                    'description' => 'Mempelajari materi regulasi, studi kasus LKPP, video tutorial, serta menyelesaikan kuis formatif pada setiap modul.',
                ],
                [
                    'step'        => 3,
                    'title'       => 'Tugas Praktik & Evaluasi',
                    'description' => 'Menyusun dokumen praktikum (RUP, HPS, spesifikasi teknis, atau draft kontrak) yang dievaluasi langsung oleh instruktur fasilitator.',
                ],
                [
                    'step'        => 4,
                    'title'       => 'Post-Test & Penerbitan Sertifikat',
                    'description' => 'Ujian komprehensif penentu kelulusan. Peserta yang melampaui passing grade berhak mengunduh sertifikat resmi ber-QR Code verifikasi.',
                ],
            ],
            'subtitle' => 'Alur pengerjaan yang harus dilalui oleh peserta selama periode kelas berlangsung:',
        ], JSON_UNESCAPED_UNICODE);

        $classDescription = "Pelatihan resmi tingkat dasar Pengadaan Barang dan Jasa Pemerintah Batch 1 diselenggarakan oleh UKPBJ Pemerintah Provinsi Lampung terakreditasi LKPP.\n\n"
            . "<!--BATCH_STAGES:{$batchStagesJson}:BATCH_STAGES-->";

        $classCode = 'PBJ-2026-01';
        $classRow = $db->table('learning_classes')->where('code', $classCode)->get()->getRowArray();
        $classData = [
            'course_id'           => $courseId,
            'name'                => 'Pengantar Pengadaan Barang dan Jasa Pemerintah - Batch 1',
            'code'                => $classCode,
            'description'         => $classDescription,
            'learning_method'     => 'online',
            'location_or_link'    => 'Begawi LMS & Zoom Cloud Meetings UKPBJ',
            'target_audience'     => "Aparatur Sipil Negara (ASN), PPK, Pejabat Pengadaan, Pokja Pemilihan, dan staf pendukung unit kerja di lingkungan Pemprov Lampung.",
            'capacity'            => 60,
            'passing_grade'       => 75.00,
            'quiz_weight'         => 20.00,
            'assignment_weight'   => 20.00,
            'post_test_weight'    => 60.00,
            'certificate_enabled' => 1,
            'start_at'            => date('Y-m-d H:i:s', strtotime('-5 days')),
            'end_at'              => date('Y-m-d H:i:s', strtotime('+25 days')),
            'status'              => 'active',
            'created_at'          => $now,
            'updated_at'          => $now,
        ];

        if (! $classRow) {
            $db->table('learning_classes')->insert($classData);
            $classId = (int) $db->insertID();
            CLI::write("  [CREATED] Class: {$classData['name']} (ID {$classId}, Code: {$classCode})", "green");
        } else {
            $classId = (int) $classRow['id'];
            $db->table('learning_classes')->where('id', $classId)->update(array_merge($classData, ['updated_at' => $now]));
            CLI::write("  [UPDATED] Class: {$classData['name']} (ID {$classId}, Code: {$classCode})", "cyan");
        }

        // Additional Class 2: Bimtek Penyusunan HPS & Dokumen Pemilihan
        $class2Code = 'PBJ-2026-02';
        $class2Row  = $db->table('learning_classes')->where('code', $class2Code)->get()->getRowArray();
        $class2Data = [
            'course_id'           => $courseId,
            'name'                => 'Bimtek Penyusunan HPS & Dokumen Pemilihan - Batch 2',
            'code'                => $class2Code,
            'description'         => "Pelatihan intensif penyusunan Dokumen HPS, Spesifikasi Teknis, dan Rancangan Kontrak Kerja PBJ terakreditasi LKPP.\n\n<!--BATCH_STAGES:{$batchStagesJson}:BATCH_STAGES-->",
            'learning_method'     => 'online',
            'location_or_link'    => 'Begawi LMS & Zoom UKPBJ',
            'target_audience'     => 'PPK, Pokja Pemilihan, dan Pejabat Pengadaan se-Provinsi Lampung',
            'capacity'            => 80,
            'passing_grade'       => 75.00,
            'quiz_weight'         => 20.00,
            'assignment_weight'   => 20.00,
            'post_test_weight'    => 60.00,
            'certificate_enabled' => 1,
            'start_at'            => date('Y-m-d H:i:s', strtotime('+3 days')),
            'end_at'              => date('Y-m-d H:i:s', strtotime('+24 days')),
            'status'              => 'active',
            'created_at'          => $now,
            'updated_at'          => $now,
        ];
        if (! $class2Row) {
            $db->table('learning_classes')->insert($class2Data);
            $class2Id = (int) $db->insertID();
            CLI::write("  [CREATED] Class 2: {$class2Data['name']} (ID {$class2Id}, Code: {$class2Code})", "green");
        } else {
            $class2Id = (int) $class2Row['id'];
            $db->table('learning_classes')->where('id', $class2Id)->update(array_merge($class2Data, ['updated_at' => $now]));
            CLI::write("  [UPDATED] Class 2: {$class2Data['name']} (ID {$class2Id}, Code: {$class2Code})", "cyan");
        }

        // Trainer Assignment
        $trainerId = $userIds['trainer.pbj'] ?? null;
        if ($trainerId) {
            $existingTrainer = $db->table('class_trainers')
                ->where('class_id', $classId)
                ->where('user_id', $trainerId)
                ->get()->getRowArray();
            if (! $existingTrainer) {
                $db->table('class_trainers')->insert([
                    'class_id'    => $classId,
                    'user_id'     => $trainerId,
                    'assigned_by' => $trainerId,
                    'role'        => 'primary',
                    'status'      => 'active',
                    'assigned_at' => $now,
                ]);
                CLI::write("  [ASSIGNED] Trainer Dr. Hendra Gunawan to Class #{$classId} as Primary Trainer.", "green");
            }
        }

        // -------------------------------------------------------------
        // STEP 5: CURRICULUM SEEDING (PRE-TEST, MODULES, POST-TEST)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [5/7] Seeding Pre-Test, Modules, Quizzes, and Post-Test...", "yellow");

        // 1. Pre-Test
        $existingPreTest = $db->table('quizzes')
            ->where('class_id', $classId)
            ->where('type', 'pre_test')
            ->get()->getRowArray();

        if (! $existingPreTest) {
            $db->table('quizzes')->insert([
                'class_id'      => $classId,
                'lesson_id'     => null,
                'type'          => 'pre_test',
                'title'         => 'Pre-Test: Asesmen Diagnostik Awal Pengantar PBJ',
                'instructions'  => 'Ujian diagnostik awal untuk mengukur pemahaman awal Anda sebelum mempelajari materi regulasi.',
                'attempt_limit' => 2,
                'duration_minutes' => 30,
                'is_required'   => 1,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            $preTestId = (int) $db->insertID();

            // Sync with pre_tests table
            $db->table('pre_tests')->insert([
                'class_id'      => $classId,
                'is_enabled'    => 1,
                'attempt_limit' => 2,
                'status'        => 'published',
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
            CLI::write("  [CREATED] Pre-Test Quiz (ID {$preTestId})", "green");
        } else {
            $preTestId = (int) $existingPreTest['id'];
            CLI::write("  [EXISTING] Pre-Test Quiz (ID {$preTestId})", "cyan");
        }

        // Pre-Test Questions (5 authentic questions)
        $this->seedQuizQuestions($db, $preTestId, [
            [
                'prompt'      => 'Berdasarkan Perpres No. 16 Tahun 2018 jo Perpres No. 12 Tahun 2021, apa tujuan utama pengadaan barang/jasa pemerintah?',
                'points'      => 20.0,
                'explanation' => 'Tujuan PBJ antara lain menghasilkan barang/jasa tepat value for money, meningkatkan penggunaan produk dalam negeri, dan peran UMKM-Koperasi.',
                'options'     => [
                    ['text' => 'Mendapatkan keuntungan komersial sebesar-besarnya bagi instansi pemerintah', 'correct' => 0],
                    ['text' => 'Menghasilkan barang/jasa yang tepat dari setiap uang yang dibelanjakan diukur dari segi kualitas, jumlah, waktu, biaya, lokasi, dan penyedia', 'correct' => 1],
                    ['text' => 'Menghabiskan seluruh pagu anggaran DPA sebelum batas akhir tahun anggaran', 'correct' => 0],
                    ['text' => 'Membatasi persaingan usaha hanya bagi rekanan yang telah terdaftar di daerah', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Manakah dari opsi berikut yang TIDAK termasuk dalam 7 (tujuh) prinsip dasar pengadaan barang/jasa pemerintah?',
                'points'      => 20.0,
                'explanation' => 'Tujuh prinsip PBJ adalah: Efisien, Efektif, Transparan, Terbuka, Bersaing, Adil/Tidak Diskriminatif, dan Akuntabel.',
                'options'     => [
                    ['text' => 'Efisien dan Efektif', 'correct' => 0],
                    ['text' => 'Transparan dan Terbuka', 'correct' => 0],
                    ['text' => 'Birokratis dan Eksklusif', 'correct' => 1],
                    ['text' => 'Adil, Tidak Diskriminatif, dan Akuntabel', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Siapakah pelaku pengadaan yang memiliki kewenangan menyusun dan menetapkan Rencana Anggaran Biaya (RAB) dan Rencana Umum Pengadaan (RUP)?',
                'points'      => 20.0,
                'explanation' => 'Pengguna Anggaran (PA) atau Kuasa Pengguna Anggaran (KPA) berwenang menetapkan perencanaan pengadaan dan RUP.',
                'options'     => [
                    ['text' => 'Pengguna Anggaran (PA) / Kuasa Pengguna Anggaran (KPA)', 'correct' => 1],
                    ['text' => 'Pejabat Pembuat Komitmen (PPK)', 'correct' => 0],
                    ['text' => 'Pokja Pemilihan UKPBJ', 'correct' => 0],
                    ['text' => 'Penyedia Barang/Jasa', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Penyusunan Harga Perkiraan Sendiri (HPS) adalah tugas dan kewenangan dari:',
                'points'      => 20.0,
                'explanation' => 'Pasal 11 ayat (1) huruf a Perpres 16/2018 jo Perpres 12/2021 menegaskan bahwa PPK bertugas menyusun HPS.',
                'options'     => [
                    ['text' => 'Pokja Pemilihan UKPBJ', 'correct' => 0],
                    ['text' => 'Pejabat Pembuat Komitmen (PPK)', 'correct' => 1],
                    ['text' => 'Inspektorat Daerah / APIP', 'correct' => 0],
                    ['text' => 'Kepala Daerah', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Batas nilai maksimal pengadaan barang/jasa lainnya melalui metode Pengadaan Langsung oleh Pejabat Pengadaan adalah:',
                'points'      => 20.0,
                'explanation' => 'Pengadaan Langsung Barang/Pekerjaan Konstruksi/Jasa Lainnya bernilai paling banyak Rp200.000.000 (dua ratus juta rupiah).',
                'options'     => [
                    ['text' => 'Maksimal Rp 50.000.000 (lima puluh juta rupiah)', 'correct' => 0],
                    ['text' => 'Maksimal Rp 100.000.000 (seratus juta rupiah)', 'correct' => 0],
                    ['text' => 'Maksimal Rp 200.000.000 (dua ratus juta rupiah)', 'correct' => 1],
                    ['text' => 'Maksimal Rp 500.000.000 (lima ratus juta rupiah)', 'correct' => 0],
                ],
            ],
        ]);

        // 2. Modules & Lessons
        $modulesData = [
            [
                'title'       => 'Modul 1: Ketentuan Umum, Tujuan, dan Etika PBJ Pemerintah',
                'description' => 'Mempelajari ruang lingkup, definisi, 7 prinsip dasar, dan kode etik pengadaan barang/jasa pemerintah.',
                'sort_order'  => 1,
                'lessons'     => [
                    [
                        'title'        => 'Lesson 1.1: Ruang Lingkup dan Definisi Dasar PBJ',
                        'slug'         => 'lesson-ruang-lingkup-definisi-pbj',
                        'content_type' => 'text',
                        'content'      => "<h3>Ruang Lingkup PBJ Pemerintah</h3><p>Pengadaan Barang/Jasa Pemerintah yang diatur dalam Perpres 16/2018 jo Perpres 12/2021 mencakup pengadaan barang, pekerjaan konstruksi, jasa konsultansi, dan jasa lainnya yang didanai sebagian atau seluruhnya oleh APBN/APBD.</p><p>Prinsip kehati-hatian, akuntabilitas belanja daerah, dan kepatuhan terhadap regulasi merupakan pilar utama keberhasilan pengadaan publik.</p>",
                        'sort_order'   => 1,
                    ],
                    [
                        'title'        => 'Lesson 1.2: Tujuh Prinsip Pengadaan dan Pencegahan Benturan Kepentingan',
                        'slug'         => 'lesson-prinsip-etika-pbj',
                        'content_type' => 'text',
                        'content'      => "<h3>7 Prinsip Dasar Pengadaan</h3><ul><li><strong>Efisien:</strong> Menggunakan dana dan daya secara tepat untuk mencapai sasaran optimal.</li><li><strong>Efektif:</strong> Mencapai target kuantitas, kualitas, dan waktu yang ditetapkan.</li><li><strong>Transparan:</strong> Seluruh ketentuan dan informasi pengadaan terbuka bagi masyarakat.</li><li><strong>Terbuka:</strong> Dapat diikuti oleh semua penyedia yang memenuhi syarat.</li><li><strong>Bersaing:</strong> Pelaksanaan pemilihan dilakukan melalui persaingan yang sehat.</li><li><strong>Adil:</strong> Memberikan perlakuan sama bagi seluruh calon penyedia.</li><li><strong>Akuntabel:</strong> Seluruh tahapan dapat dipertanggungjawabkan secara hukum dan administratif.</li></ul>",
                        'sort_order'   => 2,
                    ],
                ],
                'quiz' => [
                    'title'        => 'Kuis Formatif Modul 1: Prinsip & Etika PBJ',
                    'instructions' => 'Selesaikan kuis formatif ini untuk memastikan pemahaman prinsip dan etika pengadaan.',
                    'questions'    => [
                        [
                            'prompt'      => 'Manakah yang termasuk pelanggaran etika pengadaan barang/jasa pemerintah?',
                            'points'      => 20.0,
                            'explanation' => 'Menerima imbalan atau gratifikasi dari penyedia merupakan pelanggaran berat etika pengadaan.',
                            'options'     => [
                                ['text' => 'Menghindari benturan kepentingan pribadi dalam mengambil keputusan', 'correct' => 0],
                                ['text' => 'Menerima hadiah atau imbalan dari rekanan yang sedang mengikuti tender', 'correct' => 1],
                                ['text' => 'Bekerja secara profesional, mandiri, dan menjaga rahasia proses evaluasi', 'correct' => 0],
                                ['text' => 'Mencegah terjadinya pemborosan dan kebocoran keuangan negara', 'correct' => 0],
                            ],
                        ],
                        [
                            'prompt'      => 'Prinsip yang menjamin semua penyedia yang memenuhi kualifikasi memiliki kesempatan yang sama untuk bersaing adalah:',
                            'points'      => 20.0,
                            'explanation' => 'Prinsip Terbuka dan Bersaing.',
                            'options'     => [
                                ['text' => 'Efisien', 'correct' => 0],
                                ['text' => 'Terbuka dan Bersaing', 'correct' => 1],
                                ['text' => 'Akuntabel', 'correct' => 0],
                                ['text' => 'Efektif', 'correct' => 0],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title'       => 'Modul 2: Pelaku Pengadaan dan Perencanaan Kebutuhan PBJ',
                'description' => 'Mendalami struktur kewenangan PA/KPA, PPK, Pejabat Pengadaan, Pokja Pemilihan, dan alur penyusunan dokumen perencanaan.',
                'sort_order'  => 2,
                'lessons'     => [
                    [
                        'title'        => 'Lesson 2.1: Peran dan Tanggung Jawab Para Pihak PBJ',
                        'slug'         => 'lesson-peran-pelaku-pbj',
                        'content_type' => 'text',
                        'content'      => "<h3>Para Pihak dalam Pengadaan</h3><p>Keberhasilan eksekusi pengadaan ditentukan oleh sinergi antara:</p><ol><li><strong>Pengguna Anggaran (PA):</strong> Pejabat pemegang kewenangan penggunaan anggaran K/L/PD.</li><li><strong>Pejabat Pembuat Komitmen (PPK):</strong> Pejabat yang diberi kewenangan oleh PA/KPA untuk mengambil keputusan dan melakukan tindakan yang dapat mengakibatkan pengeluaran anggaran belanja.</li><li><strong>Pokja Pemilihan:</strong> Pengelola pemilihan penyedia barang/jasa.</li><li><strong>Pejabat Pengadaan:</strong> Pengelola pengadaan langsung dan e-purchasing nilai tertentu.</li></ol>",
                        'sort_order'   => 1,
                    ],
                    [
                        'title'        => 'Lesson 2.2: Penyusunan Spesifikasi Teknis dan Kerangka Acuan Kerja (KAK)',
                        'slug'         => 'lesson-spesifikasi-kak-hps',
                        'content_type' => 'text',
                        'content'      => "<h3>Penyusunan Spesifikasi Teknis yang Benar</h3><p>Spesifikasi teknis harus mengacu pada kebutuhan nyata pengguna, bukan mengarah pada merek atau produk tertentu kecuali untuk suku cadang atau komponen pada sistem yang sudah ada.</p><p>Struktur spesifikasi mencakup: spesifikasi mutu/kualitas, spesifikasi jumlah, spesifikasi waktu pelaksanaan, dan spesifikasi pelayanan purna jual.</p>",
                        'sort_order'   => 2,
                    ],
                ],
                'quiz' => null,
            ],
        ];

        foreach ($modulesData as $mIdx => $m) {
            $existingMod = $db->table('learning_modules')
                ->where('class_id', $classId)
                ->where('sort_order', $m['sort_order'])
                ->get()->getRowArray();

            if (! $existingMod) {
                $db->table('learning_modules')->insert([
                    'class_id'    => $classId,
                    'title'       => $m['title'],
                    'description' => $m['description'],
                    'sort_order'  => $m['sort_order'],
                    'is_required' => 1,
                    'status'      => 'published',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $modId = (int) $db->insertID();
                CLI::write("  [CREATED] Module #{$m['sort_order']}: {$m['title']} (ID {$modId})", "green");
            } else {
                $modId = (int) $existingMod['id'];
                $db->table('learning_modules')->where('id', $modId)->update([
                    'title'       => $m['title'],
                    'description' => $m['description'],
                    'status'      => 'published',
                    'updated_at'  => $now,
                ]);
                CLI::write("  [EXISTING] Module #{$m['sort_order']}: {$m['title']} (ID {$modId})", "cyan");
            }

            // Lessons
            foreach ($m['lessons'] as $les) {
                $existingLes = $db->table('lessons')
                    ->where('module_id', $modId)
                    ->where('slug', $les['slug'])
                    ->get()->getRowArray();

                if (! $existingLes) {
                    $db->table('lessons')->insert([
                        'module_id'    => $modId,
                        'title'        => $les['title'],
                        'slug'         => $les['slug'],
                        'content_type' => $les['content_type'],
                        'content'      => $les['content'],
                        'sort_order'   => $les['sort_order'],
                        'is_required'  => 1,
                        'status'       => 'published',
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                    $lesId = (int) $db->insertID();
                    CLI::write("    [CREATED] Lesson: {$les['title']} (ID {$lesId})", "green");
                } else {
                    $lesId = (int) $existingLes['id'];
                }
            }

            // Module Quiz if present
            if (! empty($m['quiz'])) {
                $existingModQuiz = $db->table('quizzes')
                    ->where('class_id', $classId)
                    ->where('title', $m['quiz']['title'])
                    ->get()->getRowArray();

                if (! $existingModQuiz) {
                    $db->table('quizzes')->insert([
                        'class_id'      => $classId,
                        'lesson_id'     => $lesId ?? null,
                        'type'          => 'lesson_quiz',
                        'title'         => $m['quiz']['title'],
                        'instructions'  => $m['quiz']['instructions'],
                        'attempt_limit' => 3,
                        'is_required'   => 1,
                        'status'        => 'published',
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                    $modQuizId = (int) $db->insertID();
                    CLI::write("    [CREATED] Module Quiz: {$m['quiz']['title']} (ID {$modQuizId})", "green");
                } else {
                    $modQuizId = (int) $existingModQuiz['id'];
                }
                $this->seedQuizQuestions($db, $modQuizId, $m['quiz']['questions']);
            }
        }

        // 3. Post-Test Quiz
        $existingPostTest = $db->table('quizzes')
            ->where('class_id', $classId)
            ->where('type', 'post_test')
            ->get()->getRowArray();

        if (! $existingPostTest) {
            $db->table('quizzes')->insert([
                'class_id'         => $classId,
                'lesson_id'        => null,
                'type'             => 'post_test',
                'title'            => 'Post-Test: Ujian Akhir Komprehensif PBJ Pemerintah',
                'instructions'     => 'Ujian akhir komprehensif kelulusan kelas. Anda memiliki batas waktu 60 menit dan kesempatan pengerjaan maksimal 3 kali. Nilai passing grade adalah 75.',
                'attempt_limit'    => 3,
                'duration_minutes' => 60,
                'is_required'      => 1,
                'status'           => 'published',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $postTestId = (int) $db->insertID();
            CLI::write("  [CREATED] Post-Test Quiz (ID {$postTestId})", "green");
        } else {
            $postTestId = (int) $existingPostTest['id'];
            CLI::write("  [EXISTING] Post-Test Quiz (ID {$postTestId})", "cyan");
        }

        // Post-Test Questions (10 authentic questions)
        $this->seedQuizQuestions($db, $postTestId, [
            [
                'prompt'      => 'Dokumen perencanaan pengadaan yang diumumkan secara terbuka oleh PA/KPA melalui aplikasi SiRUP disebut:',
                'points'      => 10.0,
                'explanation' => 'Rencana Umum Pengadaan (RUP) wajib diumumkan melalui aplikasi SiRUP LKPP.',
                'options'     => [
                    ['text' => 'Rencana Kerja Anggaran (RKA)', 'correct' => 0],
                    ['text' => 'Rencana Umum Pengadaan (RUP)', 'correct' => 1],
                    ['text' => 'Kerangka Acuan Kerja (KAK)', 'correct' => 0],
                    ['text' => 'Dokumen Pemilihan Rekanan', 'correct' => 0],
                    ['text' => 'Rencana Pelaksanaan Proyek (RPP)', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Penyusunan Harga Perkiraan Sendiri (HPS) paling lambat ditetapkan pada saat:',
                'points'      => 10.0,
                'explanation' => 'HPS ditetapkan paling lama 28 hari kerja sebelum batas akhir pemasukan penawaran.',
                'options'     => [
                    ['text' => 'Paling lama 28 hari kerja sebelum batas akhir pemasukan dokumen penawaran', 'correct' => 1],
                    ['text' => 'Pada hari penandatanganan kontrak pengadaan', 'correct' => 0],
                    ['text' => 'Saat penyerahan hasil pekerjaan pertama (PHO)', 'correct' => 0],
                    ['text' => 'Bersamaan dengan penyusunan RUP di awal tahun anggaran', 'correct' => 0],
                    ['text' => 'Setelah dilakukan klarifikasi teknis dan negosiasi biaya', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Metode pemilihan penyedia untuk pengadaan barang bernilai di atas Rp 200.000.000 yang spesifikasi dan volumenya sudah dapat ditentukan secara rinci adalah:',
                'points'      => 10.0,
                'explanation' => 'Tender atau E-Purchasing (jika sudah tersedia di Katalog Elektronik). Secara umum adalah Tender.',
                'options'     => [
                    ['text' => 'Pengadaan Langsung', 'correct' => 0],
                    ['text' => 'Penunjukan Langsung', 'correct' => 0],
                    ['text' => 'Tender Cepat atau Tender Terbuka', 'correct' => 1],
                    ['text' => 'Sayembara Terbatas', 'correct' => 0],
                    ['text' => 'Swakelola Tipe IV', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Dalam pengadaan barang/jasa melalui penyedia, uang muka dapat diberikan kepada penyedia usaha kecil paling tinggi sebesar:',
                'points'      => 10.0,
                'explanation' => 'Untuk usaha mikro, kecil, dan koperasi, uang muka dapat diberikan paling tinggi 50% dari nilai kontrak.',
                'options'     => [
                    ['text' => '20% dari nilai kontrak', 'correct' => 0],
                    ['text' => '30% dari nilai kontrak', 'correct' => 0],
                    ['text' => '50% dari nilai kontrak', 'correct' => 1],
                    ['text' => '100% dari nilai kontrak', 'correct' => 0],
                    ['text' => '10% dari nilai kontrak', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Jaminan Pelaksanaan TIDAK diberlakukan untuk pengadaan dengan kriteria berikut, KECUALI:',
                'points'      => 10.0,
                'explanation' => 'Jaminan pelaksanaan diberlakukan untuk tender barang/jasa/konstruksi di atas Rp200 juta.',
                'options'     => [
                    ['text' => 'Pengadaan Jasa Konsultansi', 'correct' => 0],
                    ['text' => 'Pengadaan Barang/Jasa Lainnya dengan nilai kontrak sampai dengan Rp 200 juta', 'correct' => 0],
                    ['text' => 'Pengadaan melalui E-purchasing', 'correct' => 0],
                    ['text' => 'Tender Pekerjaan Konstruksi dengan nilai kontrak di atas Rp 200 juta', 'correct' => 1],
                    ['text' => 'Pengadaan secara Swakelola Tipe I', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Siapakah yang berwenang menjawab Sanggah dari peserta tender yang merasa dirugikan dalam proses pemilihan penyedia?',
                'points'      => 10.0,
                'explanation' => 'Pokja Pemilihan UKPBJ berwenang memberikan jawaban tertulis atas sanggah peserta.',
                'options'     => [
                    ['text' => 'Pokja Pemilihan UKPBJ', 'correct' => 1],
                    ['text' => 'Pejabat Pembuat Komitmen (PPK)', 'correct' => 0],
                    ['text' => 'Kepala Daerah', 'correct' => 0],
                    ['text' => 'Auditor BPKP', 'correct' => 0],
                    ['text' => 'Pengadilan Tata Usaha Negara (PTUN)', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Pemberian sanksi Daftar Hitam (Blacklist) kepada penyedia pengadaan diusulkan oleh PPK kepada:',
                'points'      => 10.0,
                'explanation' => 'PPK mengusulkan sanksi daftar hitam kepada PA/KPA, untuk kemudian diteruskan dan dicatat dalam portal Inaproc LKPP.',
                'options'     => [
                    ['text' => 'Pengguna Anggaran (PA) / Kuasa Pengguna Anggaran (KPA)', 'correct' => 1],
                    ['text' => 'Kepolisian Daerah', 'correct' => 0],
                    ['text' => 'Kantor Pelayanan Pajak Pratama', 'correct' => 0],
                    ['text' => 'Kementerian Perdagangan RI', 'correct' => 0],
                    ['text' => 'Asosiasi Rekanan Terkait', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Apa yang dimaksud dengan Tingkat Komponen Dalam Negeri (TKDN) dalam pengadaan barang/jasa pemerintah?',
                'points'      => 10.0,
                'explanation' => 'TKDN adalah persentase nilai isian manufaktur lokal, tenaga kerja, dan material domestik dalam suatu produk atau jasa.',
                'options'     => [
                    ['text' => 'Besaran diskon yang diberikan pabrikan kepada instansi pemerintah', 'correct' => 0],
                    ['text' => 'Besarnya persentase kandungan komponen lokal/domestik pada barang atau jasa yang ditawarkan', 'correct' => 1],
                    ['text' => 'Jumlah kantor cabang penyedia yang beroperasi di wilayah provinsi pengadaan', 'correct' => 0],
                    ['text' => 'Batas waktu garansi teknis yang disepakati dalam kontrak', 'correct' => 0],
                    ['text' => 'Rasio perbandingan keuntungan penyedia dengan modal investasi', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Tahapan pengadaan di mana PPK dan Penyedia menandatangani dokumen penerimaan hasil pekerjaan setelah dilakukan pemeriksaan fisik dan uji fungsi disebut:',
                'points'      => 10.0,
                'explanation' => 'Berita Acara Serah Terima (BAST).',
                'options'     => [
                    ['text' => 'Surat Perintah Mulai Kerja (SPMK)', 'correct' => 0],
                    ['text' => 'Surat Penunjukan Penyedia Barang/Jasa (SPPBJ)', 'correct' => 0],
                    ['text' => 'Berita Acara Serah Terima (BAST)', 'correct' => 1],
                    ['text' => 'Surat Jaminan Pemeliharaan', 'correct' => 0],
                    ['text' => 'Surat Perintah Pencairan Dana (SP2D)', 'correct' => 0],
                ],
            ],
            [
                'prompt'      => 'Prinsip Value for Money dalam pengadaan barang/jasa pemerintah menghendaki tercapainya kombinasi optimal antara:',
                'points'      => 10.0,
                'explanation' => 'Value for money menekankan kombinasi kualitas, kuantitas, waktu, biaya, dan manfaat sepanjang siklus hidup barang/jasa.',
                'options'     => [
                    ['text' => 'Biaya total kepemilikan terendah dengan pemenuhan kualitas, waktu, dan manfaat yang tepat', 'correct' => 1],
                    ['text' => 'Harga penawaran terendah tanpa memperhatikan daya tahan barang', 'correct' => 0],
                    ['text' => 'Pembelian produk impor bermerek internasional dengan harga premium', 'correct' => 0],
                    ['text' => 'Pembelanjaan anggaran dalam jumlah paling besar untuk meningkatkan serapan', 'correct' => 0],
                    ['text' => 'Penunjukan langsung kepada rekanan binaan tanpa proses seleksi', 'correct' => 0],
                ],
            ],
        ]);

        // -------------------------------------------------------------
        // STEP 6: ASSIGNMENT SEEDING WITH REAL PDF GUIDE
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [6/7] Generating PDF Guide & Assignment...", "yellow");

        $uploadDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'learning' . DIRECTORY_SEPARATOR . 'assignments';
        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $pdfFilename = 'petunjuk_tugas_spesifikasi_hps.pdf';
        $pdfFilePath = $uploadDir . DIRECTORY_SEPARATOR . $pdfFilename;
        $pdfRelativeUrl = 'uploads/learning/assignments/' . $pdfFilename;

        // Generate authentic PDF document
        $pdfContent = $this->generateSamplePdfDocument(
            "PANDUAN PRAKTIKUM STUDI KASUS PBJ - BATCH 1",
            "PETUNJUK PENYUSUNAN DOKUMEN SPESIFIKASI TEKNIS & HPS PENGADAAN APBD 2026",
            [
                "1. KASUS: Pengadaan Perangkat Komputer dan Jaringan Lab Komputer OPD Pemprov Lampung.",
                "2. PAGU ANGGARAN: Rp 450.000.000 (Empat Ratus Lima Puluh Juta Rupiah) - Sumber Dana APBD 2026.",
                "3. TUGAS PESERTA:",
                "   a. Susun Kerangka Acuan Kerja (KAK) mencakup latar belakang dan indikator keluaran.",
                "   b. Tetapkan Spesifikasi Teknis (mutu, jumlah, waktu, layanan) dengan afirmasi PDN/TKDN min 40%.",
                "   c. Buat simulasi Kertas Kerja HPS berdasarkan minimal 3 sumber referensi harga pasar yang sah.",
                "4. FORMAT PENGUMPULAN: Berkas PDF (maksimal 10MB) diunggah melalui formulir tugas workspace LMS.",
                "5. INDIKATOR PENILAIAN: Kesesuaian regulasi (30%), Kelayakan spesifikasi (35%), dan Akurasi HPS (35%).",
            ]
        );
        file_put_contents($pdfFilePath, $pdfContent);
        $fileSize = filesize($pdfFilePath);
        CLI::write("  [GENERATED] PDF Guide file created at: {$pdfFilePath} ({$fileSize} bytes)", "green");

        $guideMetadata = json_encode([
            'url'      => $pdfRelativeUrl,
            'filename' => $pdfFilename,
            'size'     => $fileSize,
        ], JSON_UNESCAPED_SLASHES);

        $assignmentInstructions = "Pelajari skenario studi kasus pengadaan perangkat laboratorium komputer dan jaringan pada berkas petunjuk terlampir.\n\n"
            . "Peserta diminta menyusun dokumen spesifikasi teknis dan kertas kerja HPS sesuai ketentuan Perpres 12/2021. Berkas tugas yang telah disusun rapi wajib diunggah dalam format PDF.\n\n"
            . "<!--ASSIGNMENT_GUIDE:{$guideMetadata}:ASSIGNMENT_GUIDE-->";

        $assignmentTitle = 'Studi Kasus Penyusunan Dokumen Spesifikasi Teknis & HPS';
        $existingAssignment = $db->table('assignments')
            ->where('class_id', $classId)
            ->where('title', $assignmentTitle)
            ->get()->getRowArray();

        $assignmentData = [
            'class_id'     => $classId,
            'lesson_id'    => null, // Level Kelas
            'title'        => $assignmentTitle,
            'instructions' => $assignmentInstructions,
            'due_at'       => date('Y-m-d H:i:s', strtotime('+20 days')),
            'max_attempts' => 2,
            'is_required'  => 1,
            'status'       => 'published',
            'created_at'   => $now,
            'updated_at'   => $now,
        ];

        if (! $existingAssignment) {
            $db->table('assignments')->insert($assignmentData);
            $assignmentId = (int) $db->insertID();
            CLI::write("  [CREATED] Assignment: {$assignmentTitle} (ID {$assignmentId}) with PDF guide embedded.", "green");
        } else {
            $assignmentId = (int) $existingAssignment['id'];
            $db->table('assignments')->where('id', $assignmentId)->update(array_merge($assignmentData, ['updated_at' => $now]));
            CLI::write("  [UPDATED] Assignment: {$assignmentTitle} (ID {$assignmentId}) with PDF guide embedded.", "cyan");
        }

        // -------------------------------------------------------------
        // STEP 7: ENROLLMENTS SEEDING (ACTIVE, COMPLETED, PENDING, WITHDRAWN)
        // -------------------------------------------------------------
        CLI::newLine();
        CLI::write(">>> [7/7] Seeding Authentic Enrollments across Multiple Lifecycle States...", "yellow");

        foreach ($demoUsers as $u) {
            if ($u['role_key'] !== 'peserta') {
                continue;
            }
            $uId = $userIds[$u['username']] ?? null;
            if (! $uId) {
                continue;
            }

            $existingEnrollment = $db->table('enrollments')
                ->where('class_id', $classId)
                ->where('user_id', $uId)
                ->get()->getRowArray();

            $enrStatus = $u['enroll_status'] ?? 'active';
            $enrData = [
                'class_id'     => $classId,
                'user_id'      => $uId,
                'status'       => $enrStatus,
                'enrolled_at'  => date('Y-m-d H:i:s', strtotime('-4 days')),
                'activated_at' => in_array($enrStatus, ['active', 'completed'], true) ? date('Y-m-d H:i:s', strtotime('-4 days')) : null,
                'cancelled_at' => ($enrStatus === 'withdrawn') ? date('Y-m-d H:i:s', strtotime('-1 day')) : null,
                'final_score'  => $u['final_score'] ?? null,
            ];

            if (! $existingEnrollment) {
                $db->table('enrollments')->insert($enrData);
                $enrId = (int) $db->insertID();
                CLI::write("  [ENROLLED] {$u['name']} into Class #{$classId} (ID {$enrId}, Status: {$enrStatus})", "green");
            } else {
                $enrId = (int) $existingEnrollment['id'];
                $db->table('enrollments')->where('id', $enrId)->update($enrData);
                CLI::write("  [UPDATED] {$u['name']} in Class #{$classId} (ID {$enrId}, Status: {$enrStatus})", "cyan");
            }
        }

        CLI::newLine();
        CLI::write("==================================================================", "cyan");
        CLI::write(" [SUCCESS] ARENA BELAJAR DEMO SEEDING COMPLETED SUCCESSFULLY!", "green");
        CLI::write("==================================================================", "cyan");
        CLI::write("Demo Summary:", "yellow");
        CLI::write("  - Program ID:    {$programId} (Pengembangan Kompetensi PBJ)");
        CLI::write("  - Course ID:     {$courseId} (Pengantar Pengadaan Barang dan Jasa - 24 JP)");
        CLI::write("  - Class ID:      {$classId} (Batch 1, Code: {$classCode})");
        CLI::write("  - Pre-Test ID:   {$preTestId} (5 Questions)");
        CLI::write("  - Post-Test ID:  {$postTestId} (10 Questions)");
        CLI::write("  - Assignment ID: {$assignmentId} (With PDF Guide: {$pdfFilename})");
        CLI::write("  - Trainer:       trainer.pbj@lampungprov.go.id / Password123!");
        CLI::write("  - Operator:      operator.lms@lampungprov.go.id / Password123!");
        CLI::write("  - Active User:   ani.peserta@lampungprov.go.id / Password123!");
        CLI::write("  - Completed:     budi.peserta@lampungprov.go.id / Password123!");
        CLI::write("  - Pending User:  citra.peserta@lampungprov.go.id / Password123! (Siap Approve/Tolak)");
        CLI::write("  - Withdrawn:     doni.peserta@lampungprov.go.id / Password123! (Siap Diaktifkan Kembali)");
        CLI::write("==================================================================", "cyan");

        return 0;
    }

    /**
     * Helper to seed questions and choices into a quiz idempotently.
     */
    private function seedQuizQuestions($db, int $quizId, array $questions): void
    {
        $now = date('Y-m-d H:i:s');
        $existingCount = (int) $db->table('quiz_questions')->where('quiz_id', $quizId)->countAllResults();
        if ($existingCount > 0) {
            // Questions already exist for this quiz
            return;
        }

        foreach ($questions as $qIdx => $q) {
            $db->table('quiz_questions')->insert([
                'quiz_id'     => $quizId,
                'type'        => 'multiple_choice',
                'prompt'      => $q['prompt'],
                'points'      => $q['points'] ?? 10.0,
                'sort_order'  => $qIdx + 1,
                'explanation' => $q['explanation'] ?? '',
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $questionId = (int) $db->insertID();

            foreach ($q['options'] as $oIdx => $opt) {
                $db->table('quiz_options')->insert([
                    'question_id' => $questionId,
                    'option_text' => $opt['text'],
                    'is_correct'  => $opt['correct'] ? 1 : 0,
                    'sort_order'  => $oIdx + 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }

    /**
     * Generates a minimal, 100% valid PDF 1.4 binary document without external libraries.
     */
    private function generateSamplePdfDocument(string $header, string $title, array $lines): string
    {
        $stream = "BT /F1 16 Tf 50 780 Td (" . addcslashes($header, "()\\\r\n") . ") Tj ET\n";
        $stream .= "BT /F2 12 Tf 50 755 Td (" . addcslashes($title, "()\\\r\n") . ") Tj ET\n";
        $stream .= "0 0 0 rg 50 745 495 2 re f\n"; // Horizontal divider rule

        $y = 715;
        foreach ($lines as $line) {
            $stream .= "BT /F3 10 Tf 50 {$y} Td (" . addcslashes($line, "()\\\r\n") . ") Tj ET\n";
            $y -= 22;
        }

        $stream .= "BT /F3 8 Tf 50 50 Td (Dokumen Resmi Begawi LMS - Biro Pengadaan Barang dan Jasa Sekretariat Daerah Provinsi Lampung) Tj ET\n";
        $streamLen = strlen($stream);

        $out = "%PDF-1.4\n";
        $offsets = [];

        $offsets[1] = strlen($out);
        $out .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        $offsets[2] = strlen($out);
        $out .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

        $offsets[3] = strlen($out);
        $out .= "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R /F3 7 0 R >> >> >>\nendobj\n";

        $offsets[4] = strlen($out);
        $out .= "4 0 obj\n<< /Length {$streamLen} >>\nstream\n{$stream}\nendstream\nendobj\n";

        $offsets[5] = strlen($out);
        $out .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

        $offsets[6] = strlen($out);
        $out .= "6 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-BoldOblique >>\nendobj\n";

        $offsets[7] = strlen($out);
        $out .= "7 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $xrefPos = strlen($out);
        $out .= "xref\n0 8\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= 7; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $out .= "trailer\n<< /Size 8 /Root 1 0 R >>\nstartxref\n{$xrefPos}\n%%EOF\n";

        return $out;
    }
}
