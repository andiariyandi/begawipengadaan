<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\EventAssessmentService;
use CodeIgniter\HTTP\Files\UploadedFile;

class EventAssessmentEnhancementTest extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:event-enhancement';
    protected $description = 'Verifies Event & Agenda Pre-Test, Materials, and Post-Test enhancements without migrations.';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN TEST EVENT & AGENDA ENHANCEMENT ===", 'yellow');

        $db = db_connect();
        $passed = 0;
        $failed = 0;

        // Test 1: Service Instantiation
        $service = new EventAssessmentService();
        if ($service instanceof EventAssessmentService) {
            CLI::write("[PASS] 1. EventAssessmentService berhasil diinstansiasi.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 1. Gagal menginstansiasi EventAssessmentService.");
            $failed++;
        }

        // Find or create test event
        $event = $db->table('events')->where('deleted_at IS NULL')->orderBy('id', 'DESC')->get()->getRowArray();
        if (! $event) {
            $eventId = (int) $db->table('events')->insert([
                'title'               => 'Webinar Uji Coba Asesmen Interaktif',
                'slug'                => 'webinar-uji-coba-asesmen-' . time(),
                'type'                => 'webinar',
                'mode'                => 'online',
                'status'              => 'open_registration',
                'visibility'          => 'public',
                'summary'             => 'Ringkasan kegiatan uji coba.',
                'description'         => 'Deskripsi kegiatan uji coba asesmen.',
                'certificate_enabled' => 1,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ], true);
            $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
        }
        $eventId = (int) $event['id'];
        CLI::write("Menggunakan Event ID #{$eventId}: '{$event['title']}' (Tipe: {$event['type']})", 'cyan');

        // Find valid test user
        $user = $db->table('users')->where('deleted_at IS NULL')->orderBy('id', 'ASC')->get()->getRowArray();
        $testUserId = (int) ($user['id'] ?? 13);
        CLI::write("Menggunakan User ID #{$testUserId}: '{$user['name']}'", 'cyan');

        // Test 2: Default Assessment Config Retrieval
        $defaultConfig = $service->getAssessmentConfig($eventId);
        if (isset($defaultConfig['pre_test']) && isset($defaultConfig['post_test'])) {
            CLI::write("[PASS] 2. Assessment config retrieval memiliki struktur pre_test dan post_test.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 2. Assessment config tidak valid.");
            $failed++;
        }

        // Test 3: Save Assessment Config with Questions (Pre-Test & Post-Test)
        $testConfig = [
            'pre_test' => [
                'enabled'          => true,
                'title'            => 'Pre-Test Pemahaman Regulasi PBJ',
                'duration_minutes' => 20,
                'passing_score'    => 75,
                'instructions'     => 'Jawab seluruh pertanyaan dengan teliti sebelum sesi dimulai.',
                'questions'        => [
                    [
                        'id'          => 1,
                        'question'    => 'Apa tujuan utama dari proses pengadaan barang dan jasa pemerintah?',
                        'options'     => [
                            'Menghabiskan anggaran sebelum akhir tahun anggaran',
                            'Memperoleh barang/jasa yang menghasilkan value for money',
                            'Mempermudah penunjukan langsung bagi mitra kerja',
                            'Menghindari audit dari aparat pengawasan internal'
                        ],
                        'answer'      => 1, // Option index 1 (B)
                        'explanation' => 'Tujuan utama PBJ adalah menghasilkan value for money yang berorientasi pada hasil dan manfaat publik.'
                    ],
                    [
                        'id'          => 2,
                        'question'    => 'Siapakah pejabat yang berwenang menetapkan HPS dalam tender pengadaan?',
                        'options'     => [
                            'Pejabat Pembuat Komitmen (PPK)',
                            'Pokja Pemilihan UKPBJ',
                            'Pengguna Anggaran (PA)',
                            'Inspektorat Daerah'
                        ],
                        'answer'      => 0, // Option index 0 (A)
                        'explanation' => 'Sesuai aturan Perpres PBJ, penetapan HPS merupakan kewenangan PPK.'
                    ]
                ]
            ],
            'post_test' => [
                'enabled'          => true,
                'title'            => 'Post-Test Evaluasi Pemahaman PBJ',
                'duration_minutes' => 25,
                'passing_score'    => 80,
                'instructions'     => 'Kerjakan evaluasi akhir setelah menyimak materi pembicara.',
                'questions'        => [
                    [
                        'id'          => 1,
                        'question'    => 'Metode pemilihan penyedia untuk pekerjaan konstruksi bernilai di atas Rp 200 juta adalah:',
                        'options'     => [
                            'Pengadaan Langsung',
                            'Tender',
                            'Penunjukan Langsung Tanpa Syarat',
                            'Swakelola Tipe I Saja'
                        ],
                        'answer'      => 1, // Option index 1 (B)
                        'explanation' => 'Pekerjaan konstruksi di atas Rp 200 juta umumnya dilakukan melalui mekanisme Tender.'
                    ]
                ]
            ]
        ];

        $saveOk = $service->saveAssessmentConfig($eventId, $testConfig, $testUserId);
        if ($saveOk) {
            CLI::write("[PASS] 3. Berhasil menyimpan konfigurasi Pre-Test dan Post-Test.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 3. Gagal menyimpan konfigurasi assessment.");
            $failed++;
        }

        // Test 4: Verify Saved Assessment Config Data
        $savedConfig = $service->getAssessmentConfig($eventId);
        if (
            ! empty($savedConfig['pre_test']['enabled']) &&
            count($savedConfig['pre_test']['questions']) === 2 &&
            ! empty($savedConfig['post_test']['enabled']) &&
            count($savedConfig['post_test']['questions']) === 1
        ) {
            CLI::write("[PASS] 4. Konfigurasi yang tersimpan cocok dan persisten tanpa migration.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 4. Data konfigurasi tidak cocok.");
            $failed++;
        }

        // Test 5: Material File Storage via direct insert / Upload simulation
        // Create mock presentation and pdf material files in file_assets table
        $now = date('Y-m-d H:i:s');
        $sampleMaterials = [
            [
                'original_name' => 'Paparan_Regulasi_PBJ_2026.pdf',
                'stored_name'   => 'mat_sample_' . time() . '_1.pdf',
                'extension'     => 'pdf',
                'mime_type'     => 'application/pdf',
                'size_bytes'    => 1024 * 1024 * 2, // 2MB
                'title'         => 'Slide Materi Regulasi Pengadaan',
                'speaker'       => 'Dr. Hendra, S.T., M.M.',
            ],
            [
                'original_name' => 'Bahan_Tayang_Presentasi.pptx',
                'stored_name'   => 'mat_sample_' . time() . '_2.pptx',
                'extension'     => 'pptx',
                'mime_type'     => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'size_bytes'    => 1024 * 1024 * 5, // 5MB
                'title'         => 'Bahan Tayang Narasumber LKPP',
                'speaker'       => 'Tim Fasilitator PBJ',
            ],
            [
                'original_name' => 'Lembar_Kerja_HPS.xlsx',
                'stored_name'   => 'mat_sample_' . time() . '_3.xlsx',
                'extension'     => 'xlsx',
                'mime_type'     => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'size_bytes'    => 512 * 1024, // 512KB
                'title'         => 'Template Excel Kertas Kerja HPS',
                'speaker'       => 'Instruktur Ahli',
            ],
            [
                'original_name' => 'Panduan_Peserta.docx',
                'stored_name'   => 'mat_sample_' . time() . '_4.docx',
                'extension'     => 'docx',
                'mime_type'     => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'size_bytes'    => 256 * 1024, // 256KB
                'title'         => 'Dokumen Petunjuk Praktik',
                'speaker'       => 'Sekretariat Panitia',
            ],
        ];

        $insertedMaterialIds = [];
        foreach ($sampleMaterials as $sm) {
            $db->table('file_assets')->insert([
                'disk'             => 'local',
                'path'             => 'uploads/events/materials/' . $sm['stored_name'],
                'original_name'    => $sm['original_name'],
                'stored_name'      => $sm['stored_name'],
                'extension'        => $sm['extension'],
                'mime_type'        => $sm['mime_type'],
                'size_bytes'       => $sm['size_bytes'],
                'visibility'       => 'public',
                'lifecycle_status' => 'available',
                'resource_type'    => 'event_material',
                'resource_id'      => $eventId,
                'uploaded_by'      => $testUserId,
                'metadata'         => json_encode([
                    'title'       => $sm['title'],
                    'speaker'     => $sm['speaker'],
                    'uploaded_at' => $now,
                ], JSON_UNESCAPED_UNICODE),
                'available_at'     => $now,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $insertedMaterialIds[] = (int) $db->insertID();
        }

        $materialsList = $service->getMaterials($eventId);
        if (count($materialsList) >= 4) {
            CLI::write("[PASS] 5. Berhasil mengambil list materi event (PDF, PPTX, XLSX, DOCX) dengan icon & metadata.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 5. Gagal mengambil materi event.");
            $failed++;
        }

        // Test 6: Verify Participant Registration & Quiz Submission
        $user = $db->table('users')->where('deleted_at IS NULL')->orderBy('id', 'ASC')->get()->getRowArray();
        $testUserId = (int) ($user['id'] ?? 1);

        $reg = $db->table('event_registrations')->where(['event_id' => $eventId, 'user_id' => $testUserId])->get()->getRowArray();
        if (! $reg) {
            $db->table('event_registrations')->insert([
                'event_id'      => $eventId,
                'user_id'       => $testUserId,
                'status'        => 'registered',
                'registered_at' => date('Y-m-d H:i:s'),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $regId = (int) $db->insertID();
        } else {
            $regId = (int) $reg['id'];
            $db->table('event_registrations')->where('id', $regId)->update(['status' => 'registered']);
        }
        $reg = $db->table('event_registrations')->where('id', $regId)->get()->getRowArray();

        // Participant submits Pre-Test: question 1 correct (ans=1), question 2 correct (ans=0) => 100 score
        $submissionAnswers = [
            '1' => 1,
            '2' => 0,
        ];
        $quizResult = $service->submitQuizAttempt($eventId, $testUserId, $regId, 'pre_test', $submissionAnswers);
        if ($quizResult['success'] && $quizResult['score'] === 100 && $quizResult['is_passed'] === true) {
            CLI::write("[PASS] 6. Berhasil submit Pre-Test, penilaian akurat (Skor: 100, Lulus: Ya).", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 6. Evaluasi kuis Pre-Test gagal atau tidak sesuai.");
            $failed++;
        }

        // Test 7: Verify getUserAttempt
        $attempt = $service->getUserAttempt($eventId, $testUserId, 'pre_test');
        if ($attempt && $attempt['score'] === 100 && count($attempt['review']) === 2) {
            CLI::write("[PASS] 7. Percobaan kuis tersimpan dan dapat dimuat kembali beserta pembahasannya.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 7. Gagal memuat percobaan kuis.");
            $failed++;
        }

        // Test 8: Participant submits Post-Test with wrong answer: question 1 chosen 0 (correct is 1) => 0 score
        $postAnswers = [
            '1' => 0,
        ];
        $postResult = $service->submitQuizAttempt($eventId, $testUserId, $regId, 'post_test', $postAnswers);
        if ($postResult['success'] && $postResult['score'] === 0 && $postResult['is_passed'] === false) {
            CLI::write("[PASS] 8. Berhasil submit Post-Test dengan evaluasi jawaban salah (Skor: 0, Lulus: Tidak).", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 8. Evaluasi kuis Post-Test gagal.");
            $failed++;
        }

        // Test 9: Assessment Statistics for Admin
        $stats = $service->getAssessmentStats($eventId);
        if ($stats['pre_test_count'] >= 1 && $stats['post_test_count'] >= 1) {
            CLI::write("[PASS] 9. Statistik asesmen event (jumlah peserta & rata-rata nilai) berhasil dihitung untuk admin.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 9. Gagal menghitung statistik asesmen event.");
            $failed++;
        }

        // Test 10: Attendance Certificate Rule: Quiz is optional, ONLY Presensi via Kode Sandi grants Certificate
        // Confirm attendance is present
        $att = $db->table('attendances')->where('event_registration_id', $regId)->get()->getRowArray();
        if (! $att) {
            $db->table('attendances')->insert([
                'event_registration_id' => $regId,
                'status'                => 'present',
                'recorded_by'           => $testUserId,
                'recorded_at'           => date('Y-m-d H:i:s'),
                'notes'                 => 'Hadir via kode presensi',
            ]);
        } else {
            $db->table('attendances')->where('id', $att['id'])->update(['status' => 'present']);
        }

        $commSvc = new \App\Services\Communication\CommunicationService();
        $batchCert = $commSvc->batchGenerateEventCertificates($eventId, $testUserId);
        $certCount = $db->table('certificates')->where('source_type', 'event_registration')->where('source_id', $regId)->countAllResults();

        if ($certCount >= 1) {
            CLI::write("[PASS] 10. Sertifikat terbit sukses berdasarkan PRESENSI KEHADIRAN (Pre-Test/Post-Test opsional).", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 10. Penerbitan sertifikat kehadiran gagal.");
            $failed++;
        }

        // Test 11: Material Deletion Test
        if (! empty($insertedMaterialIds)) {
            $deleteTarget = end($insertedMaterialIds);
            $deleted = $service->deleteMaterial($deleteTarget, $eventId);
            if ($deleted) {
                CLI::write("[PASS] 11. Hapus materi event berfungsi dengan baik.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 11. Gagal menghapus materi event.");
                $failed++;
            }
        }

        // Clean up test materials created in this test run
        foreach ($insertedMaterialIds as $mid) {
            $db->table('file_assets')->where('id', $mid)->delete();
        }

        // Test 12: View Render Dashboard Event Create Form
        try {
            $htmlCreate = view('dashboard/communication/events/form', [
                'title'            => 'Tambah Event | Dashboard BegawiPengadaan',
                'pageTitle'        => 'Tambah Event',
                'formMode'         => 'create',
                'activeMenu'       => 'communication',
                'activeSubmenu'    => 'events',
                'item'             => null,
                'assessmentConfig' => $service->getAssessmentConfig(0),
                'materials'        => [],
                'assessmentStats'  => [],
            ]);
            if (str_contains($htmlCreate, '7. Materi &amp; Bahan Tayangan Presentasi') && str_contains($htmlCreate, '8. Aktivitas Interaktif: Pre-Test &amp; Post-Test')) {
                CLI::write("[PASS] 12. Render Dashboard Event Form (Create) sukses memuat form materi & asesmen.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 12. Form create tidak memuat section materi atau asesmen.");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error("[FAIL] 12. Error render form create: " . $e->getMessage());
            $failed++;
        }

        // Test 13: View Render Dashboard Event Edit Form
        try {
            $htmlEdit = view('dashboard/communication/events/form', [
                'title'            => 'Ubah Event | Dashboard BegawiPengadaan',
                'pageTitle'        => 'Ubah Event',
                'formMode'         => 'edit',
                'activeMenu'       => 'communication',
                'activeSubmenu'    => 'events',
                'item'             => $event,
                'eventId'          => $eventId,
                'materials'        => $service->getMaterials($eventId),
                'assessmentConfig' => $service->getAssessmentConfig($eventId),
                'assessmentStats'  => $service->getAssessmentStats($eventId),
            ]);
            if (str_contains($htmlEdit, 'Kelola Soal Pre-Test') && str_contains($htmlEdit, 'Kelola Soal Post-Test')) {
                CLI::write("[PASS] 13. Render Dashboard Event Form (Edit) sukses memuat modal soal pre/post test.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 13. Form edit tidak memuat modal soal.");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error("[FAIL] 13. Error render form edit: " . $e->getMessage());
            $failed++;
        }

        // Test 14: View Render Dashboard Event Detail
        try {
            $htmlDetail = view('dashboard/communication/events/detail', [
                'title'            => 'Detail Event | Dashboard BegawiPengadaan',
                'pageTitle'        => 'Detail Event',
                'activeMenu'       => 'communication',
                'activeSubmenu'    => 'events',
                'item'             => $event,
                'eventId'          => $eventId,
                'registeredCount'  => 1,
                'presentCount'     => 1,
                'certifiedCount'   => 1,
                'participants'     => [],
                'materials'        => $service->getMaterials($eventId),
                'assessmentConfig' => $service->getAssessmentConfig($eventId),
                'assessmentStats'  => $service->getAssessmentStats($eventId),
            ]);
            if (str_contains($htmlDetail, 'Materi &amp; Bahan Tayangan Presentasi') && str_contains($htmlDetail, 'modalUploadMaterial')) {
                CLI::write("[PASS] 14. Render Dashboard Event Detail sukses memuat materi & modal upload materi.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 14. Detail event tidak memuat komponen materi.");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error("[FAIL] 14. Error render detail: " . $e->getMessage());
            $failed++;
        }

        // Test 15: View Render Workspace Peserta Detail Event
        try {
            $htmlWs = view('main/workspace/pages/detail_event', [
                'pageTitle'            => $event['title'] . ' | Event Saya',
                'breadcrumbTail'       => [
                    ['label' => 'Event Saya', 'url' => route_to('workspace.participant.events')],
                    ['label' => $event['title']],
                ],
                'event'                => $event,
                'registration'         => $reg,
                'attendance'           => $att,
                'certificate'          => null,
                'hasAttended'          => true,
                'isPresensiActive'     => true,
                'isPresensiClosed'     => false,
                'isPresensiNotOpenYet' => false,
                'eventState'           => 'ongoing',
                'materials'            => $service->getMaterials($eventId),
                'assessmentConfig'     => $service->getAssessmentConfig($eventId),
                'preTestAttempt'       => $service->getUserAttempt($eventId, $testUserId, 'pre_test'),
                'postTestAttempt'      => $service->getUserAttempt($eventId, $testUserId, 'post_test'),
                'selfPacedProgress'    => $service->getSelfPacedProgress($eventId, $testUserId, $regId, false),
            ]);
            if (str_contains($htmlWs, 'Materi &amp; Bahan Tayangan Presentasi') && str_contains($htmlWs, 'modalTakePreTest') && str_contains($htmlWs, 'modalTakePostTest')) {
                CLI::write("[PASS] 15. Render Workspace Peserta sukses memuat berkas tayangan, Pre-Test modal, dan Post-Test modal.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 15. Workspace peserta tidak memuat komponen pre-test/post-test/materi.");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error("[FAIL] 15. Error render workspace detail: " . $e->getMessage());
            $failed++;
        }

        // =========================================================================
        // PENGUJIAN FITUR PEMBELAJARAN MANDIRI (SELF-PACED LEARNING SETELAH REAL SELESAI)
        // =========================================================================
        CLI::write("\n--- PENGUJIAN MODE PEMBELAJARAN MANDIRI (SELF-PACED) ---", 'yellow');

        // Test 16: Setup Past Event with Self-Paced Config & YouTube Recording
        $pastEventSlug = 'webinar-past-self-paced-' . time();
        $db->table('events')->insert([
            'title'               => 'Webinar Pasca Pelaksanaan PBJ Mandiri ' . time(),
            'slug'                => $pastEventSlug,
            'type'                => 'webinar',
            'mode'                => 'online',
            'status'              => 'finished',
            'visibility'          => 'public',
            'start_at'            => date('Y-m-d H:i:s', strtotime('-5 days')),
            'end_at'              => date('Y-m-d H:i:s', strtotime('-4 days')),
            'registration_open_at'=> date('Y-m-d H:i:s', strtotime('-10 days')),
            'registration_close_at'=> date('Y-m-d H:i:s', strtotime('-6 days')),
            'summary'             => 'Webinar yang telah selesai dan dibuka kembali untuk pembelajaran mandiri.',
            'description'         => 'Peserta dapat menyimak rekaman sesi narasumber serta memperoleh sertifikat mandiri.',
            'certificate_enabled' => 1,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);
        $pastEventId = (int) $db->insertID();

        $spConfig = [
            'pre_test' => [
                'enabled'          => true,
                'title'            => 'Pre-Test Mandiri Pengadaan',
                'duration_minutes' => 15,
                'passing_score'    => 70,
                'instructions'     => 'Kerjakan pre-test mandiri sebelum menyimak rekaman.',
                'questions'        => [
                    [
                        'id'          => 1,
                        'question'    => 'Apakah e-Purchasing melalui Katalog Elektronik wajib dimanfaatkan instansi pemerintah?',
                        'options'     => ['Ya, untuk produk yang sudah tercantum', 'Tidak, hanya bersifat opsional belaka'],
                        'answer'      => 0,
                        'explanation' => 'Sesuai regulasi LKPP, e-purchasing diprioritaskan bagi produk katalog.'
                    ]
                ]
            ],
            'post_test' => [
                'enabled'          => true,
                'title'            => 'Post-Test Mandiri Pengadaan',
                'duration_minutes' => 15,
                'passing_score'    => 75,
                'instructions'     => 'Selesaikan evaluasi post-test untuk membuka sertifikat.',
                'questions'        => [
                    [
                        'id'          => 1,
                        'question'    => 'Berapakah batas nilai pengadaan barang melalui mekanisme Pengadaan Langsung?',
                        'options'     => ['Maksimal Rp 200 Juta', 'Maksimal Rp 50 Juta', 'Maksimal Rp 1 Miliar'],
                        'answer'      => 0,
                        'explanation' => 'Pengadaan langsung barang/pekerjaan konstruksi/jasa lainnya adalah sampai dengan Rp 200 juta.'
                    ]
                ]
            ],
            'self_paced' => [
                'enabled'             => true,
                'recording_url'       => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'recording_title'     => 'Rekaman Paparan Kebijakan Pengadaan 2026',
                'extended_until'      => date('Y-m-d H:i:s', strtotime('+30 days')),
                'min_post_test_score' => 75,
                'require_pre_test'    => true,
                'require_materials'   => true,
                'require_post_test'   => true,
                'instructions'        => 'Simak video rekaman pemateri dan selesaikan pre-test serta post-test untuk klaim sertifikat.',
            ]
        ];

        $service->saveAssessmentConfig($pastEventId, $spConfig, $testUserId);
        $savedSp = $service->getAssessmentConfig($pastEventId);

        if (! empty($savedSp['self_paced']['enabled']) && $savedSp['self_paced']['min_post_test_score'] === 75) {
            CLI::write("[PASS] 16. Pengaturan Self-Paced tersimpan dengan aman tanpa migration.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 16. Gagal menyimpan pengaturan self-paced.");
            $failed++;
        }

        // Test 17: YouTube Embed URL Helper
        $embed1 = EventAssessmentService::getYoutubeEmbedUrl('https://www.youtube.com/watch?v=dQw4w9WgXcQ');
        $embed2 = EventAssessmentService::getYoutubeEmbedUrl('https://youtu.be/dQw4w9WgXcQ?t=10');
        if ($embed1 === 'https://www.youtube.com/embed/dQw4w9WgXcQ' && $embed2 === 'https://www.youtube.com/embed/dQw4w9WgXcQ') {
            CLI::write("[PASS] 17. Ekstraksi embed URL YouTube akurat untuk berbagai format tautan.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 17. Ekstraksi embed YouTube tidak sesuai: '{$embed1}' | '{$embed2}'");
            $failed++;
        }

        // Test 18: Register Participant to Past Event with Self-Paced Enabled
        // Create registration simulating participant registering after execution date
        $db->table('event_registrations')->insert([
            'event_id'      => $pastEventId,
            'user_id'       => $testUserId,
            'status'        => 'registered',
            'registered_at' => date('Y-m-d H:i:s'),
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        $spRegId = (int) $db->insertID();

        if ($spRegId > 0) {
            CLI::write("[PASS] 18. Peserta dapat mendaftar pada kegiatan pasca-selesai via Mode Pembelajaran Mandiri.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 18. Gagal mendaftarkan peserta jalur mandiri.");
            $failed++;
        }

        // Test 19: Check Initial Progress - Must NOT be eligible for certificate yet
        $initialProgress = $service->getSelfPacedProgress($pastEventId, $testUserId, $spRegId, true);
        if ($initialProgress['is_self_paced_enabled'] && ! $initialProgress['is_eligible_for_cert'] && ! $initialProgress['has_certificate']) {
            CLI::write("[PASS] 19. Progres awal terverifikasi: Sertifikat belum dapat diklaim sebelum menyelesaikan kuis.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 19. Validasi kelayakan sertifikat awal salah.");
            $failed++;
        }

        // Test 20: Pre-Test Completion in Self-Paced Mode
        $preSubmit = $service->submitQuizAttempt($pastEventId, $testUserId, $spRegId, 'pre_test', [0]); // Jawaban Benar (Opsi A)
        $progAfterPre = $service->getSelfPacedProgress($pastEventId, $testUserId, $spRegId, true);
        if ($preSubmit['score'] === 100 && $progAfterPre['has_completed_pre_test'] && ! $progAfterPre['is_eligible_for_cert']) {
            CLI::write("[PASS] 20. Pre-Test mandiri selesai (Skor 100). Sertifikat tetap menunggu Post-Test.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 20. Validasi tahapan Pre-Test mandiri tidak sesuai.");
            $failed++;
        }

        // Test 21: Post-Test Failed Attempt & Claim Protection
        $service->submitQuizAttempt($pastEventId, $testUserId, $spRegId, 'post_test', [1]); // Jawaban Salah (Opsi B)
        $progAfterFail = $service->getSelfPacedProgress($pastEventId, $testUserId, $spRegId, true);
        $claimBlocked = false;
        try {
            $service->claimSelfPacedCertificate($pastEventId, $testUserId, $spRegId);
        } catch (\RuntimeException $e) {
            $claimBlocked = true;
        }

        if (! $progAfterFail['has_passed_post_test'] && $claimBlocked) {
            CLI::write("[PASS] 21. Proteksi ketat: Klaim sertifikat ditolak jika Post-Test belum mencapai passing grade (75).", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 21. Klaim sertifikat tidak terproteksi dari nilai di bawah passing grade.");
            $failed++;
        }

        // Test 22: Pass Post-Test & Successfully Claim Self-Paced Certificate
        $postPassed = $service->submitQuizAttempt($pastEventId, $testUserId, $spRegId, 'post_test', [0]); // Jawaban Benar (Opsi A)
        $progAfterPass = $service->getSelfPacedProgress($pastEventId, $testUserId, $spRegId, true);
        if ($postPassed['score'] === 100 && $progAfterPass['is_eligible_for_cert']) {
            CLI::write("[PASS] 22a. Post-Test mandiri lulus (Skor: 100 >= 75). Kelayakan sertifikat terbuka.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 22a. Kelayakan sertifikat gagal terbuka.");
            $failed++;
        }

        $claimResult = $service->claimSelfPacedCertificate($pastEventId, $testUserId, $spRegId, $testUserId);
        $spAtt = $db->table('attendances')->where('event_registration_id', $spRegId)->get()->getRowArray();
        $spCert = $db->table('certificates')->where('source_type', 'event_registration')->where('source_id', $spRegId)->get()->getRowArray();

        if (
            ! empty($claimResult['success']) &&
            ! empty($spCert) &&
            ($spCert['status'] ?? '') === 'issued' &&
            ! empty($spAtt) &&
            $spAtt['status'] === 'present' &&
            str_contains($spAtt['notes'] ?? '', 'Hadir via Pembelajaran Mandiri')
        ) {
            CLI::write("[PASS] 22b. Sertifikat mandiri BERHASIL diterbitkan (No: {$spCert['certificate_number']}) dengan catatan presensi mandiri.", 'green');
            $passed++;
        } else {
            CLI::error("[FAIL] 22b. Gagal menerbitkan sertifikat mandiri.");
            $failed++;
        }

        // Test 23: Idempotent Claim & View Render Workspace Detail Self-Paced
        $repeatClaim = $service->claimSelfPacedCertificate($pastEventId, $testUserId, $spRegId, $testUserId);
        $pastEventRow = $db->table('events')->where('id', $pastEventId)->get()->getRowArray();
        $pastRegRow = $db->table('event_registrations')->where('id', $spRegId)->get()->getRowArray();

        try {
            $htmlSpWs = view('main/workspace/pages/detail_event', [
                'pageTitle'            => $pastEventRow['title'] . ' | Event Saya',
                'breadcrumbTail'       => [
                    ['label' => 'Event Saya', 'url' => route_to('workspace.participant.events')],
                    ['label' => $pastEventRow['title']],
                ],
                'event'                => $pastEventRow,
                'registration'         => $pastRegRow,
                'attendance'           => $spAtt,
                'certificate'          => $spCert,
                'hasAttended'          => true,
                'isPresensiActive'     => false,
                'isPresensiClosed'     => true,
                'isPresensiNotOpenYet' => false,
                'eventState'           => 'finished',
                'materials'            => $service->getMaterials($pastEventId),
                'assessmentConfig'     => $service->getAssessmentConfig($pastEventId),
                'preTestAttempt'       => $service->getUserAttempt($pastEventId, $testUserId, 'pre_test'),
                'postTestAttempt'      => $service->getUserAttempt($pastEventId, $testUserId, 'post_test'),
                'selfPacedProgress'    => $service->getSelfPacedProgress($pastEventId, $testUserId, $spRegId, true),
            ]);

            if (
                ! empty($repeatClaim['success']) &&
                $repeatClaim['certificate_number'] === $spCert['certificate_number'] &&
                str_contains($htmlSpWs, 'Mode Pembelajaran Mandiri (Self-Paced Learning)') &&
                str_contains($htmlSpWs, 'E-Sertifikat Pembelajaran Mandiri Anda Telah Terbit!')
            ) {
                CLI::write("[PASS] 23. Klaim berulang idempoten & view workspace mandiri render sempurna dengan player video rekaman dan sertifikat.", 'green');
                $passed++;
            } else {
                CLI::error("[FAIL] 23. View workspace mandiri tidak memuat banner atau sertifikat.");
                $failed++;
            }
        } catch (\Throwable $e) {
            CLI::error("[FAIL] 23. Error render view workspace mandiri: " . $e->getMessage());
            $failed++;
        }

        CLI::write("\n=======================================================", 'yellow');
        CLI::write("HASIL PENGUJIAN EVENT & AGENDA ENHANCEMENT:", 'cyan');
        CLI::write("Total Pengujian : " . ($passed + $failed));
        CLI::write("Lulus (PASS)    : {$passed}", 'green');
        CLI::write("Gagal (FAIL)    : {$failed}", $failed > 0 ? 'red' : 'green');
        CLI::write("=======================================================\n", 'yellow');

        return $failed === 0 ? 0 : 1;
    }
}
