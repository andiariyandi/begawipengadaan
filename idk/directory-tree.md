# BegawiPBJ — Directory & File Tree

## Struktur Target Proyek

Dokumen ini menetapkan struktur direktori dan file target untuk proyek **BegawiPBJ** berbasis CodeIgniter 4 AppStarter. Struktur ini menerjemahkan kontrak arsitektur, boundary antarmuka, model akses, domain, privacy, keamanan, storage, dan arah implementasi yang ditetapkan pada `README.md`.

Struktur proyek dibangun untuk mendukung:

- Landing Page dan Public Area berbasis FiStudy.
- Workspace berbasis FiStudy bagi Participant, Trainer, Evaluator, dan Operator.
- Auth Layout terpadu berbasis WowDash bagi seluruh pengguna.
- Dashboard Back-office berbasis WowDash bagi Admin dan Super Admin.
- Arena Belajar, Bunga Rampai, Regulasi PBJ, Berita/Agenda/Event, Sertifikat Digital, Government Intelligence PBJ, Statistik Publik PBJ, serta layanan shared.

Keberadaan folder atau file dalam tree bukan indikator bahwa modul telah selesai. Implementasi harus memakai status `Planned`, `UI Implemented`, `Backend Ready`, atau `Production Ready` pada `docs/project/implementation-status.md`.

---

## Kontrak Antarmuka

```text
FiStudy
├── Landing Page dan Public Area
│   └── app/Views/landing/
├── Workspace Participant / Trainer / Evaluator / Operator
│   └── app/Views/main/workspace/
└── Asset produksi
    └── public/assets/main/

WowDash
├── Auth terpadu untuk seluruh pengguna
│   └── app/Views/dashboard/auth/
├── Dashboard Back-office untuk Admin / Super Admin
│   └── app/Views/dashboard/
└── Asset produksi
    └── public/assets/dashboard/

Reference template non-production
└── app/Views/reference/
```

## Kontrak Root Controller

```text
app/Controllers/Landing/
→ Route publik dan presentasi read-only data published/public.

app/Controllers/Dashboard/Auth/
→ Satu-satunya controller autentikasi produksi bagi seluruh pengguna.

app/Controllers/Dashboard/
→ Back-office WowDash untuk role dengan dashboard permission yang sah.

app/Controllers/Workspace/
→ Aktivitas Workspace FiStudy berdasarkan role, ownership, enrollment,
  assignment, profile, bookmark, dan notification.

app/Controllers/Learning/, Knowledge/, Regulation/, Communication/,
Certificate/, Intelligence/, Shared/
→ Controller domain dan layanan umum; setiap route wajib memakai filter,
  permission, ownership, assignment, serta workflow yang sesuai.
```

---

## Tree Lengkap

```text
begawipbj/
├── app/
│   ├── Config/
│   │   ├── App.php
│   │   ├── Autoload.php
│   │   ├── Cache.php
│   │   ├── Certificate.php                          # Nomor, QR, format, lifecycle sertifikat
│   │   ├── ContentSecurityPolicy.php
│   │   ├── Cookie.php
│   │   ├── Database.php
│   │   ├── DocTypes.php
│   │   ├── Email.php
│   │   ├── Encryption.php
│   │   ├── Exceptions.php
│   │   ├── Feature.php                              # Feature toggle bila dibutuhkan
│   │   ├── FileStorage.php                          # Disk, visibility, retention, upload policy
│   │   ├── Filters.php
│   │   ├── ForeignCharacters.php
│   │   ├── Generators.php
│   │   ├── Honeypot.php
│   │   ├── Images.php
│   │   ├── Kint.php
│   │   ├── KnowledgeMedia.php                       # Media, provider eksternal, carousel policy
│   │   ├── Logger.php
│   │   ├── Migrations.php
│   │   ├── Mimes.php
│   │   ├── Modules.php
│   │   ├── Notification.php                         # Channel dan policy notifikasi
│   │   ├── Optimize.php
│   │   ├── Pager.php
│   │   ├── Paths.php
│   │   ├── Publisher.php
│   │   ├── Registration.php                         # Tipe profil dan policy registrasi
│   │   ├── Routes.php
│   │   ├── Routing.php
│   │   ├── Security.php
│   │   ├── Services.php
│   │   ├── Session.php
│   │   ├── Toolbar.php
│   │   ├── UserAgents.php
│   │   ├── Validation.php
│   │   ├── View.php
│   │   └── Workflow.php                             # State/transisi workflow lintas domain
│   │
│   ├── Controllers/
│   │   ├── BaseController.php
│   │   │
│   │   ├── Landing/                                 # FiStudy: Landing dan Public Area
│   │   │   ├── HomeController.php
│   │   │   ├── ArenaBelajarController.php
│   │   │   ├── BungaRampaiController.php
│   │   │   ├── RegulationPublicController.php
│   │   │   ├── NewsPublicController.php
│   │   │   ├── EventPublicController.php
│   │   │   ├── StatisticPublicController.php
│   │   │   ├── CertificateVerificationController.php
│   │   │   └── PublicPageController.php
│   │   │
│   │   ├── Dashboard/                               # WowDash: Auth dan Back-office
│   │   │   ├── Auth/                                # Satu-satunya auth production controller
│   │   │   │   ├── LoginController.php
│   │   │   │   ├── LogoutController.php
│   │   │   │   ├── RegisterController.php
│   │   │   │   ├── RegisterProfileController.php
│   │   │   │   ├── EmailVerificationController.php
│   │   │   │   ├── PasswordController.php
│   │   │   │   └── AccountStatusController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── UserController.php
│   │   │   ├── UserProfileVerificationController.php
│   │   │   ├── RoleController.php
│   │   │   ├── PermissionController.php
│   │   │   ├── RoleRequestReviewController.php
│   │   │   ├── ProcurementRoleController.php
│   │   │   ├── ApprovalController.php
│   │   │   ├── AuditController.php
│   │   │   ├── OrganizationController.php
│   │   │   ├── SettingController.php
│   │   │   ├── InternalReportController.php
│   │   │   └── SystemHealthController.php
│   │   │
│   │   ├── Workspace/                               # FiStudy: authenticated non-back-office
│   │   │   ├── WorkspaceHomeController.php
│   │   │   ├── ParticipantController.php
│   │   │   ├── TrainerController.php
│   │   │   ├── EvaluatorController.php
│   │   │   ├── OperatorController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ProfileChangeRequestController.php
│   │   │   ├── RoleRequestController.php
│   │   │   ├── BookmarkController.php
│   │   │   └── NotificationController.php
│   │   │
│   │   ├── Learning/
│   │   │   ├── ProgramController.php
│   │   │   ├── CourseController.php
│   │   │   ├── LearningClassController.php
│   │   │   ├── ModuleController.php
│   │   │   ├── LessonController.php
│   │   │   ├── EnrollmentController.php
│   │   │   ├── PreTestController.php
│   │   │   ├── QuizController.php
│   │   │   ├── AssignmentController.php
│   │   │   ├── GradeController.php
│   │   │   ├── FeedbackController.php
│   │   │   └── LearningProgressController.php
│   │   │
│   │   ├── Knowledge/
│   │   │   ├── KnowledgeArticleController.php
│   │   │   ├── KnowledgeFaqController.php
│   │   │   ├── KnowledgeCaseStudyController.php
│   │   │   ├── KnowledgeTemplateController.php
│   │   │   ├── KnowledgeMediaController.php
│   │   │   ├── KnowledgeCategoryController.php
│   │   │   └── KnowledgeBookmarkController.php
│   │   │
│   │   ├── Regulation/
│   │   │   ├── RegulationController.php
│   │   │   ├── RegulationCategoryController.php
│   │   │   ├── RegulationVersionController.php
│   │   │   ├── RegulationRelationController.php
│   │   │   ├── RegulationDownloadController.php
│   │   │   └── RegulationBookmarkController.php
│   │   │
│   │   ├── Communication/
│   │   │   ├── NewsController.php
│   │   │   ├── EventController.php
│   │   │   ├── EventRegistrationController.php
│   │   │   └── AttendanceController.php
│   │   │
│   │   ├── Certificate/
│   │   │   ├── CertificateController.php
│   │   │   ├── CertificateApprovalController.php
│   │   │   ├── CertificateTemplateController.php
│   │   │   └── CertificateDownloadController.php
│   │   │
│   │   ├── Intelligence/
│   │   │   ├── OpdController.php
│   │   │   ├── JfPpbjController.php
│   │   │   ├── CompetencyCertificateController.php
│   │   │   └── IntelligenceDashboardController.php
│   │   │
│   │   └── Shared/
│   │       ├── FileController.php
│   │       ├── SearchController.php
│   │       ├── ExportController.php
│   │       └── HealthController.php
│   │
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2026-09-29-000001_CreateIdentityAndRbacTables.php
│   │   │   ├── 2026-09-29-000002_CreateOrganizationAndProfileVerificationTables.php
│   │   │   ├── 2026-09-29-000003_CreateSharedWorkflowAuditFileTables.php
│   │   │   ├── 2026-09-29-000004_CreateLearningCoreTables.php
│   │   │   ├── 2026-09-29-000005_CreateLearningAssessmentTables.php
│   │   │   ├── 2026-09-29-000006_CreateKnowledgeAndMediaTables.php
│   │   │   ├── 2026-09-29-000007_CreateRegulationAndRelationTables.php
│   │   │   ├── 2026-09-29-000008_CreateCommunicationTables.php
│   │   │   ├── 2026-09-29-000009_CreateCertificateTables.php
│   │   │   ├── 2026-09-29-000010_CreateGovernmentIntelligenceTables.php
│   │   │   └── 2026-09-29-000011_AddPerformanceIndexesAndConstraints.php
│   │   └── Seeds/
│   │       ├── DatabaseSeeder.php
│   │       ├── Reference/
│   │       │   ├── RoleSeeder.php
│   │       │   ├── PermissionSeeder.php
│   │       │   ├── RolePermissionSeeder.php
│   │       │   ├── OrganizationSeeder.php
│   │       │   ├── ProcurementRoleSeeder.php
│   │       │   ├── KnowledgeCategorySeeder.php
│   │       │   ├── RegulationCategorySeeder.php
│   │       │   ├── ProcurementStageSeeder.php
│   │       │   ├── CompetencySeeder.php
│   │       │   ├── JfPpbjLevelSeeder.php
│   │       │   └── CompetencyCertificateTypeSeeder.php
│   │       ├── System/
│   │       │   ├── InitialSuperAdminSeeder.php
│   │       │   └── SystemSettingSeeder.php
│   │       └── Development/
│   │           ├── DevelopmentUserSeeder.php
│   │           ├── LearningDemoSeeder.php
│   │           ├── KnowledgeDemoSeeder.php
│   │           ├── RegulationDemoSeeder.php
│   │           ├── CommunicationDemoSeeder.php
│   │           ├── CertificateDemoSeeder.php
│   │           └── IntelligenceDemoSeeder.php
│   │
│   ├── Entities/
│   │   ├── Identity/
│   │   │   ├── User.php
│   │   │   ├── UserProfile.php
│   │   │   ├── Role.php
│   │   │   ├── Permission.php
│   │   │   ├── RoleRequest.php
│   │   │   └── ProfileChangeRequest.php
│   │   ├── Learning/
│   │   │   ├── Program.php
│   │   │   ├── Course.php
│   │   │   ├── LearningClass.php
│   │   │   ├── Enrollment.php
│   │   │   ├── Lesson.php
│   │   │   ├── QuizAttempt.php
│   │   │   ├── AssignmentSubmission.php
│   │   │   └── LearningProgress.php
│   │   ├── Knowledge/
│   │   │   ├── KnowledgeArticle.php
│   │   │   ├── KnowledgeMedia.php
│   │   │   └── KnowledgeBookmark.php
│   │   ├── Regulation/
│   │   │   ├── Regulation.php
│   │   │   ├── RegulationVersion.php
│   │   │   └── RegulationRelation.php
│   │   ├── Communication/
│   │   │   ├── News.php
│   │   │   ├── Event.php
│   │   │   ├── EventRegistration.php
│   │   │   └── Attendance.php
│   │   ├── Certificate/
│   │   │   └── Certificate.php
│   │   ├── Intelligence/
│   │   │   ├── OrganizationUnit.php
│   │   │   ├── JfPpbj.php
│   │   │   └── CompetencyCertificate.php
│   │   └── Shared/
│   │       ├── Approval.php
│   │       ├── AuditLog.php
│   │       ├── Bookmark.php
│   │       ├── FileAsset.php
│   │       └── Notification.php
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   ├── AccountStatusFilter.php
│   │   ├── GuestFilter.php
│   │   ├── PermissionFilter.php
│   │   ├── RoleFilter.php
│   │   ├── WorkspaceAccessFilter.php
│   │   ├── DashboardAccessFilter.php
│   │   ├── VerifiedEmailFilter.php
│   │   ├── RateLimitFilter.php
│   │   └── AuditContextFilter.php
│   │
│   ├── Helpers/
│   │   ├── asset_helper.php
│   │   ├── authorization_helper.php
│   │   ├── date_helper.php
│   │   ├── file_helper.php
│   │   ├── flash_helper.php
│   │   ├── format_helper.php
│   │   ├── route_helper.php
│   │   └── security_helper.php
│   │
│   ├── Language/
│   │   └── id/
│   │       ├── Auth.php
│   │       ├── Dashboard.php
│   │       ├── Error.php
│   │       ├── Learning.php
│   │       ├── Validation.php
│   │       └── Workspace.php
│   │
│   ├── Libraries/
│   │   ├── CertificatePdfGenerator.php
│   │   ├── PasswordPolicy.php
│   │   ├── QrCodeGenerator.php
│   │   └── SafeHtmlSanitizer.php
│   │
│   ├── Models/
│   │   ├── Identity/
│   │   │   ├── UserModel.php
│   │   │   ├── UserProfileModel.php
│   │   │   ├── RoleModel.php
│   │   │   ├── PermissionModel.php
│   │   │   ├── UserRoleModel.php
│   │   │   ├── RolePermissionModel.php
│   │   │   ├── RoleRequestModel.php
│   │   │   ├── ProfileChangeRequestModel.php
│   │   │   ├── PasswordResetModel.php
│   │   │   └── EmailVerificationModel.php
│   │   ├── Learning/
│   │   │   ├── ProgramModel.php
│   │   │   ├── CourseModel.php
│   │   │   ├── LearningClassModel.php
│   │   │   ├── ModuleModel.php
│   │   │   ├── LessonModel.php
│   │   │   ├── EnrollmentModel.php
│   │   │   ├── PreTestModel.php
│   │   │   ├── QuizModel.php
│   │   │   ├── QuizAttemptModel.php
│   │   │   ├── AssignmentModel.php
│   │   │   ├── AssignmentSubmissionModel.php
│   │   │   ├── GradeModel.php
│   │   │   ├── FeedbackModel.php
│   │   │   └── LearningProgressModel.php
│   │   ├── Knowledge/
│   │   │   ├── KnowledgeArticleModel.php
│   │   │   ├── KnowledgeFaqModel.php
│   │   │   ├── KnowledgeCaseStudyModel.php
│   │   │   ├── KnowledgeTemplateModel.php
│   │   │   ├── KnowledgeMediaModel.php
│   │   │   ├── KnowledgeCategoryModel.php
│   │   │   └── KnowledgeBookmarkModel.php
│   │   ├── Regulation/
│   │   │   ├── RegulationModel.php
│   │   │   ├── RegulationCategoryModel.php
│   │   │   ├── RegulationVersionModel.php
│   │   │   ├── RegulationRelationModel.php
│   │   │   ├── RegulationDownloadModel.php
│   │   │   └── RegulationBookmarkModel.php
│   │   ├── Communication/
│   │   │   ├── NewsModel.php
│   │   │   ├── EventModel.php
│   │   │   ├── EventRegistrationModel.php
│   │   │   └── AttendanceModel.php
│   │   ├── Certificate/
│   │   │   ├── CertificateModel.php
│   │   │   └── CertificateTemplateModel.php
│   │   ├── Intelligence/
│   │   │   ├── OrganizationUnitModel.php
│   │   │   ├── JfPpbjModel.php
│   │   │   ├── CompetencyModel.php
│   │   │   ├── CompetencyCertificateModel.php
│   │   │   └── CompetencyGapSnapshotModel.php
│   │   └── Shared/
│   │       ├── ApprovalModel.php
│   │       ├── AuditLogModel.php
│   │       ├── BookmarkModel.php
│   │       ├── FileAssetModel.php
│   │       ├── NotificationModel.php
│   │       └── SystemSettingModel.php
│   │
│   ├── Services/
│   │   ├── Identity/
│   │   │   ├── AuthenticationService.php
│   │   │   ├── RegistrationService.php
│   │   │   ├── AccountStatusService.php
│   │   │   ├── EmailVerificationService.php
│   │   │   ├── PasswordResetService.php
│   │   │   ├── PasswordService.php
│   │   │   ├── ProfileService.php
│   │   │   ├── ProfileChangeRequestService.php
│   │   │   ├── RoleAssignmentService.php
│   │   │   ├── RoleRequestService.php
│   │   │   ├── PermissionService.php
│   │   │   └── AuthorizationService.php
│   │   ├── Landing/
│   │   │   ├── PublicCatalogService.php
│   │   │   ├── PublicSearchService.php
│   │   │   └── PublicStatisticService.php
│   │   ├── Learning/
│   │   │   ├── ProgramService.php
│   │   │   ├── CourseService.php
│   │   │   ├── LearningClassService.php
│   │   │   ├── EnrollmentService.php
│   │   │   ├── LessonService.php
│   │   │   ├── AssessmentService.php
│   │   │   ├── QuizService.php
│   │   │   ├── AssignmentService.php
│   │   │   ├── GradingService.php
│   │   │   ├── LearningProgressService.php
│   │   │   └── LearningStatisticService.php
│   │   ├── Knowledge/
│   │   │   ├── KnowledgeArticleService.php
│   │   │   ├── KnowledgeFaqService.php
│   │   │   ├── KnowledgeMediaService.php
│   │   │   ├── KnowledgePublicationService.php
│   │   │   └── KnowledgeSearchService.php
│   │   ├── Regulation/
│   │   │   ├── RegulationService.php
│   │   │   ├── RegulationVersionService.php
│   │   │   ├── RegulationRelationService.php
│   │   │   ├── RegulationPublicationService.php
│   │   │   └── RegulationDownloadService.php
│   │   ├── Communication/
│   │   │   ├── NewsService.php
│   │   │   ├── EventService.php
│   │   │   ├── EventRegistrationService.php
│   │   │   └── AttendanceService.php
│   │   ├── Certificate/
│   │   │   ├── CertificateApprovalService.php
│   │   │   ├── CertificateEligibilityService.php
│   │   │   ├── CertificateGenerationService.php
│   │   │   ├── CertificateRevocationService.php
│   │   │   └── CertificateVerificationService.php
│   │   ├── Intelligence/
│   │   │   ├── CompetencyCertificateService.php
│   │   │   ├── CompetencyGapService.php
│   │   │   ├── IntelligenceStatisticService.php
│   │   │   ├── JfPpbjService.php
│   │   │   └── OpdService.php
│   │   └── Shared/
│   │       ├── ApiResponseService.php
│   │       ├── ApprovalService.php
│   │       ├── AuditService.php
│   │       ├── DashboardMetricService.php
│   │       ├── ExportService.php
│   │       ├── FileAccessService.php
│   │       ├── FileDownloadService.php
│   │       ├── FilePreviewService.php
│   │       ├── FileStorageService.php
│   │       ├── NotificationService.php
│   │       ├── SearchService.php
│   │       └── WorkspaceNavigationService.php
│   │
│   ├── Validation/
│   │   ├── CertificateRules.php
│   │   ├── CommunicationRules.php
│   │   ├── IdentityRules.php
│   │   ├── IntelligenceRules.php
│   │   ├── KnowledgeRules.php
│   │   ├── LearningRules.php
│   │   └── RegulationRules.php
│   │
│   └── Views/
│       ├── landing/                              # FiStudy: Landing dan Public Area
│       │   ├── layouts/
│       │   │   ├── minimal.php
│       │   │   └── public.php
│       │   ├── components/
│       │   │   ├── breadcrumb.php
│       │   │   ├── carousel_controls.php
│       │   │   ├── certificate_verification_form.php
│       │   │   ├── empty_state.php
│       │   │   ├── flash_message.php
│       │   │   ├── footer.php
│       │   │   ├── head.php
│       │   │   ├── header.php
│       │   │   ├── media_card.php
│       │   │   ├── pagination.php
│       │   │   └── public_search.php
│       │   ├── home/
│       │   │   └── index.php
│       │   ├── learning/
│       │   │   ├── course_detail.php
│       │   │   ├── course_list.php
│       │   │   ├── instructor_detail.php
│       │   │   ├── instructor_list.php
│       │   │   └── program_list.php
│       │   ├── knowledge/
│       │   │   ├── article_detail.php
│       │   │   ├── article_list.php
│       │   │   ├── case_study_list.php
│       │   │   ├── faq.php
│       │   │   ├── index.php
│       │   │   ├── media_gallery.php
│       │   │   ├── search_result.php
│       │   │   └── template_list.php
│       │   ├── regulations/
│       │   │   ├── detail.php
│       │   │   ├── index.php
│       │   │   ├── relation_list.php
│       │   │   └── version_list.php
│       │   ├── news/
│       │   │   ├── detail.php
│       │   │   └── index.php
│       │   ├── events/
│       │   │   ├── detail.php
│       │   │   └── index.php
│       │   ├── statistics/
│       │   │   └── index.php
│       │   ├── certificates/
│       │   │   └── verify.php
│       │   └── pages/
│       │       ├── about.php
│       │       ├── accessibility.php
│       │       ├── contact.php
│       │       ├── privacy.php
│       │       └── terms.php
│       │
│       ├── main/                                 # FiStudy: pengguna terautentikasi non-back-office
│       │   ├── layouts/
│       │   │   ├── minimal.php
│       │   │   └── workspace.php
│       │   ├── components/
│       │   │   ├── assessment_status.php
│       │   │   ├── bookmark_button.php
│       │   │   ├── breadcrumb.php
│       │   │   ├── confirmation_modal.php
│       │   │   ├── empty_state.php
│       │   │   ├── file_preview.php
│       │   │   ├── flash_message.php
│       │   │   ├── head.php
│       │   │   ├── notification_menu.php
│       │   │   ├── pagination.php
│       │   │   ├── pdf_viewer.php
│       │   │   ├── workspace_footer.php
│       │   │   ├── workspace_header.php
│       │   │   └── workspace_sidebar.php
│       │   ├── workspace/
│       │   │   ├── home/
│       │   │   │   └── index.php
│       │   │   ├── participant/
│       │   │   │   ├── assignment.php
│       │   │   │   ├── assignment_submission.php
│       │   │   │   ├── certificates.php
│       │   │   │   ├── dashboard.php             # Participant Workspace Dashboard
│       │   │   │   ├── enrollments.php
│       │   │   │   ├── feedback.php
│       │   │   │   ├── learning_detail.php
│       │   │   │   ├── pre_test.php
│       │   │   │   ├── pre_test_result.php
│       │   │   │   ├── progress.php
│       │   │   │   ├── quiz.php
│       │   │   │   ├── quiz_result.php
│       │   │   │   └── study.php
│       │   │   ├── trainer/
│       │   │   │   ├── assignment_form.php
│       │   │   │   ├── class_detail.php
│       │   │   │   ├── class_list.php
│       │   │   │   ├── dashboard.php             # Trainer Workspace Dashboard
│       │   │   │   ├── lesson_form.php
│       │   │   │   ├── participant_progress.php
│       │   │   │   └── quiz_form.php
│       │   │   ├── evaluator/
│       │   │   │   ├── assessment_history.php
│       │   │   │   ├── assignment_grade.php
│       │   │   │   ├── assignment_queue.php
│       │   │   │   └── dashboard.php             # Evaluator Workspace Dashboard
│       │   │   ├── operator/
│       │   │   │   ├── attendance_management.php
│       │   │   │   ├── dashboard.php             # Operator Workspace Dashboard
│       │   │   │   ├── delegated_approvals.php
│       │   │   │   ├── enrollment_management.php
│       │   │   │   └── verification_queue.php
│       │   │   ├── bookmarks/
│       │   │   │   └── index.php
│       │   │   ├── notifications/
│       │   │   │   └── index.php
│       │   │   └── profile/
│       │   │       ├── academic_profile.php
│       │   │       ├── account.php
│       │   │       ├── asn_profile.php
│       │   │       ├── change_requests.php
│       │   │       ├── index.php
│       │   │       ├── procurement_roles.php
│       │   │       ├── provider_profile.php
│       │   │       ├── public_profile.php
│       │   │       └── role_requests.php
│       │   └── partials/
│       │       ├── certificate_card.php
│       │       ├── event_card.php
│       │       └── learning_card.php
│       │
│       ├── dashboard/                            # WowDash: Auth dan Back-office
│       │   ├── layouts/
│       │   │   ├── auth.php                      # Satu-satunya layout auth produksi
│       │   │   ├── dashboard.php
│       │   │   ├── minimal.php
│       │   │   └── print.php
│       │   ├── components/
│       │   │   ├── approval_timeline.php
│       │   │   ├── audit_detail.php
│       │   │   ├── breadcrumb.php
│       │   │   ├── chart_container.php
│       │   │   ├── confirmation_modal.php
│       │   │   ├── data_table.php
│       │   │   ├── empty_state.php
│       │   │   ├── filter_form.php
│       │   │   ├── flash_message.php
│       │   │   ├── footer.php
│       │   │   ├── head.php
│       │   │   ├── pagination.php
│       │   │   ├── sidebar.php
│       │   │   ├── status_badge.php
│       │   │   └── topbar.php
│       │   ├── auth/                             # Auth WowDash untuk seluruh pengguna
│       │   │   ├── account_pending.php
│       │   │   ├── account_rejected.php
│       │   │   ├── account_revision_required.php
│       │   │   ├── forgot_password.php
│       │   │   ├── login.php
│       │   │   ├── register_account.php
│       │   │   ├── register_academic.php
│       │   │   ├── register_asn.php
│       │   │   ├── register_provider.php
│       │   │   ├── register_public.php
│       │   │   ├── register_region.php
│       │   │   ├── register_review.php
│       │   │   ├── register_success.php
│       │   │   ├── reset_password.php
│       │   │   ├── reset_password_success.php
│       │   │   ├── verify_email.php
│       │   │   └── verify_email_success.php
│       │   ├── home/
│       │   │   └── index.php                    # Dashboard Back-office Home
│       │   ├── users/
│       │   │   ├── create.php
│       │   │   ├── detail.php
│       │   │   ├── edit.php
│       │   │   ├── index.php
│       │   │   ├── profile_verification_queue.php
│       │   │   └── role_request_queue.php
│       │   ├── rbac/
│       │   │   ├── permission_form.php
│       │   │   ├── permissions.php
│       │   │   ├── role_form.php
│       │   │   ├── role_permission_matrix.php
│       │   │   └── roles.php
│       │   ├── approvals/
│       │   │   ├── detail.php
│       │   │   └── index.php
│       │   ├── audits/
│       │   │   ├── detail.php
│       │   │   └── index.php
│       │   ├── organizations/
│       │   │   ├── form.php
│       │   │   └── index.php
│       │   ├── learning/
│       │   │   ├── classes.php
│       │   │   ├── class_form.php
│       │   │   ├── courses.php
│       │   │   ├── course_form.php
│       │   │   ├── enrollment_management.php
│       │   │   ├── grades.php
│       │   │   ├── learning_reports.php
│       │   │   ├── lessons.php
│       │   │   ├── modules.php
│       │   │   ├── programs.php
│       │   │   └── program_form.php
│       │   ├── knowledge/
│       │   │   ├── article_form.php
│       │   │   ├── articles.php
│       │   │   ├── categories.php
│       │   │   ├── faq.php
│       │   │   ├── media.php
│       │   │   └── publication_queue.php
│       │   ├── regulations/
│       │   │   ├── form.php
│       │   │   ├── index.php
│       │   │   ├── publication_queue.php
│       │   │   ├── relations.php
│       │   │   └── versions.php
│       │   ├── communication/
│       │   │   ├── attendance.php
│       │   │   ├── event_form.php
│       │   │   ├── events.php
│       │   │   ├── news.php
│       │   │   ├── news_form.php
│       │   │   └── registrations.php
│       │   ├── certificates/
│       │   │   ├── approval_queue.php
│       │   │   ├── detail.php
│       │   │   ├── index.php
│       │   │   ├── revoked.php
│       │   │   ├── template_form.php
│       │   │   └── templates.php
│       │   ├── intelligence/
│       │   │   ├── competency_certificates.php
│       │   │   ├── competency_gaps.php
│       │   │   ├── exports.php
│       │   │   ├── index.php
│       │   │   ├── jf_ppbj.php
│       │   │   └── organizations.php
│       │   ├── reports/
│       │   │   ├── index.php
│       │   │   └── internal_statistics.php
│       │   └── settings/
│       │       ├── application.php
│       │       ├── index.php
│       │       ├── notification.php
│       │       ├── security.php
│       │       └── storage.php
│       │
│       ├── error/                                # Error pages; minimal/custom
│       │   ├── 400.php
│       │   ├── 403.php
│       │   ├── 404.php
│       │   ├── 419.php
│       │   ├── 429.php
│       │   ├── 500.php
│       │   ├── exception.php
│       │   └── maintenance.php
│       │
│       └── reference/                            # Source template; tidak dirender produksi
│           ├── fistudy/
│           │   ├── assets/
│           │   ├── inc/
│           │   │   ├── PHPMailer/
│           │   │   └── sendemail.php
│           │   ├── js/
│           │   ├── 404.html
│           │   ├── about.html
│           │   ├── contact.html
│           │   ├── course.html
│           │   ├── course-details.html
│           │   ├── events.html
│           │   ├── event-details.html
│           │   ├── index.html
│           │   ├── instructor.html
│           │   ├── instructor-details.html
│           │   ├── login.html                   # Reference only; bukan auth produksi
│           │   └── sign-up.html                 # Reference only; bukan auth produksi
│           └── wowdash/
│               ├── assets/
│               ├── forgot-password.html         # Reference only
│               ├── form.html
│               ├── index.html
│               ├── pagination.html
│               ├── progress.html
│               ├── sign-in.html                 # Reference only
│               └── sign-up.html                 # Reference only
│
├── public/
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   ├── robots.txt
│   ├── assets/
│   │   ├── main/                                 # FiStudy: Landing/Public/Workspace
│   │   │   ├── css/
│   │   │   ├── fonts/
│   │   │   ├── icons/
│   │   │   ├── images/
│   │   │   ├── js/
│   │   │   ├── vendor/
│   │   │   └── uploads-public/                  # Hanya konten eksplisit berstatus public
│   │   └── dashboard/                            # WowDash: Auth/Dashboard
│   │       ├── css/
│   │       ├── fonts/
│   │       ├── icons/
│   │       ├── images/
│   │       ├── js/
│   │       └── vendor/
│   └── build/                                    # Opsional: output minified/bundled asset
│
├── writable/
│   ├── cache/
│   ├── debugbar/
│   ├── exports/                                  # Export sementara; retention policy wajib
│   ├── logs/
│   ├── session/
│   └── uploads/                                  # Private runtime storage; tidak public URL
│       ├── certificates/
│       ├── knowledge/
│       ├── learning/
│       ├── profiles/
│       ├── regulations/
│       ├── submissions/
│       └── temporary/
│
├── tests/
│   ├── _support/
│   │   ├── Database/
│   │   └── Fixtures/
│   ├── unit/
│   │   ├── Certificate/
│   │   ├── Identity/
│   │   ├── Intelligence/
│   │   ├── Knowledge/
│   │   ├── Learning/
│   │   └── Regulation/
│   ├── feature/
│   │   ├── Auth/
│   │   ├── Dashboard/
│   │   ├── Files/
│   │   ├── Landing/
│   │   └── Workspace/
│   └── database/
│       ├── ConstraintsTest.php
│       ├── MigrationsTest.php
│       └── SeedersTest.php
│
├── database/
│   └── reference/
│       └── wilayah.sql                           # Master resmi wilayah; bukan seed dummy/development
│
├── docs/
│   ├── architecture/
│   │   ├── auth-and-rbac.md
│   │   ├── file-storage-policy.md
│   │   ├── route-contract.md
│   │   ├── ui-boundary.md
│   │   └── workflow-contract.md
│   ├── database/
│   │   ├── data-dictionary.md
│   │   ├── database-blueprint.md
│   │   ├── database-erd.md
│   │   └── schema-fresh-v4.sql
│   ├── domain/
│   │   ├── assessment-and-certification-rules.md
│   │   ├── privacy-and-data-classification.md
│   │   ├── public-statistics-policy.md
│   │   ├── registration-policy.md
│   │   └── regulation-policy.md
│   └── project/
│       ├── directory-tree.md
│       ├── documentation-and-database-file-register.md
│       └── implementation-status.md
│
├── .env.example
├── .gitignore
├── LICENSE
├── README.md
├── composer.json
├── composer.lock
├── phpunit.xml.dist
└── spark
```

---

## Kontrak Route Auth

Seluruh route autentikasi menggunakan controller `App\Controllers\Dashboard\Auth` dan view `app/Views/dashboard/auth/`.

```text
GET|POST  /dashboard/masuk
→ Dashboard\Auth\LoginController

POST      /dashboard/keluar
→ Dashboard\Auth\LogoutController

GET|POST  /dashboard/daftar
→ Dashboard\Auth\RegisterController

GET|POST  /dashboard/daftar/wilayah
→ Dashboard\Auth\RegisterController

GET|POST  /dashboard/daftar/profil/{tipe}
→ Dashboard\Auth\RegisterProfileController

GET|POST  /dashboard/daftar/tinjau
→ Dashboard\Auth\RegisterController

GET       /dashboard/daftar/berhasil
→ Dashboard\Auth\RegisterController

GET|POST  /dashboard/verifikasi/{token}
→ Dashboard\Auth\EmailVerificationController

GET|POST  /dashboard/lupa-kata-sandi
→ Dashboard\Auth\PasswordController

GET|POST  /dashboard/reset-kata-sandi/{token}
→ Dashboard\Auth\PasswordController

GET       /dashboard/status-akun
→ Dashboard\Auth\AccountStatusController
```

Auth UI tidak memberikan privilege Dashboard secara otomatis. Redirect pascalogin menggunakan account status, role, permission, ownership/assignment, serta route awal yang diminta.

---

## Aturan Asset, Reference, dan Storage

```text
app/Views/reference/
→ Source template FiStudy/WowDash
→ Non-production
→ Tidak boleh dirender controller/route produksi

public/assets/main/
→ Hanya asset FiStudy yang dipakai Landing/Public/Workspace produksi

public/assets/dashboard/
→ Hanya asset WowDash yang dipakai Auth/Dashboard produksi

writable/uploads/
→ File private/internal/restricted
→ Tidak boleh diakses langsung melalui URL publik
→ Preview/download wajib melalui FileController + FileAccessService

public/assets/main/uploads-public/
→ Hanya file yang secara eksplisit berstatus public
```

`app/Views/reference/fistudy/inc/sendemail.php` dan PHPMailer bawaan template tidak digunakan untuk email produksi. Pengiriman email produksi menggunakan layanan CodeIgniter 4 dan dependency Composer yang dikelola proyek.

---

## Dokumentasi Teknis

```text
docs/architecture/ui-boundary.md
→ Boundary UI, template, layout, asset, dan redirect lintas area.

docs/architecture/route-contract.md
→ Route, HTTP method, controller, filter, permission, route name, dan response/view.

docs/architecture/auth-and-rbac.md
→ Auth, session, account status, role, permission, filter, redirect, dan access matrix.

docs/architecture/workflow-contract.md
→ State machine, transisi, actor, approval, alasan, dan audit.

docs/architecture/file-storage-policy.md
→ Disk, visibility, upload, preview/download, retention, serta audit file.

docs/database/database-blueprint.md
→ Tabel, PK/FK, enum, constraint, index, ownership, migration order, dan seed strategy.

docs/database/database-erd.md
→ ERD konseptual dan relasi antar-domain.

docs/database/data-dictionary.md
→ Definisi tabel/kolom, tipe data, nullable, default, klasifikasi data, dan validasi.

docs/domain/registration-policy.md
→ Field, validasi, verifikasi, perubahan profil, dan role request per tipe profil.

docs/domain/assessment-and-certification-rules.md
→ Assessment, nilai, kelulusan, eligibility, dan sertifikat.

docs/domain/regulation-policy.md
→ Versioning, legal status, relation, publication, visibility, preview, dan download regulasi.

docs/domain/privacy-and-data-classification.md
→ Klasifikasi public/internal/restricted, masking, export, retention, dan audit redaction.

docs/domain/public-statistics-policy.md
→ Indikator, aggregation, suppression, threshold, dan disclosure control.

docs/project/implementation-status.md
→ Register Planned/UI Implemented/Backend Ready/Production Ready per area.
```

---

## Path yang Dilarang

Path berikut tidak boleh dibuat, dikembangkan, maupun dipakai sebagai route produksi:

```text
app/Controllers/Auth/
app/Controllers/Admin/
app/Controllers/Admin/AdminAuthController.php
app/Views/auth/
app/Views/workspace/
app/Views/main/layouts/auth.php
public/assets/workspace/
```

Gunakan selalu lokasi target berikut:

```text
app/Controllers/Dashboard/Auth/
app/Controllers/Dashboard/
app/Views/dashboard/auth/
app/Views/dashboard/layouts/auth.php
app/Views/main/workspace/
public/assets/main/
public/assets/dashboard/
```

---

## Status Implementasi

| Status | Arti |
|---|---|
| Planned | Folder/file tersedia sebagai placeholder; belum diisi atau belum berfungsi |
| UI Implemented | Layout/view/component tersedia; dapat masih memakai data dummy atau backend belum lengkap |
| Backend Ready | Route aksi, validation, service, model, persistence, policy, serta authorization utama tersedia |
| Production Ready | Test, audit, security review, observability, dokumentasi operasional, serta acceptance test telah dipenuhi |

Keberadaan folder, controller, model, migration, route GET, atau view HTML tidak boleh dianggap sebagai fitur selesai.
