<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ProfileOptions extends BaseConfig
{
    public array $profileTypes = [
        'asn' => 'ASN / Pengelola PBJ / Pejabat Terkait',
        'provider' => 'Penyedia Barang/Jasa',
        'academic_practitioner' => 'Akademisi / Praktisi',
        'public' => 'Masyarakat Umum',
    ];

    public array $asnStatuses = ['PNS', 'PPPK'];
    public array $identityTypes = ['NIP', 'NI PPPK'];
    public array $pbjRoles = ['PPK', 'Pejabat Pengadaan', 'Pokja Pemilihan', 'Tim Pendukung', 'Lainnya'];
    public array $positions = ['Kepala Dinas', 'Kepala Biro', 'Kepala Bagian', 'Kepala Bidang', 'Pejabat Fungsional', 'Pelaksana', 'Lainnya'];
    public array $grades = ['I/a', 'I/b', 'II/a', 'II/b', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'];
    public array $expertise = ['Pengadaan Barang/Jasa', 'Hukum', 'Manajemen', 'Keuangan', 'Teknologi Informasi', 'Kebijakan Publik', 'Lainnya'];
    public array $provinces = ['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah', 'Jawa Timur', 'Banten', 'Sumatera Utara', 'Sulawesi Selatan', 'Lainnya'];
    public array $regencies = ['Jakarta Pusat', 'Bandung', 'Bekasi', 'Bogor', 'Semarang', 'Surabaya', 'Tangerang', 'Medan', 'Makassar', 'Lainnya'];
    public array $institutions = ['Pemerintah Provinsi', 'Pemerintah Kabupaten/Kota', 'Kementerian/Lembaga', 'Universitas / Perguruan Tinggi', 'Organisasi / Lembaga', 'Lainnya'];
    public array $workUnits = ['Sekretariat Daerah', 'Dinas', 'Biro', 'Bagian', 'Bidang', 'Unit Pelaksana Teknis', 'Lainnya'];
}
