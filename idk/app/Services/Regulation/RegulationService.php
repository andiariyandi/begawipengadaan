<?php

namespace App\Services\Regulation;

use App\Models\Regulation\RegulationModel;
use App\Models\Regulation\RegulationVersionModel;
use App\Services\AuditService;

/**
 * Regulation service: orchestrates CRUD + workflow lifecycle.
 *
 * Workflow states (CLAUDE.md §20):
 *   draft → submitted → in_review → approved → published → archived
 *              └──────────→ revision_required → (back to) submitted
 *              └──────────→ rejected
 *
 * Every state change writes audit_logs AND workflow_transitions.
 * File upload goes through file_assets table (no direct filesystem paths).
 */
class RegulationService
{
    public const ALLOWED_WORKFLOW_STATUSES = [
        'draft', 'submitted', 'in_review', 'approved',
        'published', 'rejected', 'revision_required', 'archived',
    ];

    public const TRANSITION_RULES = [
        'draft'             => ['submitted', 'archived'],
        // Allow direct approve/publish path from submitted for back-office UI that
        // collapses review into a single step; in_review remains as intermediate.
        'submitted'         => ['in_review', 'approved', 'rejected', 'archived'],
        'in_review'         => ['approved', 'rejected', 'revision_required'],
        'revision_required' => ['submitted', 'archived'],
        'approved'          => ['published', 'archived'],
        'published'         => ['archived'],
        'rejected'          => ['archived'],
        'archived'          => [],
    ];

    public function __construct(
        private readonly RegulationModel $regulations = new RegulationModel(),
        private readonly RegulationVersionModel $versions = new RegulationVersionModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    // ---------- CRUD ----------

    public function create(array $data, ?int $actorId = null): int
    {
        $data['slug']                = $data['slug'] ?? $this->generateSlug($data['title']);
        $data['workflow_status']     = $data['workflow_status'] ?? 'draft';
        $data['legal_status']        = $data['legal_status'] ?? 'active';
        $data['visibility']          = $data['visibility'] ?? 'internal';
        $data['created_by']          = $actorId;
        $data['updated_by']          = $actorId;

        $id = (int) $this->regulations->insert($data, true);
        $this->writeTransition('regulation', $id, null, $data['workflow_status'], 'regulation.create', $actorId, null, null, ['title' => $data['title']]);
        $this->audit->log($actorId, 'regulation.create', 'regulations', 'regulation', $id, ['title' => $data['title']]);
        return $id;
    }

    public function update(int $id, array $data, ?int $actorId = null): bool
    {
        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }
        $data['updated_by'] = $actorId;
        $ok = (bool) $this->regulations->update($id, $data);
        if ($ok) {
            $this->audit->log($actorId, 'regulation.update', 'regulations', 'regulation', $id, array_keys($data));
        }
        return $ok;
    }

    public function delete(int $id, ?int $actorId = null): bool
    {
        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }
        // Soft delete — model uses useSoftDeletes=true.
        $ok = (bool) $this->regulations->delete($id);
        if ($ok) {
            $this->writeTransition('regulation', $id, $current['workflow_status'] ?? null, 'archived', 'regulation.delete', $actorId, null, null);
            $this->audit->log($actorId, 'regulation.delete', 'regulations', 'regulation', $id);
        }
        return $ok;
    }

    // ---------- Workflow transitions ----------

    public function submitReview(int $id, ?int $actorId = null, ?string $notes = null): bool
    {
        return $this->transition($id, 'submitted', 'regulation.submit', $actorId, $notes);
    }

    public function approve(int $id, ?int $actorId = null, ?string $notes = null): bool
    {
        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }
        $ok = $this->transition($id, 'approved', 'regulation.approve', $actorId, $notes, [
            'reviewed_by' => $actorId,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);
        if ($ok) {
            $this->regulations->update($id, ['workflow_status' => 'approved', 'reviewed_by' => $actorId]);
        }
        return $ok;
    }

    public function reject(int $id, string $reason, ?int $actorId = null): bool
    {
        if (trim($reason) === '') {
            throw new \InvalidArgumentException('Alasan penolakan wajib diisi.');
        }
        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }
        return $this->transition($id, 'rejected', 'regulation.reject', $actorId, $reason, [
            'reviewed_by' => $actorId,
            'reviewed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function requestRevision(int $id, string $note, ?int $actorId = null): bool
    {
        if (trim($note) === '') {
            throw new \InvalidArgumentException('Catatan revisi wajib diisi.');
        }
        return $this->transition($id, 'revision_required', 'regulation.revision', $actorId, $note);
    }

    public function publish(int $id, ?int $actorId = null): bool
    {
        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }
        $ok = $this->transition($id, 'published', 'regulation.publish', $actorId, null, [
            'published_by' => $actorId,
            'published_at' => date('Y-m-d H:i:s'),
        ]);
        if ($ok) {
            $this->regulations->update($id, [
                'workflow_status' => 'published',
                'published_by'    => $actorId,
                'published_at'    => date('Y-m-d H:i:s'),
            ]);
        }
        return $ok;
    }

    // ---------- Versions ----------

    public function addVersion(int $regulationId, int $fileAssetId, ?string $changelog, ?int $actorId = null): int
    {
        $nextNumber = $this->versions->latestVersionNumber($regulationId) + 1;
        $now = date('Y-m-d H:i:s');

        $ok = $this->versions->insert([
            'regulation_id'  => $regulationId,
            'version_number' => $nextNumber,
            'file_id'        => $fileAssetId,
            'changelog'      => $changelog,
            'is_current'     => 0,
            'uploaded_by'    => $actorId,
            'created_at'     => $now,
        ]);
        if (! $ok) {
            return 0;
        }
        $versionId = (int) $this->versions->insertID();

        if ($versionId > 0) {
            // Auto-set as current version (single most recent is current)
            $this->versions->setCurrent($regulationId, $versionId);
            $this->writeTransition('regulation', $regulationId, null, null, 'regulation.version.add', $actorId, null, $changelog, [
                'version_id'    => $versionId,
                'version_number' => $nextNumber,
                'file_id'       => $fileAssetId,
            ]);
            $this->audit->log($actorId, 'regulation.version.add', 'regulation_versions', 'regulation_version', $versionId, [
                'regulation_id' => $regulationId,
                'version_number' => $nextNumber,
            ]);
        }

        return $versionId;
    }

    public function setCurrentVersion(int $regulationId, int $versionId, ?int $actorId = null): bool
    {
        $ok = $this->versions->setCurrent($regulationId, $versionId);
        if ($ok) {
            $this->audit->log($actorId, 'regulation.version.set_current', 'regulation_versions', 'regulation_version', $versionId);
        }
        return $ok;
    }

    // ---------- Helpers ----------

    /**
     * Validate transition against TRANSITION_RULES and execute.
     */
    private function transition(int $id, string $toState, string $action, ?int $actorId, ?string $reason = null, array $extraUpdate = []): bool
    {
        if (! in_array($toState, self::ALLOWED_WORKFLOW_STATUSES, true)) {
            throw new \InvalidArgumentException("Status workflow tidak valid: {$toState}");
        }

        $current = $this->regulations->find($id);
        if (! $current) {
            return false;
        }

        $fromState = (string) ($current['workflow_status'] ?? 'draft');
        if (! in_array($toState, self::TRANSITION_RULES[$fromState] ?? [], true)) {
            throw new \InvalidArgumentException("Transisi {$fromState} → {$toState} tidak diizinkan.");
        }

        $update = ['workflow_status' => $toState] + $extraUpdate;
        $ok = (bool) $this->regulations->update($id, $update);

        if ($ok) {
            $this->writeTransition('regulation', $id, $fromState, $toState, $action, $actorId, $reason);
            $this->audit->log($actorId, $action, 'regulations', 'regulation', $id, [
                'from' => $fromState,
                'to'   => $toState,
            ]);
        }

        return $ok;
    }

    private function writeTransition(
        string $resourceType,
        int $resourceId,
        ?string $fromState,
        ?string $toState,
        string $action,
        ?int $actorId,
        ?string $reason,
        ?string $notes = null,
        ?array $metadata = null,
    ): void {
        try {
            // For non-transition events (e.g. version.add) both from_state and
            // to_state may be null. workflow_transitions.to_state is NOT NULL
            // so fall back to the current workflow_status, and if even that is
            // null, use a sentinel 'unknown' value.
            $resolvedTo = $toState ?? $fromState;
            if ($resolvedTo === null || $resolvedTo === '') {
                $row = $this->regulations->select('workflow_status')->find($resourceId);
                $resolvedTo = $row['workflow_status'] ?? 'unknown';
            }
            db_connect()->table('workflow_transitions')->insert([
                'resource_type' => $resourceType,
                'resource_id'   => $resourceId,
                'from_state'    => $fromState,
                'to_state'      => $resolvedTo,
                'action'        => $action,
                'actor_id'      => $actorId,
                'actor_type'    => 'user',
                'reason'        => $reason,
                'notes'         => $notes,
                'metadata'      => $metadata ? json_encode($metadata, JSON_THROW_ON_ERROR) : null,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[Regulation] workflow_transitions insert failed: ' . $e->getMessage());
        }
    }

    private function generateSlug(string $title): string
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        if ($slug === '') {
            $slug = 'regulation-' . date('YmdHis');
        }
        // Ensure uniqueness
        $base = $slug;
        $i = 1;
        while ($this->regulations->where('slug', $slug)->first()) {
            $slug = $base . '-' . (++$i);
        }
        return $slug;
    }
}