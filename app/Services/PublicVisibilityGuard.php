<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;

/**
 * PublicVisibilityGuard
 *
 * Standarisasi guard visibilitas dan kriteria tayang publik untuk konten ekosistem BegawiPengadaan.
 * Mengunci aturan filter deleted_at, visibility, dan status di satu tempat terpusat
 * agar tidak terjadi kebocoran draft / internal data ke pengunjung publik.
 */
class PublicVisibilityGuard
{
    /**
     * Terapkan filter visibilitas event publik.
     * Mengunci: deleted_at IS NULL, visibility = 'public', dan status IN ('coming_soon', 'open_registration', 'full', 'finished')
     * (atau tanpa 'finished' jika $includeFinished = false).
     *
     * Catatan: Tabel events tidak memiliki workflow_status atau is_active.
     */
    public static function applyPublicEvents(object $builder, string $alias = 'e', bool $includeFinished = true): object
    {
        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
        $builder->where($prefix . 'deleted_at', null);
        $builder->where($prefix . 'visibility', 'public');

        $statuses = $includeFinished
            ? ['coming_soon', 'open_registration', 'full', 'finished']
            : ['coming_soon', 'open_registration', 'full'];

        $builder->whereIn($prefix . 'status', $statuses);

        return $builder;
    }

    /**
     * Terapkan filter visibilitas kursus / pembelajaran publik.
     * Mengunci: deleted_at IS NULL, status = 'published', dan visibility = 'public'.
     *
     * Catatan: Tabel courses tidak memiliki workflow_status atau is_active.
     */
    public static function applyPublicCourses(object $builder, string $alias = 'c'): object
    {
        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
        $builder->where($prefix . 'deleted_at', null);
        $builder->where($prefix . 'visibility', 'public');
        $builder->where($prefix . 'status', 'published');

        return $builder;
    }

    /**
     * Terapkan filter visibilitas workflow umum (artikel, regulasi, berita, dsb).
     * Mengunci: deleted_at IS NULL, visibility = 'public', dan workflow_status = 'published'.
     */
    public static function applyPublicWorkflow(object $builder, string $alias = 'w'): object
    {
        $prefix = $alias !== '' ? rtrim($alias, '.') . '.' : '';
        $builder->where($prefix . 'deleted_at', null);
        $builder->where($prefix . 'visibility', 'public');
        $builder->where($prefix . 'workflow_status', 'published');

        return $builder;
    }
}
