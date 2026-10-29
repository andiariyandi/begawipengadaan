<?php

namespace App\Services\RupAudit;

class RupAccessHelper
{
    /**
     * Memeriksa apakah pengguna saat ini berwenang mengakses modul Audit RUP Otomatis.
     *
     * Kriteria Izin:
     * 1. Super Administrator atau Administrator sistem.
     * 2. ASN Pemprov Lampung unit kerja Biro Pengadaan Barang dan Jasa.
     * 3. ASN Pemprov Lampung unit kerja Inspektorat Daerah.
     */
    public static function canAccess(?int $userId = null, ?array $roles = null): bool
    {
        $session = session();
        $uid = $userId ?? (int) ($session->get('user_id') ?: $session->get('id') ?: 0);
        if ($uid <= 0) {
            return false;
        }

        $userRoles = $roles ?? (array) ($session->get('roles') ?: []);
        $normalizedRoles = array_map(static fn($r) => strtolower(trim((string) $r)), $userRoles);

        // 1. Super Admin & Admin selalu diizinkan
        if (in_array('super_administrator', $normalizedRoles, true) || in_array('administrator', $normalizedRoles, true)) {
            return true;
        }

        // 2. Periksa profil ASN di database
        $db = \Config\Database::connect();
        $asn = $db->table('asn_profiles')->where('user_id', $uid)->get()->getRowArray();
        if (! $asn) {
            return false;
        }

        $origin = strtolower(trim((string) ($asn['origin_type'] ?? '')));
        // Pastikan ASN Pemprov Lampung
        if ($origin !== 'pemprov_lampung') {
            return false;
        }

        $unitName = strtolower(trim((string) ($asn['unit_name'] ?? '')));
        $instName = strtolower(trim((string) ($asn['institution_name'] ?? '')));

        // Biro PBJ check
        $isBiroPbj = str_contains($unitName, 'pengadaan barang dan jasa') ||
                     str_contains($unitName, 'biro pbj') ||
                     str_contains($unitName, 'ukpbj');

        // Inspektorat check
        $isInspektorat = str_contains($unitName, 'inspektorat') ||
                         str_contains($unitName, 'inspektur');

        if ($isBiroPbj || $isInspektorat) {
            return true;
        }

        // Opsional: cek via organization_units jika ada id
        if (! empty($asn['organization_unit_id'])) {
            $opd = $db->table('organization_units')->where('id', (int) $asn['organization_unit_id'])->get()->getRowArray();
            if ($opd) {
                $opdName = strtolower(trim((string) ($opd['name'] ?? '')));
                if (str_contains($opdName, 'pengadaan') || str_contains($opdName, 'inspektorat')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Mengembalikan alasan penolakan yang ramah pengguna.
     */
    public static function getAccessDeniedMessage(): string
    {
        return 'Akses Terbatas: Modul Audit RUP Otomatis khusus diperuntukkan bagi Administrator, APIP (Inspektorat), dan Pengelola PBJ (Biro Pengadaan Barang dan Jasa) Pemerintah Provinsi Lampung.';
    }
}
