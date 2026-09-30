<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Baseline schema for a fresh Begawi PBJ installation.
 * Permission data is managed by the application seeders.
 */
class RebuildBegawiSchema extends Migration
{
    public function up()
    {
        // 2026-01-01-000001_CreateUsersTable.php
        $this->forge->addField([
                    'id' => [
                        'type'           => 'BIGINT',
                        'unsigned'       => true,
                        'auto_increment' => true,
                    ],
                    'username' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 100,
                    ],
                    'email' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 150,
                    ],
                    'password_hash' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                    ],
                    'user_type' => [
                        'type'       => 'ENUM',
                        'constraint' => ['asn', 'umum'],
                        'default'    => 'umum',
                    ],
                    'profile_type' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 32,
                        'default'    => 'public',
                    ],
                    'nip' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 25,
                        'null'       => true,
                    ],
                    'instansi' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 150,
                        'null'       => true,
                    ],
                    'status' => [
                        'type'       => 'ENUM',
                        'constraint' => ['pending', 'active', 'inactive', 'rejected'],
                        'default'    => 'pending',
                    ],
                    'email_verified_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                    ],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('username');
                $this->forge->addUniqueKey('email');
                $this->forge->createTable('users');

        // 2026-01-01-000002_CreateRolesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'name' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'slug' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('slug');
                $this->forge->createTable('roles');

        // 2026-01-01-000003_CreateUserRolesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'role_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['user_id', 'role_id']);
                $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('user_roles');

        // 2026-01-01-000004_CreatePermissionsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'name' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'module' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('name');
                $this->forge->createTable('permissions');

        // 2026-01-01-000005_CreateRolePermissionsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'role_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'permission_id' => ['type' => 'BIGINT', 'unsigned' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['role_id', 'permission_id']);
                $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('permission_id', 'permissions', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('role_permissions');

        // 2026-01-01-000006_CreateAuditLogsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'user_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'action' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'module' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                    'target_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                    'target_id' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
                    'metadata' => ['type' => 'TEXT', 'null' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('user_id', 'users', 'id', 'SET NULL', 'CASCADE');
                $this->forge->createTable('audit_logs');

        // 2026-01-02-000001_CreateCoursesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'description' => ['type' => 'TEXT', 'null' => true],
                    'category' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'published', 'archived'], 'default' => 'draft'],
                    'created_by' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('created_by', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('courses');

        // 2026-01-02-000002_CreateClassesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'course_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'trainer_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'start_date' => ['type' => 'DATE'],
                    'end_date' => ['type' => 'DATE'],
                    'quota' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'status' => ['type' => 'ENUM', 'constraint' => ['scheduled', 'ongoing', 'completed', 'cancelled'], 'default' => 'scheduled'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('trainer_id', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('classes');

        // 2026-01-02-000003_CreateModulesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'class_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'sequence_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('class_id', 'classes', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('modules');

        // 2026-01-02-000004_CreateLessonsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'module_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'content_type' => ['type' => 'ENUM', 'constraint' => ['document', 'video', 'text']],
                    'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'content_text' => ['type' => 'TEXT', 'null' => true],
                    'sequence_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('module_id', 'modules', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('lessons');

        // 2026-01-02-000005_CreateAssessmentsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'module_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'type' => ['type' => 'ENUM', 'constraint' => ['quiz', 'exam', 'assignment']],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'passing_score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 70.00],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('module_id', 'modules', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('assessments');

        // 2026-01-02-000006_CreateQuestionsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'assessment_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'question_text' => ['type' => 'TEXT'],
                    'question_type' => ['type' => 'ENUM', 'constraint' => ['multiple_choice', 'essay']],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('assessment_id', 'assessments', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('questions');

        // 2026-01-02-000007_CreateAnswersTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'question_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'answer_text' => ['type' => 'TEXT'],
                    'is_correct' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('question_id', 'questions', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('answers');

        // 2026-01-02-000008_CreateEnrollmentsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'class_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'enrollment_status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
                    'final_status' => ['type' => 'ENUM', 'constraint' => ['in_progress', 'passed', 'failed'], 'default' => 'in_progress'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['user_id', 'class_id']);
                $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('class_id', 'classes', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('enrollments');

        // 2026-01-02-000009_CreateProgressTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'enrollment_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'lesson_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'completed_at' => ['type' => 'DATETIME', 'null' => true],
                    'score' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['enrollment_id', 'lesson_id']);
                $this->forge->addForeignKey('enrollment_id', 'enrollments', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('progress');

        // 2026-01-02-000010_CreateAssessmentResultsTable.php
        $this->forge->addField([
                    'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'enrollment_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                    'assessment_id'    => ['type' => 'BIGINT', 'unsigned' => true],
                    'total_question'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'correct_answer'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'score'            => ['type' => 'DECIMAL', 'constraint' => '5,2'],
                    'is_passed'        => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
                    'answers_snapshot' => ['type' => 'TEXT', 'null' => true], // JSON {question_id: answer_id} untuk bukti/audit
                    'submission_text'  => ['type' => 'TEXT', 'null' => true],
                    'evaluation_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
                    'evaluator_id'     => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
                    'evaluator_feedback' => ['type' => 'TEXT', 'null' => true],
                    'evaluated_at'     => ['type' => 'DATETIME', 'null' => true],
                    'created_at'       => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addKey(['enrollment_id', 'assessment_id']); // index biasa, bukan unique — lihat catatan di atas
                $this->forge->addForeignKey('enrollment_id', 'enrollments', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('assessment_id', 'assessments', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('evaluator_id', 'users', 'id', 'SET NULL', 'CASCADE');
                $this->forge->createTable('assessment_results');

        // 2026-01-03-000001_CreateCategoriesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'name' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'type' => ['type' => 'ENUM', 'constraint' => ['kms', 'regulation'], 'default' => 'kms'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->createTable('categories');

        // 2026-01-03-000002_CreateArticlesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'category_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'content' => ['type' => 'LONGTEXT'],
                    'author_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'view_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                    'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'published'], 'default' => 'draft'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->addForeignKey('author_id', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('articles');

        // 2026-01-03-000003_CreateFaqTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'category_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'question' => ['type' => 'VARCHAR', 'constraint' => 255],
                    'answer' => ['type' => 'TEXT'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('faq');

        // 2026-01-03-000004_CreateCaseStudiesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'category_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'content' => ['type' => 'LONGTEXT'],
                    'author_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->addForeignKey('author_id', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('case_studies');

        // 2026-01-03-000005_CreateTemplatesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'category_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 200],
                    'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('templates');

        // 2026-01-03-000006_CreateArticleBookmarksTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'article_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['user_id', 'article_id']);
                $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('article_id', 'articles', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('article_bookmarks');

        // 2026-01-04-000001_CreateRegulationCategoriesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'name' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->createTable('regulation_categories');

                $this->forge->addField([
                    'kode' => ['type' => 'VARCHAR', 'constraint' => 13],
                    'nama' => ['type' => 'VARCHAR', 'constraint' => 255],
                ]);
                $this->forge->addKey('kode', true);
                $this->forge->createTable('wilayah');

                // 2026-01-04-000002_CreateRegulationsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'regulation_category_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'title' => ['type' => 'VARCHAR', 'constraint' => 255],
                    'nomor_regulasi' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'tahun' => ['type' => 'YEAR'],
                    'status' => ['type' => 'ENUM', 'constraint' => ['berlaku', 'tidak_berlaku'], 'default' => 'berlaku'],
                    'uploaded_by' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('nomor_regulasi');
                $this->forge->addForeignKey('regulation_category_id', 'regulation_categories', 'id', 'CASCADE', 'RESTRICT');
                $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'RESTRICT');
                $this->forge->createTable('regulations');

        // 2026-01-04-000003_CreateRegulationVersionsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'regulation_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'version_number' => ['type' => 'INT', 'unsigned' => true],
                    'file_path' => ['type' => 'VARCHAR', 'constraint' => 255],
                    'change_notes' => ['type' => 'TEXT', 'null' => true],
                    'uploaded_by' => ['type' => 'BIGINT', 'unsigned' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey(['regulation_id', 'version_number']);
                $this->forge->addForeignKey('regulation_id', 'regulations', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'RESTRICT');
                $this->forge->createTable('regulation_versions');

        // 2026-01-05-000001_CreateCertificatesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'enrollment_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'certificate_number' => ['type' => 'VARCHAR', 'constraint' => 50],
                    'issued_date' => ['type' => 'DATE'],
                    'status' => ['type' => 'ENUM', 'constraint' => ['issued', 'revoked'], 'default' => 'issued'],
                    'file_path' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                    'qr_token' => ['type' => 'VARCHAR', 'constraint' => 64],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('enrollment_id');
                $this->forge->addUniqueKey('certificate_number');
                $this->forge->addUniqueKey('qr_token');
                $this->forge->addForeignKey('enrollment_id', 'enrollments', 'id', 'CASCADE', 'RESTRICT');
                $this->forge->createTable('certificates');

        // 2026-01-05-000002_CreateCertificateVerificationsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'certificate_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'verified_at' => ['type' => 'DATETIME'],
                    'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('certificate_id', 'certificates', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('certificate_verifications');

        // 2026-01-06-000001_CreateApprovalsTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'approvable_type' => ['type' => 'ENUM', 'constraint' => ['user_registration', 'enrollment', 'certificate']],
                    'approvable_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'requested_by' => ['type' => 'BIGINT', 'unsigned' => true],
                    'current_status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addKey(['approvable_type', 'approvable_id']);
                $this->forge->addForeignKey('requested_by', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('approvals');

        // 2026-01-06-000002_CreateApprovalHistoriesTable.php
        $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'approval_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'acted_by' => ['type' => 'BIGINT', 'unsigned' => true],
                    'action' => ['type' => 'ENUM', 'constraint' => ['approve', 'reject']],
                    'notes' => ['type' => 'TEXT', 'null' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
        
                $this->forge->addKey('id', true);
                $this->forge->addForeignKey('approval_id', 'approvals', 'id', 'CASCADE', 'CASCADE');
                $this->forge->addForeignKey('acted_by', 'users', 'id', 'RESTRICT', 'CASCADE');
                $this->forge->createTable('approval_histories');

        // 2026-09-12-000001_CreateCertificateSequencesTable.php
        $this->forge->addField([
                    'year'        => ['type' => 'SMALLINT', 'unsigned' => true],
                    'last_number' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
                ]);
        
                $this->forge->addKey('year', true);
                $this->forge->createTable('certificate_sequences');

                // 2026-09-12-000004_CreateUserProfileTables.php
                $this->forge->addField([
                    'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
                    'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
                    'full_name' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'title_prefix' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
                    'title_suffix' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
                    'phone' => ['type' => 'VARCHAR', 'constraint' => 25, 'null' => true],
                    'province' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'regency' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'district' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                    'province_code' => ['type' => 'VARCHAR', 'constraint' => 13, 'null' => true],
                    'regency_code' => ['type' => 'VARCHAR', 'constraint' => 13, 'null' => true],
                    'district_code' => ['type' => 'VARCHAR', 'constraint' => 13, 'null' => true],
                    'village_code' => ['type' => 'VARCHAR', 'constraint' => 13, 'null' => true],
                    'created_at' => ['type' => 'DATETIME', 'null' => true],
                    'updated_at' => ['type' => 'DATETIME', 'null' => true],
                ]);
                $this->forge->addKey('id', true);
                $this->forge->addUniqueKey('user_id');
                $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
                $this->forge->createTable('user_profiles');
        
                $this->createDetailTable('user_asn_profiles', [
                    'identity_number' => ['type' => 'VARCHAR', 'constraint' => 25],
                    'identity_type' => ['type' => 'VARCHAR', 'constraint' => 10],
                    'asn_status' => ['type' => 'VARCHAR', 'constraint' => 10],
                    'institution' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'work_unit' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'position' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'grade' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                    'pbj_role' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
                ]);
                $this->createDetailTable('user_provider_profiles', [
                    'nib' => ['type' => 'VARCHAR', 'constraint' => 30],
                    'business_name' => ['type' => 'VARCHAR', 'constraint' => 180],
                    'responsible_person' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'company_email' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'company_phone' => ['type' => 'VARCHAR', 'constraint' => 25],
                    'province' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'regency' => ['type' => 'VARCHAR', 'constraint' => 100],
                ]);
                $this->createDetailTable('user_academic_profiles', [
                    'institution' => ['type' => 'VARCHAR', 'constraint' => 180],
                    'expertise' => ['type' => 'VARCHAR', 'constraint' => 120],
                    'email_contact' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'phone_contact' => ['type' => 'VARCHAR', 'constraint' => 25],
                    'province' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'regency' => ['type' => 'VARCHAR', 'constraint' => 100],
                ]);
                $this->createDetailTable('user_public_profiles', [
                    'nik' => ['type' => 'VARCHAR', 'constraint' => 32, 'null' => true],
                    'email_contact' => ['type' => 'VARCHAR', 'constraint' => 150],
                    'phone_contact' => ['type' => 'VARCHAR', 'constraint' => 25],
                    'province' => ['type' => 'VARCHAR', 'constraint' => 100],
                    'regency' => ['type' => 'VARCHAR', 'constraint' => 100],
                ]);
        
    }

    private function createDetailTable(string $table, array $fields): void
    {
        $this->forge->addField(array_merge([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
        ], $fields, [
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]));
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable($table);
    }

    public function down()
    {
        $tables = [
            'user_profiles',
            'user_asn_profiles',
            'user_provider_profiles',
            'user_academic_profiles',
            'user_public_profiles',
            'certificate_sequences',
            'certificate_verifications',
            'certificates',
            'approval_histories',
            'approvals',
            'regulation_versions',
            'regulations',
            'wilayah',
            'regulation_categories',
            'article_bookmarks',
            'templates',
            'case_studies',
            'faq',
            'articles',
            'categories',
            'assessment_results',
            'progress',
            'enrollments',
            'answers',
            'questions',
            'assessments',
            'lessons',
            'modules',
            'classes',
            'courses',
            'audit_logs',
            'role_permissions',
            'user_roles',
            'permissions',
            'roles',
            'users',
        ];

        foreach ($tables as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
