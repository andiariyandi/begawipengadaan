<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class DatabaseResetContent extends BaseCommand
{
    protected $group       = 'Maintenance';
    protected $name        = 'db:reset:content';
    protected $description = 'Safely clears all dummy content (Arena Belajar, Bunga Rampai, Regulasi, Berita, Event, Peserta) for release v.1, keeping only Admin & Superadmin.';

    public function run(array $params): int
    {
        CLI::write("==================================================================", "cyan");
        CLI::write("      BEGAWIPENGADAAN RELEASE V.1 DATABASE CLEANUP ENGINE         ", "cyan");
        CLI::write("==================================================================", "cyan");
        CLI::newLine();

        $db = Database::connect();

        // 1. Confirm Admin accounts exist
        $superadmin = $db->table('users')->where('id', 13)->get()->getRowArray();
        $admin = $db->table('users')->where('id', 14)->get()->getRowArray();

        if (! $superadmin || ! $admin) {
            CLI::error("[ABORT] Superadmin (ID 13) or Admin (ID 14) not found! Aborting to prevent data loss.");
            return 1;
        }

        CLI::write("Target Admin Accounts Verified:", "green");
        CLI::write("  - Superadmin: ID={$superadmin['id']} | Username={$superadmin['username']} | Email={$superadmin['email']}");
        CLI::write("  - Admin     : ID={$admin['id']} | Username={$admin['username']} | Email={$admin['email']}");
        CLI::newLine();

        // Disable Foreign Key Checks
        $db->query("SET FOREIGN_KEY_CHECKS = 0;");
        CLI::write("Foreign key checks temporarily disabled.", "yellow");

        // -------------------------------------------------------------
        // SECTION 1: ARENA BELAJAR (LEARNING LMS)
        // -------------------------------------------------------------
        CLI::write("--- [1] CLEANING ARENA BELAJAR ---", "yellow");
        $learningTables = [
            'programs',
            'courses',
            'learning_classes',
            'learning_modules',
            'lessons',
            'learning_progress',
            'enrollments',
            'class_trainers',
            'class_evaluators',
            'quizzes',
            'quiz_questions',
            'quiz_options',
            'quiz_attempts',
            'quiz_answers',
            'assignments',
            'assignment_submissions',
            'assignment_submission_files',
            'pre_tests',
            'feedbacks',
        ];

        foreach ($learningTables as $tbl) {
            if ($db->tableExists($tbl)) {
                $countBefore = $db->table($tbl)->countAllResults();
                $db->table($tbl)->truncate();
                CLI::write("  [TRUNCATED] `{$tbl}` ({$countBefore} rows cleared)", "green");
            }
        }

        // -------------------------------------------------------------
        // SECTION 2: BUNGA RAMPAI (KNOWLEDGE REPOSITORY)
        // -------------------------------------------------------------
        CLI::write("--- [2] CLEANING BUNGA RAMPAI (KNOWLEDGE) ---", "yellow");
        $knowledgeTables = [
            'knowledge_articles',
            'knowledge_article_tags',
            'knowledge_article_categories',
            'knowledge_media',
            'knowledge_tags',
        ];

        foreach ($knowledgeTables as $tbl) {
            if ($db->tableExists($tbl)) {
                $countBefore = $db->table($tbl)->countAllResults();
                $db->table($tbl)->truncate();
                CLI::write("  [TRUNCATED] `{$tbl}` ({$countBefore} rows cleared)", "green");
            }
        }

        // Clean knowledge categories: remove test/qa categories
        $db->table('knowledge_categories')
            ->like('slug', 'qa-audit', 'after')
            ->orLike('slug', 'test', 'after')
            ->delete();

        // Ensure 5 standard PBJ knowledge categories exist
        $standardKnowledgeCategories = [
            ['name' => 'Perencanaan Pengadaan', 'slug' => 'perencanaan-pengadaan', 'description' => 'Identifikasi kebutuhan, penetapan barang/jasa, penyusunan anggaran, dan RUP.'],
            ['name' => 'Persiapan Pengadaan', 'slug' => 'persiapan-pengadaan', 'description' => 'Penyusunan HPS, spesifikasi teknis, rancangan kontrak, dan penetapan uang muka/jaminan.'],
            ['name' => 'Pemilihan Penyedia', 'slug' => 'pemilihan-penyedia', 'description' => 'Metode pemilihan, dokumen pemilihan, evaluasi kualifikasi, sanggah, dan SPPBJ.'],
            ['name' => 'Pelaksanaan Kontrak', 'slug' => 'pelaksanaan-kontrak', 'description' => 'Penandatanganan kontrak, SPMK, adendum, pengendalian kontrak, dan mitigasi risiko.'],
            ['name' => 'Serah Terima & Evaluasi', 'slug' => 'serah-terima-evaluasi', 'description' => 'Pemeriksaan hasil pekerjaan (BAST), pembayaran, penilaian kinerja penyedia, dan audit.'],
        ];

        foreach ($standardKnowledgeCategories as $cat) {
            $existing = $db->table('knowledge_categories')->where('slug', $cat['slug'])->get()->getRowArray();
            if (! $existing) {
                $db->table('knowledge_categories')->insert(array_merge($cat, [
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }
        CLI::write("  [OPTIMIZED] `knowledge_categories` cleaned and standardized.", "green");

        // -------------------------------------------------------------
        // SECTION 3: REGULASI (REGULATIONS)
        // -------------------------------------------------------------
        CLI::write("--- [3] CLEANING REGULASI ---", "yellow");
        $regulationTables = [
            'regulations',
            'regulation_downloads',
            'regulation_procurement_stages',
            'regulation_relations',
            'regulation_versions',
            'regulation_competencies',
        ];

        foreach ($regulationTables as $tbl) {
            if ($db->tableExists($tbl)) {
                $countBefore = $db->table($tbl)->countAllResults();
                $db->table($tbl)->truncate();
                CLI::write("  [TRUNCATED] `{$tbl}` ({$countBefore} rows cleared)", "green");
            }
        }

        // Clean test/qa categories from regulation_categories
        $db->table('regulation_categories')
            ->like('name', 'TEST', 'after')
            ->orLike('slug', 'qa-audit', 'after')
            ->delete();

        CLI::write("  [OPTIMIZED] `regulation_categories` cleaned (Official categories preserved).", "green");

        // -------------------------------------------------------------
        // SECTION 4: BERITA & EVENT
        // -------------------------------------------------------------
        CLI::write("--- [4] CLEANING BERITA & EVENT ---", "yellow");
        $newsEventTables = [
            'news',
            'events',
            'event_registrations',
            'attendances',
        ];

        foreach ($newsEventTables as $tbl) {
            if ($db->tableExists($tbl)) {
                $countBefore = $db->table($tbl)->countAllResults();
                $db->table($tbl)->truncate();
                CLI::write("  [TRUNCATED] `{$tbl}` ({$countBefore} rows cleared)", "green");
            }
        }

        // -------------------------------------------------------------
        // SECTION 5: CERTIFICATES & OPERATIONAL TRANSITIONS
        // -------------------------------------------------------------
        CLI::write("--- [5] CLEANING CERTIFICATES & DUMMY WORKFLOWS ---", "yellow");
        $operationalTables = [
            'certificates',
            'approvals',
            'profile_verifications',
            'profile_change_requests',
            'workflow_transitions',
            'bookmarks',
            'notifications',
            'password_resets',
            'email_verifications',
            'role_requests',
            'contact_messages',
            'newsletter_subscribers',
            'satisfaction_surveys',
            'visitor_statistics',
        ];

        foreach ($operationalTables as $tbl) {
            if ($db->tableExists($tbl)) {
                $countBefore = $db->table($tbl)->countAllResults();
                $db->table($tbl)->truncate();
                CLI::write("  [TRUNCATED] `{$tbl}` ({$countBefore} rows cleared)", "green");
            }
        }

        // Reset certificate sequence
        if ($db->tableExists('certificate_sequences')) {
            $db->table('certificate_sequences')->truncate();
            $db->table('certificate_sequences')->insert([
                'year'        => (int) date('Y'),
                'last_number' => 0,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [RESET] `certificate_sequences` initialized.", "green");
        }

        // Clean audit logs: clear dummy user actions, insert system initialization log
        if ($db->tableExists('audit_logs')) {
            $db->table('audit_logs')->truncate();
            $db->table('audit_logs')->insert([
                'actor_id'        => 13,
                'action'          => 'system.clean_deploy_v1',
                'resource_type'   => 'system',
                'resource_id'     => 1,
                'result'          => 'success',
                'before_snapshot' => null,
                'after_snapshot'  => json_encode(['version' => 'v.1.0-release', 'status' => 'clean_initialized']),
                'reason'          => 'Inisialisasi sistem bersih persiapan deploy release v.1 BegawiPengadaan.',
                'ip_hash'         => hash('sha256', '127.0.0.1'),
                'user_agent_hash' => hash('sha256', 'CLI/ReleaseDeploy'),
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [RESET] `audit_logs` cleared and re-initialized with release v.1 log.", "green");
        }

        // -------------------------------------------------------------
        // SECTION 6: USERS CLEANUP (PRESERVE SUPERADMIN & ADMIN ONLY)
        // -------------------------------------------------------------
        CLI::write("--- [6] CLEANING PARTICIPANT USERS & PRESERVING ADMINS ---", "yellow");

        // 1. Delete profiles of non-admins
        $db->table('user_profiles')->whereNotIn('user_id', [13, 14])->delete();
        $db->table('asn_profiles')->whereNotIn('user_id', [13, 14])->delete();
        $db->table('public_profiles')->truncate();
        $db->table('academic_profiles')->truncate();
        $db->table('provider_profiles')->truncate();
        $db->table('user_procurement_roles')->whereNotIn('user_id', [13, 14])->delete();

        // 2. Delete user_roles of non-admins
        $db->table('user_roles')->whereNotIn('user_id', [13, 14])->delete();

        // 3. Delete non-admin users
        $deletedUsers = $db->table('users')->whereNotIn('id', [13, 14])->delete();
        CLI::write("  [DELETED] {$deletedUsers} dummy participant/non-admin users deleted.", "green");

        // 4. Ensure Superadmin (ID 13) has complete profile & role
        $existingProfile13 = $db->table('user_profiles')->where('user_id', 13)->get()->getRowArray();
        if (! $existingProfile13) {
            $db->table('user_profiles')->insert([
                'user_id'                   => 13,
                'address_line'              => 'Kompleks Kantor Gubernur Provinsi Lampung, Jl. R.W. Monginsidi No. 69',
                'province_code'             => '18',
                'regency_code'              => '18.71',
                'district_code'             => '18.71.01',
                'village_code'              => null,
                'profile_completion_status' => 'complete',
                'created_at'                => date('Y-m-d H:i:s'),
                'updated_at'                => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [CREATED] Superadmin (ID 13) user profile initialized.", "green");
        }

        $existingAsn13 = $db->table('asn_profiles')->where('user_id', 13)->get()->getRowArray();
        if (! $existingAsn13) {
            $db->table('asn_profiles')->insert([
                'user_id'             => 13,
                'organization_unit_id'=> null,
                'employee_identifier' => '198501012010011001',
                'employment_status'   => 'pns',
                'origin_type'         => 'pemprov_lampung',
                'institution_name'    => 'Pemerintah Provinsi Lampung',
                'unit_name'           => 'Biro Pengadaan Barang dan Jasa',
                'position_name'       => 'Super Administrator PBJ',
                'rank_grade'          => 'Pembina Utama Muda (IV/c)',
                'official_email'      => 'superadmin@begawipbj.go.id',
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
            CLI::write("  [CREATED] Superadmin (ID 13) ASN profile initialized.", "green");
        }

        // 5. Clean test roles
        $db->table('roles')
            ->whereIn('id', [17, 19])
            ->orLike('name', 'TEST', 'after')
            ->delete();
        CLI::write("  [OPTIMIZED] Test dummy roles removed from `roles` table.", "green");

        // -------------------------------------------------------------
        // SECTION 7: FILE ASSETS (CLEAN DUMMY FILES)
        // -------------------------------------------------------------
        CLI::write("--- [7] CLEANING DUMMY FILE ASSETS ---", "yellow");
        // Keep essential branding/landing assets
        $clearedAssets = $db->table('file_assets')
            ->whereNotIn('resource_type', ['institution_seal', 'page_banner'])
            ->delete();
        CLI::write("  [DELETED] {$clearedAssets} dummy file asset records cleaned.", "green");

        // Clean dummy upload files in public/uploads/learning
        $learningUploadPath = FCPATH . 'uploads/learning';
        if (is_dir($learningUploadPath)) {
            $files = glob($learningUploadPath . '/*');
            $fCount = 0;
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                    $fCount++;
                }
            }
            CLI::write("  [CLEANED] {$fCount} dummy files removed from public/uploads/learning.", "green");
        }

        // Re-enable Foreign Key Checks
        $db->query("SET FOREIGN_KEY_CHECKS = 1;");
        CLI::write("Foreign key checks re-enabled.", "yellow");
        CLI::newLine();

        // -------------------------------------------------------------
        // SECTION 8: VERIFICATION & SUMMARY SCORECARD
        // -------------------------------------------------------------
        CLI::write("==================================================================", "cyan");
        CLI::write("                  CLEANUP VERIFICATION SUMMARY                   ", "cyan");
        CLI::write("==================================================================", "cyan");

        $verifyTables = [
            'users'                 => 'Users (Should be 2)',
            'user_roles'            => 'User Roles (Should be 2)',
            'user_profiles'         => 'User Profiles (Should be 2)',
            'learning_classes'      => 'Learning Classes (Should be 0)',
            'courses'               => 'Courses (Should be 0)',
            'enrollments'           => 'Enrollments (Should be 0)',
            'knowledge_articles'    => 'Knowledge Articles (Should be 0)',
            'regulations'           => 'Regulations (Should be 0)',
            'news'                  => 'News (Should be 0)',
            'events'                => 'Events (Should be 0)',
            'event_registrations'   => 'Event Registrations (Should be 0)',
            'certificates'          => 'Certificates (Should be 0)',
            'notifications'         => 'Notifications (Should be 0)',
            'bookmarks'             => 'Bookmarks (Should be 0)',
        ];

        foreach ($verifyTables as $tbl => $lbl) {
            $c = $db->table($tbl)->countAllResults();
            CLI::write(sprintf("  %-30s: %d rows", $lbl, $c), ($c <= 2) ? 'green' : 'yellow');
        }

        CLI::newLine();
        CLI::write("[SUCCESS] Database is now clean, consistent, and ready for release v.1 deployment!", "green");
        CLI::write("==================================================================", "cyan");

        return 0;
    }
}
