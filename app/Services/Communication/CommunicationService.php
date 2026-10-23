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

    // C-2: Anti Stored-XSS Sanitizer untuk konten publik
    private function sanitizeHtmlContent(string $raw): string
    {
        $clean = strip_tags($raw, '<p><br><b><strong><i><em><u><a><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><hr>');
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(["\'][^"\']*["\']|[^\s>]+)/i', '', $clean);
        return preg_replace('/\b(href)\s*=\s*["\']\s*(javascript|data):[^"\']*["\']/i', '$1="#"', $clean);
    }

    // ============== NEWS ==============

    public function createNews(array $data, ?int $actorId = null): int
    {
        $now = date('Y-m-d H:i:s');
        $payload = [
            'category_id'     => ! empty($data['category_id']) ? (int) $data['category_id'] : null,
            'title'           => trim((string) ($data['title'] ?? '')),
            'slug'            => $this->news->generateUniqueSlug(trim((string) ($data['title'] ?? 'berita'))),
            'summary'         => (string) ($data['summary'] ?? ''),
            'reading_time'    => ! empty($data['reading_time']) ? trim((string) $data['reading_time']) : null,
            'tags'            => ! empty($data['tags']) ? trim((string) $data['tags']) : null,
            'content'         => $this->sanitizeHtmlContent((string) ($data['content'] ?? '')),
            'source_name'     => ! empty($data['source_name']) ? trim((string) $data['source_name']) : null,
            'source_url'      => ! empty($data['source_url']) ? trim((string) $data['source_url']) : null,
            'cover_caption'   => ! empty($data['cover_caption']) ? trim((string) $data['cover_caption']) : null,
            'is_featured'     => ! empty($data['is_featured']) ? 1 : 0,
            'carousel_enabled'=> ! empty($data['carousel_enabled']) ? 1 : 0,
            'carousel_order'  => ! empty($data['carousel_order']) ? (int) $data['carousel_order'] : null,
            'workflow_status' => (string) ($data['workflow_status'] ?? 'draft'),
            'visibility'      => (string) ($data['visibility'] ?? 'public'),
            'author_id'       => $actorId,
            'scheduled_at'    => $this->parseDateTime($data['scheduled_at'] ?? null),
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'news_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        if (($payload['workflow_status'] ?? '') === 'published') {
            $payload['published_at'] = ! empty($data['published_at']) ? $this->parseDateTime($data['published_at']) : $now;
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
            'title'           => trim((string) ($data['title'] ?? '')),
            'slug'            => $this->news->generateUniqueSlug(trim((string) ($data['title'] ?? 'berita')), $id),
            'summary'         => (string) ($data['summary'] ?? ''),
            'reading_time'    => ! empty($data['reading_time']) ? trim((string) $data['reading_time']) : null,
            'tags'            => ! empty($data['tags']) ? trim((string) $data['tags']) : null,
            'content'         => $this->sanitizeHtmlContent((string) ($data['content'] ?? '')),
            'source_name'     => ! empty($data['source_name']) ? trim((string) $data['source_name']) : null,
            'source_url'      => ! empty($data['source_url']) ? trim((string) $data['source_url']) : null,
            'cover_caption'   => ! empty($data['cover_caption']) ? trim((string) $data['cover_caption']) : null,
            'is_featured'     => ! empty($data['is_featured']) ? 1 : 0,
            'carousel_enabled'=> ! empty($data['carousel_enabled']) ? 1 : 0,
            'carousel_order'  => ! empty($data['carousel_order']) ? (int) $data['carousel_order'] : null,
            'workflow_status' => (string) ($data['workflow_status'] ?? 'draft'),
            'visibility'      => (string) ($data['visibility'] ?? 'public'),
            'scheduled_at'    => $this->parseDateTime($data['scheduled_at'] ?? null),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        if (! empty($data['cover_file'])) {
            $fileId = $this->storeFile($data['cover_file'], 'news_cover', $actorId);
            if ($fileId) {
                $payload['cover_file_id'] = $fileId;
            }
        }
        if (! empty($data['published_at'])) {
            $payload['published_at'] = $this->parseDateTime($data['published_at']);
        } elseif (($payload['workflow_status'] ?? '') === 'published' && empty($current['published_at'])) {
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
        $slug = ! empty($data['slug']) ? url_title(strtolower(trim((string) $data['slug'])), '-', true) : $this->newsCategories->generateUniqueSlug($name);
        $isActive = array_key_exists('is_active', $data) ? (! empty($data['is_active']) ? 1 : 0) : 1;
        $id = (int) $this->newsCategories->insert([
            'name'        => $name,
            'slug'        => $slug,
            'description' => (string) ($data['description'] ?? ''),
            'is_active'   => $isActive,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ], true);
        $this->audit->log($actorId, 'news_category.create', 'news_categories', 'news_category', $id);
        return $id;
    }

    public function updateCategory(int $id, array $data, ?int $actorId = null): bool
    {
        $name = trim((string) ($data['name'] ?? ''));
        $slug = ! empty($data['slug']) ? url_title(strtolower(trim((string) $data['slug'])), '-', true) : $this->newsCategories->generateUniqueSlug($name !== '' ? $name : 'kategori', $id);
        $isActive = array_key_exists('is_active', $data) ? (! empty($data['is_active']) ? 1 : 0) : 0;
        $ok = (bool) $this->newsCategories->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => (string) ($data['description'] ?? ''),
            'is_active'   => $isActive,
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

        // Normalisasi mode ('online', 'offline', 'hybrid') dan type
        $mode = strtolower(trim((string) ($data['mode'] ?? 'online')));
        $type = strtolower(trim((string) ($data['type'] ?? 'webinar')));

        // Tangani kasus jika nilai mode dan type tertukar dari form
        if (in_array($type, ['online', 'offline', 'hybrid'], true) && ! in_array($mode, ['online', 'offline', 'hybrid'], true)) {
            $temp = $mode;
            $mode = $type;
            $type = in_array($temp, ['open', 'invitation', 'closed'], true) ? 'webinar' : $temp;
        }

        if (! in_array($mode, ['online', 'offline', 'hybrid'], true)) {
            $mode = 'online';
        }

        $validStatuses = ['draft', 'coming_soon', 'open_registration', 'full', 'finished', 'cancelled'];
        $status = strtolower(trim((string) ($data['status'] ?? 'draft')));
        if (! in_array($status, $validStatuses, true)) {
            $status = 'draft';
        }

        $validVisibilities = ['public', 'internal', 'restricted'];
        $visibility = strtolower(trim((string) ($data['visibility'] ?? 'public')));
        if (! in_array($visibility, $validVisibilities, true)) {
            $visibility = 'public';
        }

        $payload = [
            'title'                 => $title,
            'slug'                  => $this->events->generateUniqueSlug($title),
            'type'                  => $type !== '' ? $type : 'webinar',
            'mode'                  => $mode,
            'status'                => $status,
            'visibility'            => $visibility,
            'summary'               => (string) ($data['summary'] ?? ''),
            'description'           => (string) ($data['description'] ?? ''),
            'start_at'              => $this->parseDateTime($data['start_at'] ?? null),
            'end_at'                => $this->parseDateTime($data['end_at'] ?? null),
            'location'              => (string) ($data['location'] ?? ''),
            'meeting_url'           => (string) ($data['meeting_url'] ?? ''),
            'registration_open_at'  => $this->parseDateTime($data['registration_open_at'] ?? null),
            'registration_close_at' => $this->parseDateTime($data['registration_close_at'] ?? null),
            'capacity'              => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null,
            'waitlist_enabled'      => isset($data['waitlist_enabled']) ? 1 : 0,
            'certificate_enabled'   => isset($data['certificate_enabled']) ? 1 : 0,
            'is_featured'           => ! empty($data['is_featured']) ? 1 : 0,
            'carousel_enabled'      => ! empty($data['carousel_enabled']) ? 1 : 0,
            'carousel_order'        => ! empty($data['carousel_order']) ? (int) $data['carousel_order'] : null,
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
        $current = $this->events->find($id);
        if (! $current) {
            return false;
        }

        $mode = strtolower(trim((string) ($data['mode'] ?? $current['mode'] ?? 'online')));
        $type = strtolower(trim((string) ($data['type'] ?? $current['type'] ?? 'webinar')));

        if (in_array($type, ['online', 'offline', 'hybrid'], true) && ! in_array($mode, ['online', 'offline', 'hybrid'], true)) {
            $temp = $mode;
            $mode = $type;
            $type = in_array($temp, ['open', 'invitation', 'closed'], true) ? ($current['type'] ?? 'webinar') : $temp;
        }

        if (! in_array($mode, ['online', 'offline', 'hybrid'], true)) {
            $mode = $current['mode'] ?? 'online';
        }

        $validStatuses = ['draft', 'coming_soon', 'open_registration', 'full', 'finished', 'cancelled'];
        $status = strtolower(trim((string) ($data['status'] ?? $current['status'] ?? 'draft')));
        if (! in_array($status, $validStatuses, true)) {
            $status = $current['status'] ?? 'draft';
        }

        $validVisibilities = ['public', 'internal', 'restricted'];
        $visibility = strtolower(trim((string) ($data['visibility'] ?? $current['visibility'] ?? 'public')));
        if (! in_array($visibility, $validVisibilities, true)) {
            $visibility = $current['visibility'] ?? 'public';
        }

        $title = isset($data['title']) && trim($data['title']) !== '' ? trim((string) $data['title']) : $current['title'];
        $slug = ($title !== $current['title']) ? $this->events->generateUniqueSlug($title, $id) : $current['slug'];

        $payload = [
            'title'                 => $title,
            'slug'                  => $slug,
            'type'                  => $type !== '' ? $type : ($current['type'] ?? 'webinar'),
            'mode'                  => $mode,
            'status'                => $status,
            'visibility'            => $visibility,
            'summary'               => array_key_exists('summary', $data) ? (string) $data['summary'] : ($current['summary'] ?? ''),
            'description'           => array_key_exists('description', $data) ? (string) $data['description'] : ($current['description'] ?? ''),
            'start_at'              => array_key_exists('start_at', $data) ? $this->parseDateTime($data['start_at']) : $current['start_at'],
            'end_at'                => array_key_exists('end_at', $data) ? $this->parseDateTime($data['end_at']) : $current['end_at'],
            'location'              => array_key_exists('location', $data) ? (string) $data['location'] : ($current['location'] ?? ''),
            'meeting_url'           => array_key_exists('meeting_url', $data) ? (string) $data['meeting_url'] : ($current['meeting_url'] ?? ''),
            'registration_open_at'  => array_key_exists('registration_open_at', $data) ? $this->parseDateTime($data['registration_open_at']) : $current['registration_open_at'],
            'registration_close_at' => array_key_exists('registration_close_at', $data) ? $this->parseDateTime($data['registration_close_at']) : $current['registration_close_at'],
            'capacity'              => array_key_exists('capacity', $data) ? ($data['capacity'] !== '' && $data['capacity'] !== null ? (int) $data['capacity'] : null) : $current['capacity'],
            'waitlist_enabled'      => array_key_exists('waitlist_enabled', $data) ? (isset($data['waitlist_enabled']) && $data['waitlist_enabled'] ? 1 : 0) : (int) ($current['waitlist_enabled'] ?? 0),
            'certificate_enabled'   => array_key_exists('certificate_enabled', $data) ? (isset($data['certificate_enabled']) && $data['certificate_enabled'] ? 1 : 0) : (int) ($current['certificate_enabled'] ?? 0),
            'is_featured'           => array_key_exists('is_featured', $data) ? (! empty($data['is_featured']) ? 1 : 0) : (int) ($current['is_featured'] ?? 0),
            'carousel_enabled'      => array_key_exists('carousel_enabled', $data) ? (! empty($data['carousel_enabled']) ? 1 : 0) : (int) ($current['carousel_enabled'] ?? 0),
            'carousel_order'        => array_key_exists('carousel_order', $data) ? (! empty($data['carousel_order']) ? (int) $data['carousel_order'] : null) : ($current['carousel_order'] ?? null),
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
        $existing = $this->attendance->where('event_registration_id', $registrationId)->first();
        if ($existing) {
            $ok = (bool) $this->attendance->update($existing['id'], [
                'status'      => $status,
                'recorded_by' => $actorId,
                'recorded_at' => $now,
                'notes'       => $notes,
                'updated_at'  => $now,
            ]);
            if ($ok) {
                $this->audit->log($actorId, 'attendance.update', 'attendances', 'attendance', (int) $existing['id'], [
                    'registration_id' => $registrationId,
                    'status'          => $status,
                ]);
            }
            return $ok;
        }

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

    public function generateAttendanceCode(int $eventId, ?int $actorId = null): string
    {
        $event = $this->events->find($eventId);
        if (! $event) {
            throw new \RuntimeException('Event tidak ditemukan.');
        }

        // Format kode presensi: BGW-EVT-XXXXX (huruf kapital dan angka)
        $code = 'BGW-EVT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
        $codeHash = hash('sha256', $code);
        $now = date('Y-m-d H:i:s');

        $this->events->update($eventId, [
            'attendance_code'      => $code,
            'attendance_code_hash' => $codeHash,
            'updated_at'           => $now,
        ]);

        $this->audit->log($actorId, 'event.generate_attendance_code', 'events', 'event', $eventId, [
            'code' => $code,
        ]);

        return $code;
    }

    public function openAttendance(int $eventId, ?int $actorId = null): string
    {
        $event = $this->events->find($eventId);
        if (! $event) {
            throw new \RuntimeException('Event tidak ditemukan.');
        }

        $code = $event['attendance_code'] ?? null;
        $now = date('Y-m-d H:i:s');
        $payload = [
            'attendance_open_at'  => $now,
            'attendance_close_at' => null,
            'updated_at'          => $now,
        ];

        if (empty($code)) {
            $code = 'BGW-EVT-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
            $payload['attendance_code']      = $code;
            $payload['attendance_code_hash'] = hash('sha256', $code);
        }

        $this->events->update($eventId, $payload);

        $this->audit->log($actorId, 'event.open_attendance', 'events', 'event', $eventId, [
            'code'    => $code,
            'open_at' => $now,
        ]);

        return $code;
    }

    public function closeAttendance(int $eventId, ?int $actorId = null): bool
    {
        $event = $this->events->find($eventId);
        if (! $event) {
            throw new \RuntimeException('Event tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $ok = (bool) $this->events->update($eventId, [
            'attendance_close_at' => $now,
            'updated_at'          => $now,
        ]);

        if ($ok) {
            $this->audit->log($actorId, 'event.close_attendance', 'events', 'event', $eventId, [
                'close_at' => $now,
            ]);
        }

        return $ok;
    }

    public function validateAndCheckIn(string $rawCode, int $userId): array
    {
        // H-7: Rate-limiting anti brute-force presensi (5 attempts / 60 detik)
        $throttler = \Config\Services::throttler();
        $ip = service('request')->getIPAddress();
        $throttleKey = 'attendance_checkin_' . $userId . '_' . md5($ip);

        if (! $throttler->check($throttleKey, 5, 60)) {
            $this->audit->log(
                $userId,
                'attendance.rate_limited',
                'events',
                'event',
                null,
                ['ip' => $ip, 'user_id' => $userId],
                'Percobaan input kode presensi melebihi batas aman (rate limit).'
            );
            throw new \RuntimeException('Terlalu banyak percobaan kode presensi. Harap tunggu 1 menit sebelum mencoba kembali.');
        }

        $code = trim(strtoupper($rawCode));
        if ($code === '') {
            throw new \RuntimeException('Kode presensi tidak valid atau presensi belum dibuka.');
        }

        $db = db_connect();
        $codeHash = hash('sha256', $code);

        // 1. Kode cocok dengan event (utamakan attendance_code_hash SHA-256, fallback ke attendance_code dengan hash_equals)
        $event = $db->table('events')
            ->where('attendance_code_hash', $codeHash)
            ->where('deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $event) {
            $candidates = $db->table('events')
                ->where('attendance_code', $code)
                ->where('deleted_at IS NULL')
                ->get()->getResultArray();

            foreach ($candidates as $cand) {
                if (! empty($cand['attendance_code']) && hash_equals($cand['attendance_code'], $code)) {
                    $event = $cand;
                    if (empty($event['attendance_code_hash'])) {
                        $db->table('events')->where('id', $event['id'])->update(['attendance_code_hash' => $codeHash]);
                    }
                    break;
                }
            }
        }

        $now = time();
        $openAt = ! empty($event['attendance_open_at']) ? strtotime((string) $event['attendance_open_at']) : null;
        $closeAt = ! empty($event['attendance_close_at']) ? strtotime((string) $event['attendance_close_at']) : null;

        // H-7: Samakan pesan error ketika kode salah atau presensi belum dibuka agar tidak menjadi oracle
        if (! $event || $openAt === null || $openAt > $now) {
            throw new \RuntimeException('Kode presensi tidak valid atau presensi belum dibuka.');
        }

        // Presensi sudah ditutup
        if ($closeAt !== null && $closeAt <= $now) {
            throw new \RuntimeException('Sesi presensi untuk kegiatan ini telah ditutup.');
        }

        // 3. Peserta sudah terdaftar
        $reg = $db->table('event_registrations')
            ->where('event_id', (int) $event['id'])
            ->where('user_id', $userId)
            ->whereIn('status', ['registered', 'confirmed'])
            ->get()->getRowArray();

        if (! $reg) {
            throw new \RuntimeException('Anda belum terdaftar pada event ini.');
        }

        $registrationId = (int) $reg['id'];

        // 4. Peserta belum melakukan presensi sebelumnya
        $existingAttendance = $db->table('attendances')
            ->where('event_registration_id', $registrationId)
            ->get()->getRowArray();

        if ($existingAttendance && in_array(strtolower($existingAttendance['status']), ['present', 'hadir'], true)) {
            throw new \RuntimeException('Anda sudah melakukan presensi sebelumnya.');
        }

        $nowTime = date('Y-m-d H:i:s');
        $db->transStart();

        if ($existingAttendance) {
            $db->table('attendances')->where('id', $existingAttendance['id'])->update([
                'status'      => 'present',
                'recorded_by' => $userId,
                'recorded_at' => $nowTime,
                'notes'       => 'Presensi mandiri melalui kode sandi: ' . $code,
                'updated_at'  => $nowTime,
            ]);
            $attendanceId = (int) $existingAttendance['id'];
        } else {
            $db->table('attendances')->insert([
                'event_registration_id' => $registrationId,
                'status'                => 'present',
                'recorded_by'           => $userId,
                'recorded_at'           => $nowTime,
                'notes'                 => 'Presensi mandiri melalui kode sandi: ' . $code,
                'created_at'            => $nowTime,
                'updated_at'            => $nowTime,
            ]);
            $attendanceId = (int) $db->insertID();
        }

        $this->audit->log($userId, 'attendance.checkin_code', 'attendances', 'attendance', $attendanceId, [
            'event_id'        => (int) $event['id'],
            'registration_id' => $registrationId,
            'code'            => $code,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            throw new \RuntimeException('Gagal menyimpan pencatatan presensi ke database.');
        }

        return [
            'event'           => $event,
            'registration'    => $reg,
            'attendance_id'   => $attendanceId,
            'message'         => 'Presensi berhasil dicatat! Kehadiran Anda pada event "' . $event['title'] . '" telah terkonfirmasi.',
        ];
    }

    public function batchGenerateEventCertificates(int $eventId, ?int $actorId = null): array
    {
        $db = db_connect();
        $event = $db->table('events')->where('id', $eventId)->get()->getRowArray();
        if (! $event) {
            throw new \RuntimeException('Event tidak ditemukan.');
        }

        // Ambil peserta: event_registrations JOIN attendances DENGAN kondisi attendance.status = 'present'
        $participants = $db->table('event_registrations er')
            ->select('er.id AS registration_id, er.user_id, u.name AS user_name, u.email AS user_email, a.status AS attendance_status')
            ->join('attendances a', 'a.event_registration_id = er.id')
            ->join('users u', 'u.id = er.user_id')
            ->where('er.event_id', $eventId)
            ->where('er.status', 'registered')
            ->where('a.status', 'present')
            ->orderBy('er.id', 'ASC')
            ->get()->getResultArray();

        $generated = [];
        $now = date('Y-m-d H:i:s');

        foreach ($participants as $p) {
            $regId  = (int) $p['registration_id'];
            $userId = (int) $p['user_id'];

            // Cek apakah sertifikat sudah ada
            $existing = $db->table('certificates')
                ->where('source_type', 'event_registration')
                ->where('source_id', $regId)
                ->get()->getRowArray();

            if ($existing) {
                continue;
            }

            $certNumber = 'CERT-EVT-' . date('Ymd') . '-' . sprintf('%04d', $regId);
            $tokenHash  = hash('sha256', bin2hex(random_bytes(16)) . $regId . $now);

            $db->table('certificates')->insert([
                'source_type'             => 'event_registration',
                'source_id'               => $regId,
                'user_id'                 => $userId,
                'certificate_number'      => $certNumber,
                'verification_token_hash' => $tokenHash,
                'status'                  => 'issued',
                'issued_at'               => $now,
                'issued_by'               => $actorId,
                'created_at'              => $now,
                'updated_at'              => $now,
            ]);

            $certId = (int) $db->insertID();
            $generated[] = [
                'certificate_id'     => $certId,
                'certificate_number' => $certNumber,
                'registration_id'    => $regId,
                'user_id'            => $userId,
                'user_name'          => $p['user_name'],
            ];

            $this->audit->log($actorId, 'certificate.event_batch_issue', 'certificates', 'certificate', $certId, [
                'event_id'           => $eventId,
                'registration_id'    => $regId,
                'certificate_number' => $certNumber,
            ]);
        }

        return $generated;
    }

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