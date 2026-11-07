# BegawiPengadaan — Directory Tree Map & Structural Inventory

> **Metadata Dokumen:**
> - **Versi:** 4.2 (Production Ready)
> - **Tanggal Sinkronisasi:** 7 November 2026
> - **Sumber Kebenaran:** MySQL Live `db_begawipbj` (95 Tabel, 1.127 Kolom, 22 CHECK Constraints, 17 Migrasi) & Codebase Aktual

---

## 1. Peta Pohon Direktori Aktual Produksi (V4.2)

Berikut adalah struktur pohon fisik proyek yang bersih dari file sementara dan file dead-code:

```text
begawipengadaan/
├── app/
│   ├── Commands/                  # 20+ Perintah Spark CLI (Watchdog, Seeder, Test Suites)
│   ├── Common.php                 # Helper global aplikasi
│   ├── Config/                    # Konfigurasi sistem (Routes, Database, Filters, Security)
│   ├── Controllers/               # Controller Web & API terorganisir per domain
│   │   ├── Admin/                 # Back-Office Admin (Akses, Regulasi, Intelligence, CMS)
│   │   ├── Certificate/           # Sertifikat, Batch, Template, Download, Verifikasi
│   │   ├── Communication/         # Berita, Event PBJ
│   │   ├── Dashboard/Auth/        # Auth terpadu WowDash (Login, Register 9 Jalur, Reset)
│   │   ├── Landing/               # Portal Publik FiStudy (Home, Arena, Bunga, Regulasi, Event)
│   │   ├── Learning/              # Manajemen LMS (Kelas, Modul, Kuis, Assessment, Eligibility)
│   │   └── Workspace/             # Workspace FiStudy (Participant, Trainer, Operator, Admin)
│   ├── Database/
│   │   ├── Migrations/            # 17 Berkas Migrasi Database Resmi (Zero Migration Drift)
│   │   └── Seeds/                 # Seeder Master Data & Rekomendasi PBJ
│   ├── Filters/                   # AuthenticationFilter, WorkspaceAuthFilter, Throttle
│   ├── Helpers/                   # Custom application helpers
│   ├── Models/                    # 78 Class Model Data (100% lolos audit:schema-drift)
│   ├── Services/                  # 49 Domain Services:
│   │   ├── Admin/                 # IntelligenceAnalytics, Organization, ReportAnalytics, User
│   │   ├── Communication/         # CommunicationService (Waitlist Reconcile) & EventRegistrationStatus
│   │   ├── Email/                 # EmailService, EmailQueueService, EmailTemplateService
│   │   ├── Identity/              # AuthenticationService, AuthorizationService, RegistrationService
│   │   ├── Learning/              # QuizQuestionImportService
│   │   ├── Regulation/            # RegulationService & RegulationPublicService
│   │   ├── RupAudit/              # 10 Komponen Automated RUP Audit Engine
│   │   │   ├── RupAccessHelper.php
│   │   │   ├── RupAuditService.php
│   │   │   ├── RupCsvParser.php
│   │   │   ├── RupDiffTracker.php
│   │   │   ├── RupExportService.php
│   │   │   ├── RupGraphClusterer.php
│   │   │   ├── RupNlpExtractor.php
│   │   │   ├── RupRciCalculator.php
│   │   │   ├── RupRuleEngine.php
│   │   │   └── RupTaxonomy.php
│   │   ├── Workflow/              # ApprovalService
│   │   └── *.php                  # FileStorageService, PublicVisibilityGuard, LearningService, GradingService, dll.
│   └── Views/
│       ├── dashboard/             # Antarmuka Back-Office WowDash (tanpa kebocoran assets/main)
│       ├── landing/               # Antarmuka Landing Page FiStudy (tanpa kebocoran assets/dashboard)
│       └── main/workspace/        # Antarmuka Workspace FiStudy (tanpa kebocoran assets/dashboard)
├── database/
│   ├── reference/wilayah.sql      # Master wilayah administratif
│   ├── permissions_catalog.json   # 90 permissions catalog
│   └── permissions_sync.sql       # SQL permissions seed
├── dokumen/                       # 18 Dokumen teknis spesifikasi arsitektur resmi
├── public/
│   ├── assets/dashboard/          # Pustaka & CSS WowDash
│   ├── assets/main/               # Pustaka & CSS FiStudy
│   ├── uploads/                   # Media terproteksi .htaccess (php_flag engine off)
│   └── index.php                  # Web Front Controller
├── writable/                      # Cache, session, logs, private storage
├── schema-fresh-v4-3.sql          # Skema dasar DDL 95 tabel
├── composer.json                  # Composer dependencies
└── spark                          # Spark CLI Runner
```
