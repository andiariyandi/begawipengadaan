# BegawiPengadaan Directory Tree

## Struktur Direktori dan File Baru Proyek BegawiPengadaan V4

Dokumen ini menetapkan tree direktori dan file target untuk proyek **BegawiPengadaan V4**. Struktur ini berfokus pada file yang akan dibuat, diadaptasi, atau dikelola oleh proyek BegawiPengadaan.

Folder bawaan CodeIgniter 4 AppStarter yang tidak memerlukan penambahan file khusus ditampilkan hanya sampai level folder. File internal bawaan CI4 tidak dirinci. Source template FiStudy dan WowDash ditampilkan sesuai struktur yang telah diberikan, tetapi tetap berstatus non-production.

```text
begawipbj/
├── app/
│   ├── Config/
│   │   ├── Certificate.php                         # Konfigurasi format dan lifecycle sertifikat
│   │   ├── FileStorage.php                         # Disk, visibility, batas upload, storage policy
│   │   ├── KnowledgeMedia.php                      # Tipe media, provider eksternal, carousel policy
│   │   ├── Notification.php                        # Channel dan policy notifikasi
│   │   ├── Registration.php                        # Tipe profil dan aturan registrasi
│   │   └── Workflow.php                            # State/transisi workflow domain
│   │
│   ├── Controllers/
│   │   ├── Landing/
│   │   │   ├── HomeController.php
│   │   │   ├── ArenaBelajarController.php
│   │   │   ├── BungaRampaiController.php
│   │   │   ├── RegulationPublicController.php
│   │   │   ├── NewsPublicController.php
│   │   │   ├── EventPublicController.php
│   │   │   ├── StatisticPublicController.php
│   │   │   └── CertificateVerificationController.php
│   │   │
│   │   ├── Auth/
│   │   │   ├── LoginController.php
│   │   │   ├── RegisterController.php
│   │   │   ├── RegisterProfileController.php
│   │   │   ├── EmailVerificationController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── ProfileChangeRequestController.php
│   │   │   ├── RoleRequestController.php
│   │   │   └── PasswordController.php
│   │   │
│   │   ├── Workspace/
│   │   │   ├── WorkspaceHomeController.php
│   │   │   ├── ParticipantController.php
│   │   │   ├── TrainerController.php
│   │   │   ├── EvaluatorController.php
│   │   │   ├── OperatorController.php
│   │   │   ├── BookmarkController.php
│   │   │   └── NotificationController.php
│   │   │
│   │   ├── Admin/
│   │   │   ├── AdminAuthController.php
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
│   │   │   └── SettingController.php
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
│   │   │   ├── GradingController.php
│   │   │   ├── PostTestController.php
│   │   │   └── StudyController.php
│   │   │
│   │   ├── Knowledge/
│   │   │   ├── ArticleController.php
│   │   │   ├── FaqController.php
│   │   │   ├── CaseStudyController.php
│   │   │   ├── DocumentTemplateController.php
│   │   │   ├── KnowledgeMediaController.php
│   │   │   ├── KnowledgeMediaItemController.php
│   │   │   ├── KnowledgeCarouselController.php
│   │   │   └── BookmarkController.php
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
│   │   │   └── CertificateTemplateController.php
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
│   │       └── ExportController.php
│   │
│   ├── Database/
│   │   ├── Migrations/
│   │   │   ├── 2026-09-28-000001_CreateIdentityAndRbacTables.php
│   │   │   ├── 2026-09-28-000002_CreateOrganizationAndProfileVerificationTables.php
│   │   │   ├── 2026-09-28-000003_CreateSharedWorkflowAuditFileTables.php
│   │   │   ├── 2026-09-28-000004_CreateLearningCoreTables.php
│   │   │   ├── 2026-09-28-000005_CreateLearningAssessmentTables.php
│   │   │   ├── 2026-09-28-000006_CreateKnowledgeAndMediaTables.php
│   │   │   ├── 2026-09-28-000007_CreateRegulationAndRelationTables.php
│   │   │   ├── 2026-09-28-000008_CreateCommunicationTables.php
│   │   │   ├── 2026-09-28-000009_CreateCertificateTables.php
│   │   │   ├── 2026-09-28-000010_CreateGovernmentIntelligenceTables.php
│   │   │   └── 2026-09-28-000011_AddPerformanceIndexesAndConstraints.php
│   │   │
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
│   │       ├── Development/
│   │       │   ├── DevelopmentUserSeeder.php
│   │       │   ├── LearningDemoSeeder.php
│   │       │   ├── KnowledgeDemoSeeder.php
│   │       │   ├── RegulationDemoSeeder.php
│   │       │   ├── CommunicationDemoSeeder.php
│   │       │   └── IntelligenceDemoSeeder.php
│   │       └── Import/
│   │           ├── WilayahImportSeeder.php
│   │           └── OpdImportSeeder.php
│   │
│   ├── Filters/
│   │   ├── AuthFilter.php
│   │   ├── GuestFilter.php
│   │   ├── AdminFilter.php
│   │   ├── WorkspaceFilter.php
│   │   ├── RoleFilter.php
│   │   ├── PermissionFilter.php
│   │   ├── AccountStatusFilter.php
│   │   ├── EmailVerifiedFilter.php
│   │   ├── ProfileVerifiedFilter.php
│   │   ├── CsrfGuardFilter.php
│   │   ├── RateLimitFilter.php
│   │   ├── AuditRequestFilter.php
│   │   └── SecurityHeadersFilter.php
│   │
│   ├── Helpers/
│   │   ├── access_helper.php
│   │   ├── asset_helper.php
│   │   ├── bookmark_helper.php
│   │   ├── certificate_helper.php
│   │   ├── file_helper.php
│   │   ├── format_helper.php
│   │   ├── regulation_helper.php
│   │   ├── statistic_helper.php
│   │   └── workflow_helper.php
│   │
│   ├── Language/
│   │   ├── id/
│   │   │   ├── Auth.php
│   │   │   ├── Certificate.php
│   │   │   ├── File.php
│   │   │   ├── General.php
│   │   │   ├── Knowledge.php
│   │   │   ├── Learning.php
│   │   │   ├── Registration.php
│   │   │   ├── Regulation.php
│   │   │   ├── Validation.php
│   │   │   └── Workflow.php
│   │   └── en/
│   │       ├── General.php
│   │       └── Validation.php
│   │
│   ├── Libraries/
│   │   ├── CertificateNumberGenerator.php
│   │   ├── ExternalUrlValidator.php
│   │   ├── FileNameGenerator.php
│   │   ├── PdfFileInspector.php
│   │   ├── PdfGenerator.php
│   │   ├── QRGenerator.php
│   │   ├── SignedUrlGenerator.php
│   │   ├── SpreadsheetExporter.php
│   │   └── YouTubeEmbedUrlBuilder.php
│   │
│   ├── Models/
│   │   ├── Auth/
│   │   │   ├── LoginAttemptModel.php
│   │   │   ├── PasswordResetTokenModel.php
│   │   │   ├── PermissionModel.php
│   │   │   ├── ProfileChangeRequestModel.php
│   │   │   ├── ProfileVerificationModel.php
│   │   │   ├── RoleModel.php
│   │   │   ├── RolePermissionModel.php
│   │   │   ├── RoleRequestModel.php
│   │   │   ├── UserAcademicProfileModel.php
│   │   │   ├── UserAsnProfileModel.php
│   │   │   ├── UserModel.php
│   │   │   ├── UserProfileModel.php
│   │   │   ├── UserProviderProfileModel.php
│   │   │   ├── UserPublicProfileModel.php
│   │   │   └── UserRoleModel.php
│   │   ├── Organization/
│   │   │   ├── OrganizationUnitModel.php
│   │   │   ├── ProcurementRoleModel.php
│   │   │   ├── UserOrganizationModel.php
│   │   │   ├── UserProcurementRoleModel.php
│   │   │   └── WilayahModel.php
│   │   ├── Learning/
│   │   │   ├── AssessmentConfigurationModel.php
│   │   │   ├── AssignmentGradeModel.php
│   │   │   ├── AssignmentModel.php
│   │   │   ├── AssignmentSubmissionFileModel.php
│   │   │   ├── AssignmentSubmissionModel.php
│   │   │   ├── ClassEvaluatorModel.php
│   │   │   ├── ClassTrainerModel.php
│   │   │   ├── CourseModel.php
│   │   │   ├── EnrollmentModel.php
│   │   │   ├── FinalGradeModel.php
│   │   │   ├── LearningClassModel.php
│   │   │   ├── LearningModuleModel.php
│   │   │   ├── LearningProgressModel.php
│   │   │   ├── LessonMaterialModel.php
│   │   │   ├── LessonModel.php
│   │   │   ├── ParticipantFeedbackModel.php
│   │   │   ├── PostTestAttemptModel.php
│   │   │   ├── PreTestAttemptModel.php
│   │   │   ├── ProgramModel.php
│   │   │   ├── QuestionModel.php
│   │   │   ├── QuestionOptionModel.php
│   │   │   ├── QuizAnswerModel.php
│   │   │   ├── QuizAnswerOptionModel.php
│   │   │   ├── QuizAttemptModel.php
│   │   │   └── QuizModel.php
│   │   ├── Knowledge/
│   │   │   ├── ArticleModel.php
│   │   │   ├── ArticleTagModel.php
│   │   │   ├── BookmarkModel.php
│   │   │   ├── CaseStudyModel.php
│   │   │   ├── DocumentTemplateModel.php
│   │   │   ├── FaqModel.php
│   │   │   ├── KnowledgeCategoryModel.php
│   │   │   ├── KnowledgeMediaContentModel.php
│   │   │   ├── KnowledgeMediaItemModel.php
│   │   │   └── TagModel.php
│   │   ├── Regulation/
│   │   │   ├── CompetencyModel.php
│   │   │   ├── ProcurementStageModel.php
│   │   │   ├── RegulationCategoryModel.php
│   │   │   ├── RegulationCompetencyModel.php
│   │   │   ├── RegulationDownloadLogModel.php
│   │   │   ├── RegulationKeywordModel.php
│   │   │   ├── RegulationModel.php
│   │   │   ├── RegulationProcurementStageModel.php
│   │   │   ├── RegulationRelationModel.php
│   │   │   └── RegulationVersionModel.php
│   │   ├── Communication/
│   │   │   ├── EventAttendanceModel.php
│   │   │   ├── EventModel.php
│   │   │   ├── EventRegistrationModel.php
│   │   │   ├── NewsCategoryModel.php
│   │   │   └── NewsModel.php
│   │   ├── Certificate/
│   │   │   ├── CertificateModel.php
│   │   │   ├── CertificateRevocationModel.php
│   │   │   ├── CertificateSequenceModel.php
│   │   │   ├── CertificateTemplateModel.php
│   │   │   └── CertificateVerificationLogModel.php
│   │   ├── Intelligence/
│   │   │   ├── CompetencyCertificateModel.php
│   │   │   ├── CompetencyCertificateTypeModel.php
│   │   │   ├── CompetencyGapSnapshotModel.php
│   │   │   ├── JfPpbjHistoryModel.php
│   │   │   ├── JfPpbjLevelModel.php
│   │   │   └── JfPpbjPersonnelModel.php
│   │   └── Shared/
│   │       ├── ApprovalHistoryModel.php
│   │       ├── ApprovalModel.php
│   │       ├── AuditLogModel.php
│   │       ├── FileAssetModel.php
│   │       └── NotificationModel.php
│   │
│   ├── Services/
│   │   ├── Auth/
│   │   │   ├── AccessControlService.php
│   │   │   ├── AuthenticationService.php
│   │   │   ├── EmailVerificationService.php
│   │   │   ├── PasswordService.php
│   │   │   ├── ProfileChangeRequestService.php
│   │   │   ├── ProfileService.php
│   │   │   ├── ProfileVerificationService.php
│   │   │   ├── RegistrationProfileService.php
│   │   │   ├── RegistrationService.php
│   │   │   └── RoleRequestService.php
│   │   ├── Organization/
│   │   │   ├── AsnOrganizationProfileService.php
│   │   │   ├── OrganizationService.php
│   │   │   ├── ProcurementRoleService.php
│   │   │   └── WilayahService.php
│   │   ├── Learning/
│   │   │   ├── AssessmentConfigurationService.php
│   │   │   ├── AssignmentService.php
│   │   │   ├── ClassService.php
│   │   │   ├── CourseService.php
│   │   │   ├── EnrollmentService.php
│   │   │   ├── GradingService.php
│   │   │   ├── LearningCompletionService.php
│   │   │   ├── LessonService.php
│   │   │   ├── PostTestService.php
│   │   │   ├── PreTestService.php
│   │   │   ├── ProgramService.php
│   │   │   ├── ProgressService.php
│   │   │   └── QuizService.php
│   │   ├── Knowledge/
│   │   │   ├── ArticleService.php
│   │   │   ├── BookmarkService.php
│   │   │   ├── DocumentTemplateService.php
│   │   │   ├── ExternalMediaUrlService.php
│   │   │   ├── KnowledgeCarouselService.php
│   │   │   ├── KnowledgeMediaGalleryService.php
│   │   │   ├── KnowledgeMediaService.php
│   │   │   └── KnowledgeSearchService.php
│   │   ├── Regulation/
│   │   │   ├── RegulationDownloadService.php
│   │   │   ├── RegulationRelationService.php
│   │   │   ├── RegulationSearchService.php
│   │   │   ├── RegulationService.php
│   │   │   └── RegulationVersionService.php
│   │   ├── Communication/
│   │   │   ├── AttendanceService.php
│   │   │   ├── EventRegistrationService.php
│   │   │   ├── EventService.php
│   │   │   └── NewsService.php
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
│   └── Views/
│       ├── main/
│       │   ├── layouts/
│       │   │   ├── auth.php
│       │   │   ├── minimal.php
│       │   │   ├── public.php
│       │   │   └── workspace.php
│       │   ├── components/
│       │   │   ├── assessment_status.php
│       │   │   ├── bookmark_button.php
│       │   │   ├── breadcrumb.php
│       │   │   ├── carousel_controls.php
│       │   │   ├── confirmation_modal.php
│       │   │   ├── empty_state.php
│       │   │   ├── file_preview.php
│       │   │   ├── flash_message.php
│       │   │   ├── footer.php
│       │   │   ├── head.php
│       │   │   ├── media_card.php
│       │   │   ├── pagination.php
│       │   │   ├── pdf_viewer.php
│       │   │   ├── public_navigation.php
│       │   │   ├── scripts.php
│       │   │   ├── search_form.php
│       │   │   ├── workspace_navigation.php
│       │   │   └── workspace_sidebar.php
│       │   ├── auth/
│       │   │   ├── change_password.php
│       │   │   ├── email_verification.php
│       │   │   ├── forgot_password.php
│       │   │   ├── login.php
│       │   │   ├── pending_verification.php
│       │   │   ├── profile.php
│       │   │   ├── register_account.php
│       │   │   ├── register_profile_academic.php
│       │   │   ├── register_profile_asn.php
│       │   │   ├── register_profile_public.php
│       │   │   ├── register_profile_provider.php
│       │   │   ├── register_profile_type.php
│       │   │   ├── register_review.php
│       │   │   ├── register_success.php
│       │   │   ├── reset_password.php
│       │   │   └── revision_required.php
│       │   ├── public/
│       │   │   ├── home/
│       │   │   │   ├── index.php
│       │   │   │   └── sections/
│       │   │   │       ├── call_to_action.php
│       │   │   │       ├── ecosystem_overview.php
│       │   │   │       ├── featured_courses.php
│       │   │   │       ├── featured_knowledge.php
│       │   │   │       ├── latest_regulations.php
│       │   │   │       ├── public_statistics.php
│       │   │   │       └── upcoming_events.php
│       │   │   ├── learning/
│       │   │   ├── knowledge/
│       │   │   │   ├── articles/
│       │   │   │   ├── case_studies/
│       │   │   │   ├── faqs/
│       │   │   │   ├── gallery/
│       │   │   │   ├── index.php
│       │   │   │   ├── media/
│       │   │   │   ├── templates/
│       │   │   │   └── videos/
│       │   │   ├── regulation/
│       │   │   │   ├── detail.php
│       │   │   │   ├── index.php
│       │   │   │   ├── relation_list.php
│       │   │   │   └── version_history.php
│       │   │   ├── communication/
│       │   │   ├── statistics/
│       │   │   └── certificate/
│       │   └── workspace/
│       │       ├── shared/
│       │       ├── bookmarks/
│       │       │   ├── certificates.php
│       │       │   ├── communication.php
│       │       │   ├── index.php
│       │       │   ├── knowledge.php
│       │       │   ├── learning.php
│       │       │   └── regulations.php
│       │       ├── participant/
│       │       │   ├── assignment_revision.php
│       │       │   ├── assignment_submission.php
│       │       │   ├── assignments.php
│       │       │   ├── certificates.php
│       │       │   ├── class_detail.php
│       │       │   ├── feedback.php
│       │       │   ├── index.php
│       │       │   ├── my_classes.php
│       │       │   ├── post_test.php
│       │       │   ├── post_test_result.php
│       │       │   ├── pre_test.php
│       │       │   ├── pre_test_result.php
│       │       │   ├── progress.php
│       │       │   ├── quiz.php
│       │       │   ├── quiz_result.php
│       │       │   └── study.php
│       │       ├── trainer/
│       │       ├── evaluator/
│       │       ├── operator/
│       │       └── profile/
│       │           ├── account.php
│       │           ├── academic_profile.php
│       │           ├── asn_profile.php
│       │           ├── change_requests.php
│       │           ├── index.php
│       │           ├── procurement_roles.php
│       │           ├── provider_profile.php
│       │           ├── public_profile.php
│       │           └── role_requests.php
│       │
│       ├── dashboard/
│       │   ├── layouts/
│       │   │   ├── auth.php
│       │   │   ├── dashboard.php
│       │   │   └── print.php
│       │   ├── components/
│       │   │   ├── approval_timeline.php
│       │   │   ├── breadcrumb.php
│       │   │   ├── chart_container.php
│       │   │   ├── confirmation_modal.php
│       │   │   ├── data_table.php
│       │   │   ├── filter_bar.php
│       │   │   ├── flash_message.php
│       │   │   ├── footer.php
│       │   │   ├── head.php
│       │   │   ├── header.php
│       │   │   ├── pdf_viewer_modal.php
│       │   │   ├── scripts.php
│       │   │   ├── sidebar.php
│       │   │   └── status_badge.php
│       │   ├── auth/
│       │   │   └── login.php
│       │   ├── home/
│       │   │   └── index.php
│       │   ├── users/
│       │   │   ├── detail.php
│       │   │   ├── index.php
│       │   │   ├── profile_change_requests.php
│       │   │   ├── profile_verifications.php
│       │   │   └── role_requests.php
│       │   ├── access/
│       │   ├── organization/
│       │   │   ├── procurement_roles/
│       │   │   └── units/
│       │   ├── approvals/
│       │   ├── audit/
│       │   ├── learning/
│       │   ├── knowledge/
│       │   │   ├── carousel/
│       │   │   ├── galleries/
│       │   │   └── media/
│       │   ├── regulation/
│       │   │   ├── relations/
│       │   │   ├── regulations/
│       │   │   └── versions/
│       │   ├── communication/
│       │   ├── certificates/
│       │   ├── intelligence/
│       │   ├── reports/
│       │   └── settings/
│       │
│       ├── errors/
│       │   ├── html/
│       │   │   ├── error_400.php
│       │   │   ├── error_403.php
│       │   │   ├── error_404.php
│       │   │   ├── error_429.php
│       │   │   └── error_500.php
│       │   └── cli/
│       │
├── public/
│   ├── assets/
│   │   ├── main/
│   │   ├── dashboard/
│   │   └── shared/
│   │       ├── images/
│   │       └── fonts/
│   └── index.php
│
├── writable/
│   ├── cache/
│   ├── logs/
│   ├── session/
│   ├── uploads/
│   │   ├── learning/
│   │   │   ├── lessons/
│   │   │   ├── materials/
│   │   │   └── assignments/
│   │   ├── knowledge/
│   │   │   ├── templates/
│   │   │   ├── media/
│   │   │   ├── gallery/
│   │   │   └── attachments/
│   │   ├── regulations/
│   │   │   ├── originals/
│   │   │   └── versions/
│   │   ├── communication/
│   │   │   ├── news/
│   │   │   └── events/
│   │   ├── certificates/
│   │   │   ├── generated/
│   │   │   └── templates/
│   │   └── temporary/
│   └── exports/
│       ├── reports/
│       └── spreadsheets/
│
├── tests/
│   ├── unit/
│   │   ├── Libraries/
│   │   └── Services/
│   │       ├── Auth/
│   │       ├── Certificate/
│   │       ├── Knowledge/
│   │       ├── Learning/
│   │       ├── Regulation/
│   │       └── Shared/
│   ├── feature/
│   │   ├── Admin/
│   │   ├── Auth/
│   │   ├── Certificate/
│   │   ├── Communication/
│   │   ├── Knowledge/
│   │   ├── Landing/
│   │   ├── Learning/
│   │   ├── Registration/
│   │   ├── Regulation/
│   │   ├── Shared/
│   │   └── Workspace/
│   ├── integration/
│   │   ├── Database/
│   │   ├── FileStorage/
│   │   ├── Registration/
│   │   └── Workflow/
│   └── _support/
│       ├── Factories/
│       └── Fixtures/
│
