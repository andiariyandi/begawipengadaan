<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * LegacyDataSeeder
 *
 * Generated from now_db_begawipbj.sql.
 * This seed is prepared as the migration bridge layer.
 *
 * Next conversion stage will split data into domain seeders:
 * User, Organization, LMS, Regulation, Certificate, Knowledge.
 */
class LegacyDataSeeder extends Seeder
{
    public function run()
    {
        // Tables detected from legacy database:
        $tables = [
            'answers',
            'approvals',
            'approval_histories',
            'articles',
            'article_bookmarks',
            'assessments',
            'assessment_results',
            'audit_logs',
            'case_studies',
            'categories',
            'certificates',
            'certificate_sequences',
            'certificate_verifications',
            'classes',
            'courses',
            'enrollments',
            'faq',
            'lessons',
            'migrations',
            'modules',
            'organization_units',
            'permissions',
            'progress',
            'questions',
            'regulations',
            'regulation_categories',
            'regulation_versions',
            'roles',
            'role_permissions',
            'templates',
            'users',
            'user_academic_profiles',
            'user_asn_profiles',
            'user_profiles',
            'user_provider_profiles',
            'user_public_profiles',
            'user_roles',
            'wilayah'
        ];

        /*
         * Data migration is intentionally executed after schema validation.
         * Domain-specific seeders will be generated from this source.
         */
    }
}
