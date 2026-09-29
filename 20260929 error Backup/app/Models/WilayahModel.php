<?php

namespace App\Models;

use CodeIgniter\Model;

class WilayahModel extends Model
{
    protected $table = 'wilayah';
    protected $primaryKey = 'kode';
    protected $returnType = 'array';
    protected $allowedFields = ['kode', 'nama'];

    public function children(string $parentCode): array
    {
        $depth = substr_count($parentCode, '.') + 1;
        $prefix = $parentCode . '.';

        return $this->select('kode, nama')
            ->like('kode', $prefix, 'after')
            ->where("LENGTH(kode) - LENGTH(REPLACE(kode, '.', '')) = {$depth}", null, false)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    public function provinces(): array
    {
        return $this->select('kode, nama')
            ->where("LENGTH(kode) - LENGTH(REPLACE(kode, '.', '')) = 0", null, false)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }

    public function isValidHierarchy(string $province, string $regency, string $district, ?string $village = null): bool
    {
        if (! str_starts_with($regency, $province . '.') || ! str_starts_with($district, $regency . '.')) {
            return false;
        }

        if ($village !== null && $village !== '' && ! str_starts_with($village, $district . '.')) {
            return false;
        }

        $codes = array_filter([$province, $regency, $district, $village]);
        return count($codes) === $this->whereIn('kode', $codes)->countAllResults();
    }
}
