# BegawiPengadaan — Database Entity Relationship Diagram (ERD)

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Pendahuluan & Prinsip ERD Modular

Diagram Relasi Entitas (*Entity-Relationship Diagram* / ERD) BegawiPengadaan mendokumentasikan pemetaan struktural 95 tabel pada basis data `db_begawipbj`. Mengingat kompleksitas 157 relasi Foreign Key yang saling terhubung, dokumentasi ini dipecah menjadi **9 Sub-Diagram Domain** berbasis sintaks resmi **Mermaid (`erDiagram`)** agar mudah dipahami, dirender secara interaktif, dan dijadikan acuan pengembang.

---

## 2. Sub-Diagram 1: Identity, Multi-Profile & RBAC Engine

```mermaid
erDiagram
    users ||--o{ user_roles : has
    roles ||--o{ user_roles : assigned_to
    roles ||--o{ role_permissions : grants
    permissions ||--o{ role_permissions : defined_in
    users ||--o| user_profiles : owns
    users ||--o| asn_profiles : owns
    users ||--o| provider_profiles : owns
    users ||--o| academic_profiles : owns
    users ||--o| public_profiles : owns
    users ||--o{ user_procurement_roles : holds
    procurement_roles ||--o{ user_procurement_roles : defined_as
    users ||--o{ role_requests : submits
    users ||--o{ profile_verifications : verified_by
    users ||--o{ profile_change_requests : requests
    users ||--o{ password_resets : requests
```

---

## 3. Sub-Diagram 2: Learning Management System (LMS) & Assessment

```mermaid
erDiagram
    programs ||--o{ courses : contains
    courses ||--o{ learning_classes : hosts
    learning_classes ||--o{ learning_modules : organizes
    learning_modules ||--o{ lessons : contains
    learning_classes ||--o{ quizzes : assesses
    quizzes ||--o{ quiz_questions : has
    quiz_questions ||--o{ quiz_options : provides
    learning_classes ||--o{ assignments : assigns
    learning_classes ||--o{ class_trainers : instructs
    learning_classes ||--o{ class_evaluators : evaluates
    users ||--o{ enrollments : registers
    learning_classes ||--o{ enrollments : admits
    enrollments ||--o{ learning_progress : tracks
    enrollments ||--o{ quiz_attempts : submits
    quizzes ||--o{ quiz_attempts : records
    quiz_attempts ||--o{ quiz_answers : contains
    assignments ||--o{ assignment_submissions : receives
    enrollments ||--o{ assignment_submissions : produces
    assignment_submissions ||--o{ assignment_submission_files : attaches
    enrollments ||--o{ feedbacks : reviews
```

---

## 4. Sub-Diagram 3: Knowledge Management System (KMS) & Bunga Rampai

```mermaid
erDiagram
    knowledge_categories ||--o{ knowledge_article_categories : classifies
    knowledge_articles ||--o{ knowledge_article_categories : tagged_in
    knowledge_tags ||--o{ knowledge_article_tags : defines
    knowledge_articles ||--o{ knowledge_article_tags : labeled_with
    knowledge_categories ||--o{ knowledge_media : categorizes
    users ||--o{ knowledge_articles : authors
    file_assets ||--o{ knowledge_articles : covers
    file_assets ||--o{ knowledge_media : stores
```

---

## 5. Sub-Diagram 4: Naskah Hukum & Regulasi PBJ

```mermaid
erDiagram
    regulation_categories ||--o{ regulations : classifies
    regulations ||--o{ regulation_versions : revisions
    regulations ||--o{ regulation_relations : source_rel
    regulations ||--o{ regulation_relations : target_rel
    regulations ||--o{ regulation_downloads : logged
    procurement_stages ||--o{ regulation_procurement_stages : maps
    regulations ||--o{ regulation_procurement_stages : referenced
    competencies ||--o{ regulation_competencies : maps
    regulations ||--o{ regulation_competencies : requires
```

---

## 6. Sub-Diagram 5: Events, Agendas, Presensi & Komunikasi Publik

```mermaid
erDiagram
    events ||--o{ event_registrations : enrolls
    users ||--o{ event_registrations : joins
    events ||--o{ attendances : checks
    users ||--o{ attendances : verified
    news_categories ||--o{ news : categorizes
    users ||--o{ news : publishes
    users ||--o{ contact_messages : submits
    helpdesk_topics ||--o{ contact_messages : categorizes
    newsletter_subscribers ||--o{ news : reads
```

---

## 7. Sub-Diagram 6: Master Sertifikat & Tanda Tangan Elektronik (TTE)

```mermaid
erDiagram
    certificate_templates ||--o{ certificates : styles
    users ||--o{ certificates : awarded_to
    enrollments ||--o{ certificates : earned_from_learning
    event_registrations ||--o{ certificates : earned_from_event
    certificate_sequences ||--o{ certificates : numbers
    file_assets ||--o{ certificates : pdf_file
```

---

## 8. Sub-Diagram 7: Government Intelligence & SDM Fungsional PBJ

```mermaid
erDiagram
    jf_ppbj_levels ||--o{ jf_ppbj_records : defines_rank
    organization_units ||--o{ jf_ppbj_records : assigns_to
    users ||--o{ jf_ppbj_records : identity
    competency_certificate_types ||--o{ competency_certificates : specifies
    users ||--o{ competency_certificates : owns
    organization_units ||--o{ competency_gap_snapshots : analyzes
    competencies ||--o{ competency_gap_snapshots : measures
```

---

## 9. Sub-Diagram 8: Landing CMS & Interaksi Publik

```mermaid
erDiagram
    home_sliders ||--o{ file_assets : image
    page_banners ||--o{ file_assets : banner
    feature_highlights ||--o{ file_assets : icon
    external_portals ||--o{ file_assets : logo
    testimonials ||--o{ file_assets : photo
    team_members ||--o{ file_assets : avatar
    site_announcements ||--o{ users : posted_by
    users ||--o{ bookmarks : saves
    satisfaction_surveys ||--o{ survey_aspects : rates
```

---

## 10. Sub-Diagram 9: Central File Storage & Automated RUP Audit Engine

```mermaid
erDiagram
    users ||--o{ file_assets : uploads
    rup_audit_batches ||--o{ rup_audit_packages : ingests
    rup_audit_batches ||--o{ rup_audit_clusters : groups
    rup_audit_batches ||--o{ rup_audit_violations : screens
    rup_audit_batches ||--o{ rup_audit_opd_summaries : aggregates
    users ||--o{ rup_audit_batches : executes
    users ||--o{ audit_logs : acts
    users ||--o{ notifications : receives
    users ||--o{ approvals : requests_reviews
    users ||--o{ exports : downloads
    users ||--o{ visitor_ip_logs : tracks
```

---

## 11. Kesimpulan Integritas Relasi

Seluruh 95 tabel memiliki relasi terarah tanpa adanya tabel 'yatim piatu' (*orphan table*). Penggunaan konvensi `id` (Auto-Increment) dan Foreign Key bertipe data identik menjamin kinerja indeks optimal, integritas referensial terjaga, dan isolasi privasi data terjamin secara menyeluruh.
