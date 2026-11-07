# BegawiPengadaan — Database Blueprint

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Ringkasan Eksekutif Arsitektur Database

Basis data **BegawiPengadaan** dirancang dengan arsitektur relasional berstandar enterprise yang mengutamakan integritas referensial ketat (*Foreign Keys*), validasi domain pada level basis data (*22 Kategori Otoritatif / 58 CHECK Constraints*), mekanisme pencatatan riwayat (*soft deletes* & *audit logs*), serta kepatuhan mutlak terhadap **Zero Migration Drift** (tepat 17 berkas migrasi resmi CodeIgniter 4).

- **Nama Database:** `db_begawipbj`
- **Database Engine:** MySQL 8.4+ (Default: InnoDB, `utf8mb4_unicode_ci`)
- **Total Tabel:** **95 Tabel Aktif**
- **Total Kolom:** **1.127 Kolom Terdaftar**
- **Total CHECK Constraints:** **58 Constraints Aktif**
- **Total Foreign Keys:** **157 Relasi Integritas**
- **Total Berkas Migrasi:** **17 Migrasi Resmi** (Batch 1–17)

---

## 2. Pengelompokan 95 Tabel Berdasarkan 10 Domain Sistem

### Domain 1: Identitas, Akun, Profil Multi-Jalur & RBAC (16 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `users` | Terkelola | Entitas persistence tabel users |
| 2 | `user_roles` | Terkelola | Entitas persistence tabel user_roles |
| 3 | `roles` | Terkelola | Entitas persistence tabel roles |
| 4 | `permissions` | Terkelola | Entitas persistence tabel permissions |
| 5 | `role_permissions` | Terkelola | Entitas persistence tabel role_permissions |
| 6 | `user_profiles` | Terkelola | Entitas persistence tabel user_profiles |
| 7 | `asn_profiles` | Terkelola | Entitas persistence tabel asn_profiles |
| 8 | `provider_profiles` | Terkelola | Entitas persistence tabel provider_profiles |
| 9 | `academic_profiles` | Terkelola | Entitas persistence tabel academic_profiles |
| 10 | `public_profiles` | Terkelola | Entitas persistence tabel public_profiles |
| 11 | `procurement_roles` | Terkelola | Entitas persistence tabel procurement_roles |
| 12 | `user_procurement_roles` | Terkelola | Entitas persistence tabel user_procurement_roles |
| 13 | `role_requests` | Terkelola | Entitas persistence tabel role_requests |
| 14 | `profile_verifications` | Terkelola | Entitas persistence tabel profile_verifications |
| 15 | `profile_change_requests` | Terkelola | Entitas persistence tabel profile_change_requests |
| 16 | `password_resets` | Terkelola | Entitas persistence tabel password_resets |

### Domain 2: Learning Management System (LMS) & Kurikulum (15 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `programs` | Terkelola | Entitas persistence tabel programs |
| 2 | `courses` | Terkelola | Entitas persistence tabel courses |
| 3 | `learning_classes` | Terkelola | Entitas persistence tabel learning_classes |
| 4 | `class_trainers` | Terkelola | Entitas persistence tabel class_trainers |
| 5 | `class_evaluators` | Terkelola | Entitas persistence tabel class_evaluators |
| 6 | `learning_modules` | Terkelola | Entitas persistence tabel learning_modules |
| 7 | `lessons` | Terkelola | Entitas persistence tabel lessons |
| 8 | `quizzes` | Terkelola | Entitas persistence tabel quizzes |
| 9 | `quiz_questions` | Terkelola | Entitas persistence tabel quiz_questions |
| 10 | `quiz_options` | Terkelola | Entitas persistence tabel quiz_options |
| 11 | `quiz_attempts` | Terkelola | Entitas persistence tabel quiz_attempts |
| 12 | `quiz_answers` | Terkelola | Entitas persistence tabel quiz_answers |
| 13 | `assignments` | Terkelola | Entitas persistence tabel assignments |
| 14 | `assignment_submissions` | Terkelola | Entitas persistence tabel assignment_submissions |
| 15 | `assignment_submission_files` | Terkelola | Entitas persistence tabel assignment_submission_files |

### Domain 3: Progres Belajar, Evaluasi & Umpan Balik (4 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `enrollments` | Terkelola | Entitas persistence tabel enrollments |
| 2 | `learning_progress` | Terkelola | Entitas persistence tabel learning_progress |
| 3 | `feedbacks` | Terkelola | Entitas persistence tabel feedbacks |
| 4 | `pre_tests` | Terkelola | Entitas persistence tabel pre_tests |

### Domain 4: Knowledge Management System (KMS) & Bunga Rampai (6 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `knowledge_categories` | Terkelola | Entitas persistence tabel knowledge_categories |
| 2 | `knowledge_tags` | Terkelola | Entitas persistence tabel knowledge_tags |
| 3 | `knowledge_articles` | Terkelola | Entitas persistence tabel knowledge_articles |
| 4 | `knowledge_media` | Terkelola | Entitas persistence tabel knowledge_media |
| 5 | `knowledge_article_categories` | Terkelola | Entitas persistence tabel knowledge_article_categories |
| 6 | `knowledge_article_tags` | Terkelola | Entitas persistence tabel knowledge_article_tags |

### Domain 5: Tata Kelola Naskah Hukum & Regulasi PBJ (7 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `regulations` | Terkelola | Entitas persistence tabel regulations |
| 2 | `regulation_categories` | Terkelola | Entitas persistence tabel regulation_categories |
| 3 | `regulation_versions` | Terkelola | Entitas persistence tabel regulation_versions |
| 4 | `regulation_relations` | Terkelola | Entitas persistence tabel regulation_relations |
| 5 | `regulation_competencies` | Terkelola | Entitas persistence tabel regulation_competencies |
| 6 | `regulation_downloads` | Terkelola | Entitas persistence tabel regulation_downloads |
| 7 | `regulation_procurement_stages` | Terkelola | Entitas persistence tabel regulation_procurement_stages |

### Domain 6: Komunikasi Publik, Warta Berita & Event (8 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `news` | Terkelola | Entitas persistence tabel news |
| 2 | `news_categories` | Terkelola | Entitas persistence tabel news_categories |
| 3 | `events` | Terkelola | Entitas persistence tabel events |
| 4 | `event_registrations` | Terkelola | Entitas persistence tabel event_registrations |
| 5 | `attendances` | Terkelola | Entitas persistence tabel attendances |
| 6 | `newsletter_subscribers` | Terkelola | Entitas persistence tabel newsletter_subscribers |
| 7 | `contact_messages` | Terkelola | Entitas persistence tabel contact_messages |
| 8 | `helpdesk_topics` | Terkelola | Entitas persistence tabel helpdesk_topics |

### Domain 7: Master Sertifikat & Tanda Tangan Elektronik (TTE) (3 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `certificates` | Terkelola | Entitas persistence tabel certificates |
| 2 | `certificate_sequences` | Terkelola | Entitas persistence tabel certificate_sequences |
| 3 | `certificate_templates` | Terkelola | Entitas persistence tabel certificate_templates |

### Domain 8: Government Intelligence & SDM Fungsional PBJ (7 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `jf_ppbj_records` | Terkelola | Entitas persistence tabel jf_ppbj_records |
| 2 | `jf_ppbj_levels` | Terkelola | Entitas persistence tabel jf_ppbj_levels |
| 3 | `competency_certificates` | Terkelola | Entitas persistence tabel competency_certificates |
| 4 | `competency_certificate_types` | Terkelola | Entitas persistence tabel competency_certificate_types |
| 5 | `competencies` | Terkelola | Entitas persistence tabel competencies |
| 6 | `competency_gap_snapshots` | Terkelola | Entitas persistence tabel competency_gap_snapshots |
| 7 | `procurement_stages` | Terkelola | Entitas persistence tabel procurement_stages |

### Domain 9: Landing Page CMS, Sorotan & Interaksi Publik (11 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `home_sliders` | Terkelola | Entitas persistence tabel home_sliders |
| 2 | `page_banners` | Terkelola | Entitas persistence tabel page_banners |
| 3 | `feature_highlights` | Terkelola | Entitas persistence tabel feature_highlights |
| 4 | `external_portals` | Terkelola | Entitas persistence tabel external_portals |
| 5 | `testimonials` | Terkelola | Entitas persistence tabel testimonials |
| 6 | `team_members` | Terkelola | Entitas persistence tabel team_members |
| 7 | `site_announcements` | Terkelola | Entitas persistence tabel site_announcements |
| 8 | `satisfaction_surveys` | Terkelola | Entitas persistence tabel satisfaction_surveys |
| 9 | `survey_aspects` | Terkelola | Entitas persistence tabel survey_aspects |
| 10 | `service_categories` | Terkelola | Entitas persistence tabel service_categories |
| 11 | `bookmarks` | Terkelola | Entitas persistence tabel bookmarks |

### Domain 10: Sistem, Audit Trail, Storage, Log & RUP Audit Engine (18 Tabel)

| No | Nama Tabel | Estimasi Baris | Deskripsi Fungsi Operasional |
|---|---|:---:|---|
| 1 | `file_assets` | Terkelola | Entitas persistence tabel file_assets |
| 2 | `audit_logs` | Terkelola | Entitas persistence tabel audit_logs |
| 3 | `notifications` | Terkelola | Entitas persistence tabel notifications |
| 4 | `approvals` | Terkelola | Entitas persistence tabel approvals |
| 5 | `workflow_transitions` | Terkelola | Entitas persistence tabel workflow_transitions |
| 6 | `site_settings` | Terkelola | Entitas persistence tabel site_settings |
| 7 | `organization_units` | Terkelola | Entitas persistence tabel organization_units |
| 8 | `visitor_statistics` | Terkelola | Entitas persistence tabel visitor_statistics |
| 9 | `visitor_ip_logs` | Terkelola | Entitas persistence tabel visitor_ip_logs |
| 10 | `exports` | Terkelola | Entitas persistence tabel exports |
| 11 | `wilayah` | Terkelola | Entitas persistence tabel wilayah |
| 12 | `email_verifications` | Terkelola | Entitas persistence tabel email_verifications |
| 13 | `migrations` | Terkelola | Entitas persistence tabel migrations |
| 14 | `rup_audit_batches` | Terkelola | Entitas persistence tabel rup_audit_batches |
| 15 | `rup_audit_packages` | Terkelola | Entitas persistence tabel rup_audit_packages |
| 16 | `rup_audit_clusters` | Terkelola | Entitas persistence tabel rup_audit_clusters |
| 17 | `rup_audit_violations` | Terkelola | Entitas persistence tabel rup_audit_violations |
| 18 | `rup_audit_opd_summaries` | Terkelola | Entitas persistence tabel rup_audit_opd_summaries |

---

## 3. Daftar Lengkap 58 CHECK Constraints Otoritatif MySQL

Seluruh CHECK constraint berikut dievaluasi secara aktif pada MySQL 8.4+ untuk menjamin integritas status alur kerja dan mencegah injeksi status tidak sah pada level basis data:

| No | Nama Tabel | Nama Constraint | Klausul Pengecekan (Check Clause) |
|---|---|---|---|
| 1 | `approvals` | `approvals_status_chk` | `(`status` in ('pending','in_review','approved','rejected','revision_required','cancelled','expired'))` |
| 2 | `asn_profiles` | `asn_profiles_origin_type_chk` | `(`origin_type` in ('pemprov_lampung','kementerian_lembaga','pemerintah_daerah_lain','pemerintah_desa','bumn_bumd','lainnya'))` |
| 3 | `assignment_submissions` | `assignment_submissions_score_chk` | `((`score` is null) or ((`score` >= 0.00) and (`score` <= 100.00)))` |
| 4 | `assignment_submissions` | `assignment_submissions_status_chk` | `(`status` in ('draft','submitted','in_review','revision_required','graded','cancelled'))` |
| 5 | `attendances` | `attendances_status_chk` | `(`status` in ('present','absent','excused'))` |
| 6 | `audit_logs` | `audit_logs_result_chk` | `(`result` in ('success','failed','denied','partial'))` |
| 7 | `certificate_templates` | `certificate_templates_source_type_chk` | `(`source_type` in ('learning','event'))` |
| 8 | `certificates` | `certificates_source_type_chk` | `(`source_type` in ('learning_enrollment','event_registration'))` |
| 9 | `certificates` | `certificates_status_chk` | `(`status` in ('pending','issued','revoked','expired'))` |
| 10 | `class_evaluators` | `class_evaluators_status_chk` | `(`status` in ('active','revoked'))` |
| 11 | `class_trainers` | `class_trainers_status_chk` | `(`status` in ('active','revoked'))` |
| 12 | `competency_certificates` | `competency_certificates_status_chk` | `(`status` in ('pending','verified','rejected','expired'))` |
| 13 | `competency_gap_snapshots` | `competency_gap_snapshots_gap_chk` | `(`gap_count` = (`required_count` - `available_count`))` |
| 14 | `enrollments` | `enrollments_final_score_chk` | `((`final_score` is null) or ((`final_score` >= 0.00) and (`final_score` <= 100.00)))` |
| 15 | `enrollments` | `enrollments_status_chk` | `(`status` in ('pending','active','completed','withdrawn','cancelled'))` |
| 16 | `event_registrations` | `event_registrations_status_chk` | `(`status` in ('registered','waitlisted','cancelled','rejected'))` |
| 17 | `events` | `events_mode_chk` | `(`mode` in ('online','offline','hybrid'))` |
| 18 | `events` | `events_period_chk` | `(`end_at` >= `start_at`)` |
| 19 | `events` | `events_status_chk` | `(`status` in ('draft','coming_soon','open_registration','full','finished','cancelled'))` |
| 20 | `events` | `events_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 21 | `exports` | `exports_status_chk` | `(`status` in ('queued','generating','ready','failed','expired','deleted'))` |
| 22 | `feedbacks` | `feedbacks_rating_chk` | `((`rating` is null) or ((`rating` >= 1) and (`rating` <= 5)))` |
| 23 | `feedbacks` | `feedbacks_status_chk` | `(`status` in ('not_started','submitted'))` |
| 24 | `file_assets` | `file_assets_lifecycle_status_chk` | `(`lifecycle_status` in ('pending_upload','stored','scanned','available','rejected','quarantined','deleted'))` |
| 25 | `file_assets` | `file_assets_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 26 | `jf_ppbj_records` | `jf_ppbj_records_change_type_chk` | `(`change_type` in ('addition','promotion','transfer_in','transfer_out','retirement','deactivation','update'))` |
| 27 | `knowledge_articles` | `knowledge_articles_type_chk` | `(`type` in ('article','faq','case_study','template'))` |
| 28 | `knowledge_articles` | `knowledge_articles_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 29 | `knowledge_articles` | `knowledge_articles_workflow_chk` | `(`workflow_status` in ('draft','submitted','in_review','approved','published','revision_required','rejected','archived'))` |
| 30 | `knowledge_media` | `knowledge_media_type_chk` | `(`type` in ('image','poster','flyer','infographic','gallery','video_file','youtube','social_link','external_link'))` |
| 31 | `knowledge_media` | `knowledge_media_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 32 | `knowledge_media` | `knowledge_media_workflow_chk` | `(`workflow_status` in ('draft','submitted','in_review','approved','published','revision_required','rejected','archived'))` |
| 33 | `learning_classes` | `learning_classes_passing_grade_chk` | `((`passing_grade` >= 0.00) and (`passing_grade` <= 100.00))` |
| 34 | `learning_classes` | `learning_classes_post_test_weight_chk` | `(`post_test_weight` = 60.00)` |
| 35 | `learning_classes` | `learning_classes_weights_chk` | `(((`quiz_weight` + `assignment_weight`) + `post_test_weight`) = 100.00)` |
| 36 | `learning_progress` | `learning_progress_percent_chk` | `((`progress_percent` >= 0.00) and (`progress_percent` <= 100.00))` |
| 37 | `learning_progress` | `learning_progress_status_chk` | `(`status` in ('not_started','in_progress','completed'))` |
| 38 | `lessons` | `lessons_content_type_chk` | `(`content_type` in ('text','document','video','link'))` |
| 39 | `lessons` | `lessons_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 40 | `news` | `news_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 41 | `news` | `news_workflow_chk` | `(`workflow_status` in ('draft','submitted','in_review','approved','published','revision_required','rejected','archived'))` |
| 42 | `profile_change_requests` | `profile_change_requests_status_chk` | `(`status` in ('draft','submitted','in_review','revision_required','approved','rejected','cancelled','archived'))` |
| 43 | `profile_verifications` | `profile_verifications_status_chk` | `(`status` in ('pending','in_review','approved','rejected','revision_required'))` |
| 44 | `quiz_answers` | `quiz_answers_score_awarded_chk` | `((`score_awarded` is null) or (`score_awarded` >= 0.00))` |
| 45 | `quiz_attempts` | `quiz_attempts_score_chk` | `((`score` is null) or ((`score` >= 0.00) and (`score` <= 100.00)))` |
| 46 | `quiz_attempts` | `quiz_attempts_status_chk` | `(`status` in ('started','submitted','graded','invalidated'))` |
| 47 | `quiz_questions` | `quiz_questions_points_chk` | `(`points` >= 0.00)` |
| 48 | `quizzes` | `quizzes_type_chk` | `(`type` in ('pre_test','lesson_quiz','post_test'))` |
| 49 | `regulation_relations` | `regulation_relations_type_chk` | `(`relation_type` in ('amends','amended_by','revokes','revoked_by','replaces','replaced_by','implements','implemented_by','references','referenced_by'))` |
| 50 | `regulations` | `regulations_legal_status_chk` | `(`legal_status` in ('active','amended','revoked','expired'))` |
| 51 | `regulations` | `regulations_visibility_chk` | `(`visibility` in ('public','internal','restricted'))` |
| 52 | `regulations` | `regulations_workflow_chk` | `(`workflow_status` in ('draft','submitted','in_review','approved','published','revision_required','rejected','archived'))` |
| 53 | `role_requests` | `role_requests_status_chk` | `(`status` in ('draft','submitted','in_review','revision_required','approved','rejected','cancelled','archived'))` |
| 54 | `user_procurement_roles` | `user_procurement_roles_status_chk` | `(`verification_status` in ('self_declared','pending','verified','rejected'))` |
| 55 | `user_roles` | `user_roles_status_chk` | `(`status` in ('active','revoked','expired'))` |
| 56 | `users` | `users_account_status_chk` | `(`account_status` in ('draft','submitted','pending_verification','revision_required','active','rejected','inactive','suspended','archived'))` |
| 57 | `users` | `users_profile_type_chk` | `(`profile_type` in ('asn','provider','academic','public'))` |
| 58 | `workflow_transitions` | `workflow_transitions_actor_type_chk` | `(`actor_type` in ('user','system'))` |

---

## 4. Matriks Integritas Foreign Key (157 Relasi)

Relasi Foreign Key penting antar-domain yang mengikat siklus hidup data:

- **Identity ke Profil:** `user_profiles.user_id -> users.id`, `asn_profiles.user_id -> users.id`, `provider_profiles.user_id -> users.id`, `academic_profiles.user_id -> users.id`, `public_profiles.user_id -> users.id`.
- **LMS Hierarchy:** `courses.program_id -> programs.id`, `learning_classes.course_id -> courses.id`, `learning_modules.class_id -> learning_classes.id`, `lessons.module_id -> learning_modules.id`, `quizzes.class_id -> learning_classes.id`, `quiz_questions.quiz_id -> quizzes.id`, `quiz_options.question_id -> quiz_questions.id`.
- **Evaluasi Belajar:** `enrollments.user_id -> users.id`, `enrollments.class_id -> learning_classes.id`, `quiz_attempts.enrollment_id -> enrollments.id`, `quiz_attempts.quiz_id -> quizzes.id`, `assignment_submissions.assignment_id -> assignments.id`, `assignment_submissions.enrollment_id -> enrollments.id`.
- **Event & Kehadiran:** `event_registrations.event_id -> events.id`, `event_registrations.user_id -> users.id`, `attendances.event_id -> events.id`, `attendances.user_id -> users.id`.
- **Sertifikasi:** `certificates.user_id -> users.id`, `certificates.template_id -> certificate_templates.id`, `certificates.source_id -> enrollments.id / event_registrations.id`.
- **RUP Audit Engine:** `rup_audit_packages.batch_id -> rup_audit_batches.id`, `rup_audit_clusters.batch_id -> rup_audit_batches.id`, `rup_audit_violations.batch_id -> rup_audit_batches.id`, `rup_audit_opd_summaries.batch_id -> rup_audit_batches.id`.
- **File Asset Centralization:** `file_assets.uploaded_by -> users.id`. Seluruh lampiran dan cover (`courses.cover_file_id`, `events.cover_file_id`, `news.cover_file_id`, `regulations.cover_file_id`, `knowledge_articles.cover_file_id`, `assignment_submission_files.file_asset_id`) merujuk ke `file_assets.id`.

---

## 5. Inventarisasi 17 Berkas Migrasi Resmi (Zero Migration Drift)

Urutan eksekusi migrasi resmi CodeIgniter 4 (`app/Database/Migrations/`) yang terjaga tanpa mutasi drift:

1. `2026-10-05-000001_AddProcurementStagesAndPivotTables.php`
2. `2026-10-09-000001_AddCertificateSequencesTable.php`
3. `2026-10-10-000001_AddAttendanceCodeToEvents.php`
4. `2026-10-11-000001_AddLearningEnhancementFields.php`
5. `2026-10-12-000001_AddKnowledgeEnhancementFields.php`
6. `2026-10-12-000002_AddRegulationEnhancementFields.php`
7. `2026-10-14-000001_AddNewsEnhancementFields.php`
8. `2026-10-14-000002_CreateVisitorStatisticsTable.php`
9. `2026-10-18-000001_CreateSatisfactionSurveysTable.php`
10. `2026-10-19-000001_AddDurationMinutesToQuizzes.php`
11. `2026-10-19-000002_CreateLandingCmsTables.php`
12. `2026-10-19-000003_AddClassCoverAndSpotlightFields.php`
13. `2026-10-19-000004_AddKnowledgeMediaSlidesAndSpotlightFields.php`
14. `2026-10-19-000005_AddRegulationCarouselFields.php`
15. `2026-10-19-000006_AddNewsAndEventCarouselFields.php`
16. `2026-10-19-000007_AddLearningCarouselFields.php`
17. `2026-10-29-000001_CreateRupAuditTables.php`

---

## 6. Strategi Indexing & Optimasi Query Kritis

1. **Lookup Kinerja Tinggi:** Kolom `slug`, `email`, `username`, `registration_number`, dan `certificate_number` dilengkapi indeks UNIQUE untuk pencarian (1)$.
2. **Filter Visibilitas Terpadu:** Index komposit `(deleted_at, visibility, status)` pada tabel `events` dan `courses` untuk akselerasi eksekusi `PublicVisibilityGuard`.
3. **Workflow Guard Index:** Index komposit `(deleted_at, visibility, workflow_status)` pada `news`, `regulations`, dan `knowledge_articles`.
4. **Foreign Key Index:** Seluruh kolom Foreign Key wajib memiliki index eksplisit untuk menghindari full table lock saat cascade/update.
5. **Partisi & Retention Log:** `audit_logs`, `visitor_ip_logs`, dan `notifications` dirancang untuk pembersihan berkala melalui command `php spark audit:prune`.
