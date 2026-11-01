<?php

namespace App\Services;

class EventRundownService
{
    protected $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    /**
     * Mengambil daftar susunan acara (rundown) event berdasarkan event ID.
     * Terurut otomatis berdasarkan jam mulai (start_time).
     *
     * @param int $eventId
     * @param array|null $eventData Optional event row for synthetic fallback
     * @return array List of rundown items: [['start_time', 'end_time', 'title', 'description', 'speaker'], ...]
     */
    public function getRundown(int $eventId, ?array $eventData = null): array
    {
        $items = [];

        // 1. Cek dari tabel file_assets (resource_type = 'event_rundown')
        if ($this->db && $this->db->tableExists('file_assets')) {
            $row = $this->db->table('file_assets')
                ->where('resource_type', 'event_rundown')
                ->where('resource_id', $eventId)
                ->where('deleted_at IS NULL')
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();

            if ($row && ! empty($row['metadata'])) {
                $parsed = is_string($row['metadata']) ? json_decode($row['metadata'], true) : $row['metadata'];
                if (is_array($parsed) && ! empty($parsed)) {
                    $items = $this->cleanAndSortRundown($parsed);
                }
            }
        }

        // 2. Fallback ke filesystem JSON di WRITEPATH
        if (empty($items)) {
            $filePath = WRITEPATH . 'uploads/events/rundowns/rundown_' . $eventId . '.json';
            if (is_file($filePath)) {
                $raw = @file_get_contents($filePath);
                if ($raw !== false) {
                    $parsed = json_decode($raw, true);
                    if (is_array($parsed) && ! empty($parsed)) {
                        $items = $this->cleanAndSortRundown($parsed);
                    }
                }
            }
        }

        // 3. Fallback jika belum pernah diinput: bentuk default berdasarkan jam start_at dan end_at event
        if (empty($items) && $eventData) {
            $items = $this->generateDefaultRundown($eventData);
        }

        return $items;
    }

    /**
     * Menyimpan susunan acara (rundown) event ke file_assets & file JSON.
     * Mengurutkan item secara otomatis berdasarkan jam (start_time).
     *
     * @param int $eventId
     * @param array $rawItems
     * @param int|null $actorId
     * @return bool
     */
    public function saveRundown(int $eventId, array $rawItems, ?int $actorId = null): bool
    {
        $cleanItems = $this->cleanAndSortRundown($rawItems);
        $jsonString = json_encode($cleanItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 1. Simpan ke berkas sistem di WRITEPATH
        $dir = WRITEPATH . 'uploads/events/rundowns';
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $fileName = 'rundown_' . $eventId . '.json';
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        @file_put_contents($filePath, $jsonString);

        // 2. Simpan/Update di tabel file_assets (Zero Migration)
        if ($this->db && $this->db->tableExists('file_assets')) {
            $existing = $this->db->table('file_assets')
                ->where('resource_type', 'event_rundown')
                ->where('resource_id', $eventId)
                ->where('deleted_at IS NULL')
                ->get()->getRowArray();

            $now = date('Y-m-d H:i:s');
            $data = [
                'disk'             => 'local',
                'path'             => 'uploads/events/rundowns/' . $fileName,
                'original_name'    => $fileName,
                'stored_name'      => $fileName,
                'extension'        => 'json',
                'mime_type'        => 'application/json',
                'size_bytes'       => strlen($jsonString),
                'visibility'       => 'public',
                'lifecycle_status' => 'available',
                'resource_type'    => 'event_rundown',
                'resource_id'      => $eventId,
                'uploaded_by'      => $actorId ?: session()->get('user_id'),
                'metadata'         => $jsonString,
                'updated_at'       => $now,
            ];

            if ($existing) {
                $this->db->table('file_assets')->where('id', $existing['id'])->update($data);
            } else {
                $data['created_at'] = $now;
                $this->db->table('file_assets')->insert($data);
            }
        }

        return true;
    }

    /**
     * Membersihkan dan mengurutkan rundown berdasarkan jam (start_time ASC).
     */
    public function cleanAndSortRundown(array $items): array
    {
        $clean = [];

        foreach ($items as $it) {
            if (! is_array($it)) {
                continue;
            }

            $title = trim((string) ($it['title'] ?? $it['session_name'] ?? ''));
            $startTime = trim((string) ($it['start_time'] ?? ''));
            $endTime = trim((string) ($it['end_time'] ?? ''));
            $description = trim((string) ($it['description'] ?? ''));
            $speaker = trim((string) ($it['speaker'] ?? $it['facilitator'] ?? ''));

            if ($title === '' && $startTime === '') {
                continue;
            }

            // Standarisasi format jam HH:mm
            if (preg_match('/^(\d{1,2}):(\d{2})/', $startTime, $m)) {
                $startTime = sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
            }
            if (preg_match('/^(\d{1,2}):(\d{2})/', $endTime, $m)) {
                $endTime = sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
            }

            $clean[] = [
                'start_time'  => $startTime,
                'end_time'    => $endTime,
                'title'       => $title,
                'description' => $description,
                'speaker'     => $speaker,
            ];
        }

        // Urutkan berbasis jam (start_time ASC)
        usort($clean, static function ($a, $b) {
            $tA = $a['start_time'] ?: '99:99';
            $tB = $b['start_time'] ?: '99:99';
            return strcmp($tA, $tB);
        });

        return $clean;
    }

    /**
     * Membentuk rundown default berbasis timestamp start_at dan end_at event.
     */
    public function generateDefaultRundown(array $event): array
    {
        $start = ! empty($event['start_at']) ? strtotime($event['start_at']) : time();
        $end   = ! empty($event['end_at']) ? strtotime($event['end_at']) : $start + 7200;

        return [
            [
                'start_time'  => date('H:i', $start),
                'end_time'    => date('H:i', $start + 1800),
                'title'       => 'Pembukaan Acara & Registrasi Peserta',
                'description' => 'Verifikasi identitas dan penyambutan peserta',
                'speaker'     => 'Panitia Biro PBJ Setda Lampung',
            ],
            [
                'start_time'  => date('H:i', $start + 1800),
                'end_time'    => date('H:i', max($start + 3600, $end - 2700)),
                'title'       => 'Pemaparan Materi Pokok & Pembahasan Tematik',
                'description' => (string) ($event['title'] ?? 'Pemaparan Materi Utama'),
                'speaker'     => 'Narasumber & Tim Fasilitator Ahli PBJ',
            ],
            [
                'start_time'  => date('H:i', max($start + 3600, $end - 2700)),
                'end_time'    => date('H:i', max($start + 5400, $end - 900)),
                'title'       => 'Sesi Tanya Jawab, Diskusi & Evaluasi Kasus',
                'description' => 'Konsultasi interaktif langsung bersama narasumber/fasilitator',
                'speaker'     => 'Moderator & Seluruh Peserta',
            ],
            [
                'start_time'  => date('H:i', max($start + 5400, $end - 900)),
                'end_time'    => date('H:i', $end),
                'title'       => 'Rangkuman Hasil, Penutupan & Validasi Presensi',
                'description' => 'Perekaman rekam jejak kehadiran dan penerbitan sertifikat',
                'speaker'     => 'Penyelenggara Resmi BegawiPengadaan',
            ],
        ];
    }
}
