<?php

namespace App\Services\Communication;

use App\Models\Communication\EventAttendanceModel;
use App\Models\Communication\EventModel;
use App\Models\Communication\EventRegistrationModel;
use App\Models\Communication\NewsCategoryModel;
use App\Models\Communication\NewsModel;
use App\Services\AuditService;
use CodeIgniter\HTTP\Files\UploadedFile;

/**
 * Communication service: handles create/update/delete for news, categories,
 * events, registrations, attendance, with audit logging and a file upload
 * helper that writes through file_assets.
 */
class CommunicationService
{
    public function __construct(
        private readonly NewsModel $news = new NewsModel(),
        private readonly NewsCategoryModel $newsCategories = new NewsCategoryModel(),
        private readonly EventModel $events = new EventModel(),
        private readonly EventRegistrationModel $registrations = new EventRegistrationModel(),
        private readonly EventAttendanceModel $attendance = new EventAttendanceModel(),
        private readonly AuditService $audit = new AuditService(),
    ) {}

    // ============== NEWS ==============

    public function createNews(array $data, ?int $actorId = null): int
    {
        $now = date('Y-m-d H:i:s');
        $payload = [
            'category_id'     => ! empty($data['category_id']) ? (int) $data['category_id'] : null,
            'title'            => trim((string) ($data['title'] ?? '')),
            'slug'             => $this->news->generateUniqueSlug(trim((string) ($data['title'] ?? 'berita'))),
            'summary'          => (string) ($data['summary'] ?? ''),
            'content'          => (string) ($data['content'] ?? ''),
            'workflow_status'  => (string) ($data['workflow_status'] ?? 'draft'),
            'visibility'       => (string) ($data['visibility'] ?? 'public'),
            'author_id'        => $actorId,
            'scheduled_at'     => $this->parseDateTime($data['scheduled_at'] ?? null),
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'news_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        if (($payload['workflow_status'] ?? '') === 'published') {
            $payload['published_at'] = $now;
            $payload['published_by'] = $actorId;
        }
        $payload['created_at'] = $now;
        $payload['updated_at'] = $now;

        $id = (int) $this->news->insert($payload, true);
        $this->audit->log($actorId, 'news.create', 'news', 'news', $id, ['title' => $payload['title']]);
        return $id;
    }

    public function updateNews(int $id, array $data, ?int $actorId = null): bool
    {
        $current = $this->news->find($id);
        if (! $current) {
            return false;
        }
        $payload = [
            'category_id'     => ! empty($data['category_id']) ? (int) $data['category_id'] : null,
            'title'            => trim((string) ($data['title'] ?? '')),
            'slug'             => $this->news->generateUniqueSlug(trim((string) ($data['title'] ?? 'berita')), $id),
            'summary'          => (string) ($data['summary'] ?? ''),
            'content'          => (string) ($data['content'] ?? ''),
            'workflow_status'  => (string) ($data['workflow_status'] ?? 'draft'),
            'visibility'       => (string) ($data['visibility'] ?? 'public'),
            'scheduled_at'     => $this->parseDateTime($data['scheduled_at'] ?? null),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'news_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        if (($payload['workflow_status'] ?? '') === 'published' && empty($current['published_at'])) {
            $payload['published_at'] = date('Y-m-d H:i:s');
            $payload['published_by'] = $actorId;
        }
        $ok = (bool) $this->news->update($id, $payload);
        if ($ok) {
            $this->audit->log($actorId, 'news.update', 'news', 'news', $id);
        }
        return $ok;
    }

    public function deleteNews(int $id, ?int $actorId = null): bool
    {
        $ok = (bool) $this->news->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'news.delete', 'news', 'news', $id);
        }
        return $ok;
    }

    // ============== CATEGORIES ==============

    public function createCategory(array $data, ?int $actorId = null): int
    {
        $name = trim((string) ($data['name'] ?? ''));
        $id = (int) $this->newsCategories->insert([
            'name'        => $name,
            'slug'        => $this->newsCategories->generateUniqueSlug($name),
            'description' => (string) ($data['description'] ?? ''),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ], true);
        $this->audit->log($actorId, 'news_category.create', 'news_categories', 'news_category', $id);
        return $id;
    }

    public function updateCategory(int $id, array $data, ?int $actorId = null): bool
    {
        $ok = (bool) $this->newsCategories->update($id, [
            'name'        => trim((string) ($data['name'] ?? '')),
            'slug'        => $this->newsCategories->generateUniqueSlug(trim((string) ($data['name'] ?? 'kategori')), $id),
            'description' => (string) ($data['description'] ?? ''),
            'is_active'   => isset($data['is_active']) ? 1 : 0,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
        if ($ok) {
            $this->audit->log($actorId, 'news_category.update', 'news_categories', 'news_category', $id);
        }
        return $ok;
    }

    public function deleteCategory(int $id, ?int $actorId = null): bool
    {
        $ok = (bool) $this->newsCategories->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'news_category.delete', 'news_categories', 'news_category', $id);
        }
        return $ok;
    }

    // ============== EVENTS ==============

    public function createEvent(array $data, ?int $actorId = null): int
    {
        $title = trim((string) ($data['title'] ?? ''));
        $now = date('Y-m-d H:i:s');
        $payload = [
            'title'                => $title,
            'slug'                 => $this->events->generateUniqueSlug($title),
            'type'                 => (string) ($data['type'] ?? 'online'),
            'mode'                 => (string) ($data['mode'] ?? 'open'),
            'status'               => (string) ($data['status'] ?? 'draft'),
            'visibility'           => (string) ($data['visibility'] ?? 'public'),
            'summary'              => (string) ($data['summary'] ?? ''),
            'description'          => (string) ($data['description'] ?? ''),
            'start_at'              => $this->parseDateTime($data['start_at'] ?? null),
            'end_at'                => $this->parseDateTime($data['end_at'] ?? null),
            'location'              => (string) ($data['location'] ?? ''),
            'meeting_url'           => (string) ($data['meeting_url'] ?? ''),
            'registration_open_at'  => $this->parseDateTime($data['registration_open_at'] ?? null),
            'registration_close_at' => $this->parseDateTime($data['registration_close_at'] ?? null),
            'capacity'              => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null,
            'waitlist_enabled'      => isset($data['waitlist_enabled']) ? 1 : 0,
            'certificate_enabled'   => isset($data['certificate_enabled']) ? 1 : 0,
            'created_by'            => $actorId,
            'updated_by'            => $actorId,
            'created_at'            => $now,
            'updated_at'            => $now,
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'event_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        $id = (int) $this->events->insert($payload, true);
        $this->audit->log($actorId, 'event.create', 'events', 'event', $id);
        return $id;
    }

    public function updateEvent(int $id, array $data, ?int $actorId = null): bool
    {
        $payload = [
            'title'                => trim((string) ($data['title'] ?? '')),
            'slug'                 => $this->events->generateUniqueSlug(trim((string) ($data['title'] ?? 'event')), $id),
            'type'                 => (string) ($data['type'] ?? 'online'),
            'mode'                 => (string) ($data['mode'] ?? 'open'),
            'status'               => (string) ($data['status'] ?? 'draft'),
            'visibility'           => (string) ($data['visibility'] ?? 'public'),
            'summary'              => (string) ($data['summary'] ?? ''),
            'description'          => (string) ($data['description'] ?? ''),
            'start_at'              => $this->parseDateTime($data['start_at'] ?? null),
            'end_at'                => $this->parseDateTime($data['end_at'] ?? null),
            'location'              => (string) ($data['location'] ?? ''),
            'meeting_url'           => (string) ($data['meeting_url'] ?? ''),
            'registration_open_at'  => $this->parseDateTime($data['registration_open_at'] ?? null),
            'registration_close_at' => $this->parseDateTime($data['registration_close_at'] ?? null),
            'capacity'              => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null,
            'waitlist_enabled'      => isset($data['waitlist_enabled']) ? 1 : 0,
            'certificate_enabled'   => isset($data['certificate_enabled']) ? 1 : 0,
            'updated_by'            => $actorId,
            'updated_at'            => date('Y-m-d H:i:s'),
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'event_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        $ok = (bool) $this->events->update($id, $payload);
        if ($ok) {
            $this->audit->log($actorId, 'event.update', 'events', 'event', $id);
        }
        return $ok;
    }

    public function deleteEvent(int $id, ?int $actorId = null): bool
    {
        $ok = (bool) $this->events->delete($id);
        if ($ok) {
            $this->audit->log($actorId, 'event.delete', 'events', 'event', $id);
        }
        return $ok;
    }

    public function updateRegistrationStatus(int $id, string $newStatus, ?int $actorId = null): bool
    {
        if (! in_array($newStatus, ['registered', 'cancelled', 'promoted', 'attended', 'no_show'], true)) {
            throw new \InvalidArgumentException('Status registrasi tidak valid.');
        }
        $now = date('Y-m-d H:i:s');
        $patch = ['status' => $newStatus, 'updated_at' => $now];
        if ($newStatus === 'cancelled') $patch['cancelled_at'] = $now;
        if ($newStatus === 'promoted')  $patch['promoted_at']  = $now;
        $ok = (bool) $this->registrations->update($id, $patch);
        if ($ok) {
            $this->audit->log($actorId, 'event_registration.update', 'event_registrations', 'event_registration', $id, ['status' => $newStatus]);
        }
        return $ok;
    }

    public function recordAttendance(int $registrationId, string $status, ?int $actorId = null, ?string $notes = null): bool
    {
        if (! in_array($status, ['present', 'absent', 'excused'], true)) {
            throw new \InvalidArgumentException('Status kehadiran tidak valid.');
        }
        $now = date('Y-m-d H:i:s');
        $ok = (bool) $this->attendance->insert([
            'event_registration_id' => $registrationId,
            'status'               => $status,
            'recorded_by'          => $actorId,
            'recorded_at'          => $now,
            'notes'                => $notes,
            'created_at'           => $now,
            'updated_at'           => $now,
        ], true);
        if ($ok) {
            $this->audit->log($actorId, 'attendance.record', 'attendances', 'attendance', (int) $this->attendance->getInsertID(), [
                'registration_id' => $registrationId,
                'status'          => $status,
            ]);
        }
        return $ok;
    }

    // ============== FILE HELPER ==============

    /**
     * Stream-uploaded file to writable/uploads/<resource>/ + file_assets row.
     * Returns file_assets.id or null on failure.
     */
    public function storeFile(UploadedFile $file, string $resourceType, ?int $actorId = null): ?int
    {
        if (! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $dir = WRITEPATH . 'uploads/' . trim($resourceType, '/');
        if (! is_dir($dir) && ! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
            return null;
        }
        $storedName = $file->getRandomName();
        if (! $file->move($dir, $storedName)) {
            return null;
        }
        $now = date('Y-m-d H:i:s');
        $relativePath = 'uploads/' . trim($resourceType, '/') . '/' . $storedName;
        $ok = db_connect()->table('file_assets')->insert([
            'disk'             => 'local',
            'path'             => $relativePath,
            'original_name'    => $file->getClientName() ?: $storedName,
            'stored_name'      => $storedName,
            'extension'        => $file->getClientExtension() ?: 'bin',
            'mime_type'        => $file->getClientMimeType() ?: 'application/octet-stream',
            'size_bytes'       => (int) ($file->getSize() ?: 0),
            'visibility'       => 'public',
            'lifecycle_status' => 'available',
            'resource_type'    => $resourceType,
            'resource_id'      => null,
            'uploaded_by'      => $actorId,
            'available_at'     => $now,
            'created_at'       => $now,
            'updated_at'       => $now,
        ], true);
        if (! $ok) {
            @unlink($dir . DIRECTORY_SEPARATOR . $storedName);
            return null;
        }
        return (int) db_connect()->insertID();
    }

    private function parseDateTime(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        $ts = strtotime($value);
        return $ts === false ? null : date('Y-m-d H:i:s', $ts);
    }

    public function listActiveCategoriesWithCount(): array
    {
        return $this->newsCategories->listAllWithCount();
    }

    public function listActiveEventsForDropdown(): array
    {
        return $this->events->where('deleted_at', null)->orderBy('start_at', 'DESC')->findAll();
    }
}