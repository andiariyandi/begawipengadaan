<?php

namespace App\Services\Communication;

/**
 * Pemetaan status registrasi event (6 status kanonik) ke struktur database yang ada.
 *
 * Kolom `event_registrations.status` dibatasi CHECK constraint hanya:
 *   registered | waitlisted | cancelled | rejected
 * dan `attendances.status` hanya: present | absent | excused.
 * Karena skema tidak boleh diubah, 6 status bisnis diturunkan dari kombinasi kolom:
 *
 *  | Status Kanonik            | event_registrations                  | attendances |
 *  |---------------------------|--------------------------------------|-------------|
 *  | Waitlist (Antrian)        | status = waitlisted                  | -           |
 *  | Terdaftar (Confirmed)     | status = registered, promoted_at NULL| -           |
 *  | Promoted (Dari Waitlist)  | status = registered, promoted_at SET | -           |
 *  | Hadir (Attended)          | status = registered                  | present     |
 *  | Tidak Hadir (No-Show)     | status = rejected (ditolak)          | -           |
 *  |                           | atau status = registered             | absent      |
 *  | Dibatalkan                | status = cancelled                   | -           |
 */
final class EventRegistrationStatus
{
    public const WAITLIST  = 'waitlist';
    public const CONFIRMED = 'confirmed';
    public const PROMOTED  = 'promoted';
    public const ATTENDED  = 'attended';
    public const NO_SHOW   = 'no_show';
    public const CANCELLED = 'cancelled';

    /** Nilai mentah DB yang memberi akses ruang event (sudah disetujui). Nilai legacy tetap didukung. */
    public const ACCESS_RAW = ['registered', 'confirmed', 'promoted', 'attended', 'present', 'completed'];

    /** Nilai mentah DB yang berarti masih antre / menunggu persetujuan. */
    public const PENDING_RAW = ['waitlisted', 'waitlist', 'waiting', 'pending'];

    /** Nilai mentah DB yang berarti ditolak. */
    public const REJECTED_RAW = ['rejected', 'no_show'];

    public static function all(): array
    {
        return [self::WAITLIST, self::CONFIRMED, self::PROMOTED, self::ATTENDED, self::NO_SHOW, self::CANCELLED];
    }

    /**
     * Turunkan status kanonik dari kolom registrasi + kehadiran.
     */
    public static function resolve(?string $raw, ?string $attendance = null, ?string $promotedAt = null): string
    {
        $raw = strtolower(trim((string) $raw));
        $att = strtolower(trim((string) $attendance));

        if ($raw === 'cancelled') {
            return self::CANCELLED;
        }
        if (in_array($raw, self::REJECTED_RAW, true)) {
            return self::NO_SHOW;
        }
        if (in_array($raw, self::PENDING_RAW, true) || $raw === '') {
            return self::WAITLIST;
        }
        if (in_array($raw, self::ACCESS_RAW, true)) {
            if (in_array($att, ['present', 'hadir'], true) || in_array($raw, ['attended', 'present', 'completed'], true)) {
                return self::ATTENDED;
            }
            if ($att === 'absent') {
                return self::NO_SHOW;
            }
            if ($raw === 'promoted' || ! empty($promotedAt)) {
                return self::PROMOTED;
            }
            return self::CONFIRMED;
        }

        return self::WAITLIST;
    }

    /** Resolve dari baris array (kunci status, attendance_status, promoted_at). */
    public static function fromRow(array $row, string $statusKey = 'status', string $attKey = 'attendance_status'): string
    {
        return self::resolve($row[$statusKey] ?? null, $row[$attKey] ?? null, $row['promoted_at'] ?? null);
    }

    /** Apakah peserta boleh mengakses ruang event (materi, presensi, kuis, sertifikat). */
    public static function hasAccess(?string $raw): bool
    {
        return in_array(strtolower(trim((string) $raw)), self::ACCESS_RAW, true);
    }

    public static function isPending(?string $raw): bool
    {
        $raw = strtolower(trim((string) $raw));
        return $raw === '' || in_array($raw, self::PENDING_RAW, true);
    }

    public static function label(string $key): string
    {
        return match ($key) {
            self::CONFIRMED => 'Terdaftar (Confirmed)',
            self::PROMOTED  => 'Promoted (Dari Waitlist)',
            self::ATTENDED  => 'Hadir (Attended)',
            self::NO_SHOW   => 'Tidak Hadir (No-Show)',
            self::CANCELLED => 'Dibatalkan',
            default         => 'Waitlist (Antrian)',
        };
    }

    public static function shortLabel(string $key): string
    {
        return match ($key) {
            self::CONFIRMED => 'Terdaftar',
            self::PROMOTED  => 'Promoted',
            self::ATTENDED  => 'Hadir',
            self::NO_SHOW   => 'Tidak Hadir',
            self::CANCELLED => 'Dibatalkan',
            default         => 'Waitlist',
        };
    }

    /** Kelas badge Bootstrap 5 (workspace). */
    public static function badgeClass(string $key): string
    {
        return match ($key) {
            self::CONFIRMED => 'bg-primary-subtle text-primary border border-primary-subtle',
            self::PROMOTED  => 'bg-info-subtle text-info-emphasis border border-info-subtle',
            self::ATTENDED  => 'bg-success-subtle text-success border border-success-subtle',
            self::NO_SHOW   => 'bg-danger-subtle text-danger border border-danger-subtle',
            self::CANCELLED => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
            default         => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        };
    }

    /** Kelas badge tema dashboard admin (WowDash). */
    public static function dashboardBadgeClass(string $key): string
    {
        return match ($key) {
            self::CONFIRMED => 'bg-primary-50 text-primary-700 border border-primary-200',
            self::PROMOTED  => 'bg-info-50 text-info-700 border border-info-200',
            self::ATTENDED  => 'bg-success-50 text-success-700 border border-success-200',
            self::NO_SHOW   => 'bg-danger-50 text-danger-700 border border-danger-200',
            self::CANCELLED => 'bg-neutral-100 text-neutral-700 border border-neutral-300',
            default         => 'bg-warning-50 text-warning-700 border border-warning-200',
        };
    }

    public static function icon(string $key): string
    {
        return match ($key) {
            self::CONFIRMED => 'fas fa-user-check',
            self::PROMOTED  => 'fas fa-level-up-alt',
            self::ATTENDED  => 'fas fa-check-double',
            self::NO_SHOW   => 'fas fa-user-times',
            self::CANCELLED => 'fas fa-ban',
            default         => 'fas fa-hourglass-half',
        };
    }

    /** Opsi dropdown untuk petugas. */
    public static function options(): array
    {
        $out = [];
        foreach (self::all() as $k) {
            $out[$k] = self::label($k);
        }
        return $out;
    }

    /** Normalisasi input status lama/legacy menjadi kunci kanonik. */
    public static function normalizeInput(string $input): ?string
    {
        $input = strtolower(trim($input));
        return match ($input) {
            'waitlist', 'waitlisted', 'waiting', 'pending' => self::WAITLIST,
            'confirmed', 'registered', 'approve', 'approved' => self::CONFIRMED,
            'promoted', 'promote' => self::PROMOTED,
            'attended', 'present', 'hadir' => self::ATTENDED,
            'no_show', 'noshow', 'absent', 'rejected', 'reject' => self::NO_SHOW,
            'cancelled', 'canceled', 'cancel' => self::CANCELLED,
            default => null,
        };
    }

    /**
     * Terapkan filter status kanonik pada query builder.
     * Membutuhkan alias registrasi ($er) dan LEFT JOIN attendances ($a).
     */
    public static function applyFilter($builder, string $key, string $er = 'er', string $a = 'a'): void
    {
        $access = "'" . implode("','", self::ACCESS_RAW) . "'";
        $pending = "'" . implode("','", self::PENDING_RAW) . "'";
        $rejected = "'" . implode("','", self::REJECTED_RAW) . "'";

        $sql = match ($key) {
            self::WAITLIST  => "{$er}.status IN ({$pending})",
            self::CONFIRMED => "{$er}.status IN ({$access}) AND {$er}.status NOT IN ('attended','present','completed','promoted') AND {$er}.promoted_at IS NULL AND ({$a}.status IS NULL OR {$a}.status = 'excused')",
            self::PROMOTED  => "{$er}.status IN ({$access}) AND {$er}.status NOT IN ('attended','present','completed') AND ({$er}.promoted_at IS NOT NULL OR {$er}.status = 'promoted') AND ({$a}.status IS NULL OR {$a}.status = 'excused')",
            self::ATTENDED  => "({$er}.status IN ({$access}) AND ({$a}.status = 'present' OR {$er}.status IN ('attended','present','completed')))",
            self::NO_SHOW   => "({$er}.status IN ({$rejected}) OR ({$er}.status IN ({$access}) AND {$a}.status = 'absent'))",
            self::CANCELLED => "{$er}.status = 'cancelled'",
            default         => null,
        };

        if ($sql !== null) {
            $builder->where('(' . $sql . ')', null, false);
        }
    }
}
