<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class OpdRoleSeed extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'opd:seed';
    protected $description = 'Seed canonical OPD Pemprov Lampung and Procurement Roles';

    public function run(array $params): int
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        CLI::write("=== SEEDING OPD PEMPROV LAMPUNG ===", "yellow");

        $opds = [
            ['code' => 'BPPBJ',       'name' => 'Biro Pengadaan Barang dan Jasa',                        'type' => 'Biro',        'sort_order' => 1],
            ['code' => 'BANGDA',      'name' => 'Biro Administrasi Pembangunan',                         'type' => 'Biro',        'sort_order' => 2],
            ['code' => 'BIRO-HKM',    'name' => 'Biro Hukum',                                            'type' => 'Biro',        'sort_order' => 3],
            ['code' => 'BIRO-ORG',    'name' => 'Biro Organisasi',                                       'type' => 'Biro',        'sort_order' => 4],
            ['code' => 'BIRO-EKO',    'name' => 'Biro Perekonomian',                                     'type' => 'Biro',        'sort_order' => 5],
            ['code' => 'BIRO-KESRA',  'name' => 'Biro Kesejahteraan Rakyat',                             'type' => 'Biro',        'sort_order' => 6],
            ['code' => 'BIRO-ADPIM',  'name' => 'Biro Administrasi Pimpinan',                            'type' => 'Biro',        'sort_order' => 7],
            ['code' => 'BIRO-UMUM',   'name' => 'Biro Umum',                                             'type' => 'Biro',        'sort_order' => 8],
            ['code' => 'BIRO-PEMOTDA','name' => 'Biro Pemerintahan dan Otonomi Daerah',                  'type' => 'Biro',        'sort_order' => 9],
            ['code' => 'BMBK',        'name' => 'Dinas Bina Marga dan Bina Konstruksi',                  'type' => 'Dinas',       'sort_order' => 10],
            ['code' => 'PSDA',        'name' => 'Dinas Pengelolaan Sumber Daya Air',                     'type' => 'Dinas',       'sort_order' => 11],
            ['code' => 'PKPCK',       'name' => 'Dinas Perumahan, Kawasan Permukiman dan Cipta Karya',   'type' => 'Dinas',       'sort_order' => 12],
            ['code' => 'DISDIKBUD',   'name' => 'Dinas Pendidikan dan Kebudayaan',                       'type' => 'Dinas',       'sort_order' => 13],
            ['code' => 'DINKES',      'name' => 'Dinas Kesehatan',                                       'type' => 'Dinas',       'sort_order' => 14],
            ['code' => 'DINSOS',      'name' => 'Dinas Sosial',                                          'type' => 'Dinas',       'sort_order' => 15],
            ['code' => 'DISNAKER',    'name' => 'Dinas Tenaga Kerja',                                    'type' => 'Dinas',       'sort_order' => 16],
            ['code' => 'PPPA',        'name' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',    'type' => 'Dinas',       'sort_order' => 17],
            ['code' => 'KPTPH',       'name' => 'Dinas Ketahanan Pangan, Tanaman Pangan dan Hortikultura','type' => 'Dinas',      'sort_order' => 18],
            ['code' => 'DISBUN',      'name' => 'Dinas Perkebunan',                                      'type' => 'Dinas',       'sort_order' => 19],
            ['code' => 'DISNAKKESWAN','name' => 'Dinas Peternakan dan Kesehatan Hewan',                 'type' => 'Dinas',       'sort_order' => 20],
            ['code' => 'DKP',         'name' => 'Dinas Kelautan dan Perikanan',                          'type' => 'Dinas',       'sort_order' => 21],
            ['code' => 'DISPAREKRAF', 'name' => 'Dinas Pariwisata dan Ekonomi Kreatif',                  'type' => 'Dinas',       'sort_order' => 22],
            ['code' => 'DISHUB',      'name' => 'Dinas Perhubungan',                                     'type' => 'Dinas',       'sort_order' => 23],
            ['code' => 'DISKOMINFOTIK','name' => 'Dinas Komunikasi, Informatika dan Statistik',          'type' => 'Dinas',       'sort_order' => 24],
            ['code' => 'DISKOPUKM',   'name' => 'Dinas Koperasi, Usaha Kecil dan Menengah',              'type' => 'Dinas',       'sort_order' => 25],
            ['code' => 'DPMPTSP',     'name' => 'Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu','type' => 'Dinas',      'sort_order' => 26],
            ['code' => 'DISPORA',     'name' => 'Dinas Pemuda dan Olahraga',                             'type' => 'Dinas',       'sort_order' => 27],
            ['code' => 'DISPUSIP',    'name' => 'Dinas Perpustakaan dan Kearsipan',                      'type' => 'Dinas',       'sort_order' => 28],
            ['code' => 'DLH',         'name' => 'Dinas Lingkungan Hidup',                                'type' => 'Dinas',       'sort_order' => 29],
            ['code' => 'DISHUT',      'name' => 'Dinas Kehutanan',                                       'type' => 'Dinas',       'sort_order' => 30],
            ['code' => 'ESDM',        'name' => 'Dinas Energi dan Sumber Daya Mineral',                  'type' => 'Dinas',       'sort_order' => 31],
            ['code' => 'DISPERINDAG', 'name' => 'Dinas Perindustrian dan Perdagangan',                   'type' => 'Dinas',       'sort_order' => 32],
            ['code' => 'DISDUKCAPIL', 'name' => 'Dinas Kependudukan dan Pencatatan Sipil',              'type' => 'Dinas',       'sort_order' => 33],
            ['code' => 'SATPOLPP',    'name' => 'Satuan Polisi Pamong Praja',                            'type' => 'Dinas',       'sort_order' => 34],
            ['code' => 'BAPPEDA',     'name' => 'Badan Perencanaan Pembangunan Daerah',                  'type' => 'Badan',       'sort_order' => 35],
            ['code' => 'BAPENDA',     'name' => 'Badan Pendapatan Daerah',                              'type' => 'Badan',       'sort_order' => 36],
            ['code' => 'BPKAD',       'name' => 'Badan Pengelola Keuangan dan Aset Daerah',              'type' => 'Badan',       'sort_order' => 37],
            ['code' => 'BKD',         'name' => 'Badan Kepegawaian Daerah',                              'type' => 'Badan',       'sort_order' => 38],
            ['code' => 'BPSDM',       'name' => 'Badan Pengembangan Sumber Daya Manusia',                'type' => 'Badan',       'sort_order' => 39],
            ['code' => 'BPBD',        'name' => 'Badan Penanggulangan Bencana Daerah',                  'type' => 'Badan',       'sort_order' => 40],
            ['code' => 'BAKESBANGPOL','name' => 'Badan Kesatuan Bangsa dan Politik',                     'type' => 'Badan',       'sort_order' => 41],
            ['code' => 'INSPEKTORAT', 'name' => 'Inspektorat Daerah Provinsi Lampung',                    'type' => 'Inspektorat', 'sort_order' => 42],
            ['code' => 'RSUDAM',      'name' => 'RSUD Dr. H. Abdul Moeloek',                             'type' => 'Rumah Sakit', 'sort_order' => 43],
            ['code' => 'RSJD',        'name' => 'Rumah Sakit Jiwa Daerah Provinsi Lampung',              'type' => 'Rumah Sakit', 'sort_order' => 44],
            ['code' => 'RSUDBNH',     'name' => 'RSUD Bandar Negara Husada',                             'type' => 'Rumah Sakit', 'sort_order' => 45],
            ['code' => 'SETWAN',      'name' => 'Sekretariat DPRD Provinsi Lampung',                     'type' => 'Sekretariat', 'sort_order' => 46],
        ];

        $opdTable = $db->table('organization_units');
        $opdInserted = 0;
        $opdUpdated = 0;

        foreach ($opds as $opd) {
            $existing = $opdTable->where('code', $opd['code'])->get()->getRowArray();
            if ($existing) {
                $opdTable->where('id', $existing['id'])->update([
                    'name'       => $opd['name'],
                    'type'       => $opd['type'],
                    'is_active'  => 1,
                    'sort_order' => $opd['sort_order'],
                    'updated_at' => $now,
                ]);
                $opdUpdated++;
            } else {
                $opdTable->insert([
                    'code'       => $opd['code'],
                    'name'       => $opd['name'],
                    'type'       => $opd['type'],
                    'is_active'  => 1,
                    'sort_order' => $opd['sort_order'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                $opdInserted++;
            }
        }
        CLI::write("OPD Seeding completed: {$opdInserted} inserted, {$opdUpdated} updated.", "green");

        CLI::write("\n=== SEEDING CANONICAL PROCUREMENT ROLES ===", "yellow");

        // Deactivate any leftover TEST roles (respecting FK constraint and soft delete principle)
        $db->table('procurement_roles')->like('key', 'TEST')->update(['is_active' => 0]);

        $roles = [
            [
                'key'         => 'PA',
                'name'        => 'Pengguna Anggaran (PA)',
                'description' => 'Pejabat pemegang kewenangan penggunaan anggaran Kementerian Negara/Lembaga/Perangkat Daerah.',
            ],
            [
                'key'         => 'KPA',
                'name'        => 'Kuasa Pengguna Anggaran (KPA)',
                'description' => 'Pejabat yang memperoleh kuasa dari PA untuk melaksanakan sebagian kewenangan dan tanggung jawab penggunaan anggaran.',
            ],
            [
                'key'         => 'PPK',
                'name'        => 'Pejabat Pembuat Komitmen (PPK)',
                'description' => 'Pejabat yang diberi kewenangan oleh PA/KPA untuk mengambil keputusan dan/atau melakukan tindakan yang mengakibatkan pengeluaran anggaran belanja.',
            ],
            [
                'key'         => 'PP',
                'name'        => 'Pejabat Pengadaan (PP)',
                'description' => 'Pejabat administrasi/fungsional/personel yang bertugas melaksanakan Pengadaan Langsung, Penunjukan Langsung, dan/atau E-purchasing.',
            ],
            [
                'key'         => 'POKJA_PEMILIHAN',
                'name'        => 'Kelompok Kerja (Pokja) Pemilihan',
                'description' => 'Sumber daya manusia yang ditetapkan oleh Kepala UKPBJ untuk mengelola proses pemilihan Penyedia.',
            ],
            [
                'key'         => 'PENGELOLA_PBJ',
                'name'        => 'Jabatan Fungsional Pengelola PBJ',
                'description' => 'Aparatur Sipil Negara yang diberi tugas, tanggung jawab, wewenang, dan hak penuh untuk melaksanakan kegiatan pengadaan barang/jasa.',
            ],
            [
                'key'         => 'PPTK',
                'name'        => 'Pejabat Pelaksana Teknis Kegiatan (PPTK)',
                'description' => 'Pejabat pada unit SKPD yang melaksanakan 1 atau beberapa kegiatan dari suatu program sesuai bidang tugasnya.',
            ],
            [
                'key'         => 'PPHP',
                'name'        => 'Pejabat/Panitia Pemeriksa Hasil Pekerjaan',
                'description' => 'Pejabat/Panitia yang bertugas memeriksa kesesuaian administrasi hasil pekerjaan pengadaan barang/jasa.',
            ],
            [
                'key'         => 'TIM_TEKNIS',
                'name'        => 'Tim Teknis Pengadaan',
                'description' => 'Tim yang dibentuk oleh PPK untuk membantu penyusunan spesifikasi teknis, HPS, dan evaluasi teknis penawaran.',
            ],
            [
                'key'         => 'TIM_PENDUKUNG',
                'name'        => 'Tim Pendukung Pengadaan',
                'description' => 'Tim yang dibentuk untuk membantu kelancaran administrasi dan teknis pelaksanaan tugas PPK.',
            ],
            [
                'key'         => 'PENGAWAS_KONTRAK',
                'name'        => 'Pengawas Kontrak',
                'description' => 'Personel yang ditugaskan mengawasi pemenuhan spesifikasi teknis dan tahapan pelaksanaan kontrak pengadaan di lapangan.',
            ],
            [
                'key'         => 'APIP',
                'name'        => 'Aparat Pengawasan Intern Pemerintah (APIP)',
                'description' => 'Auditor/pengawas pada Inspektorat yang melakukan probity audit, pengawasan, dan konsultasi kepatuhan proses PBJ.',
            ],
            [
                'key'         => 'PENGURUS_BARANG',
                'name'        => 'Pengurus / Penyimpan Barang',
                'description' => 'Aparatur yang bertugas menatausahakan, mencatat, dan mengelola penerimaan barang milik daerah dari hasil pengadaan.',
            ],
        ];

        $roleTable = $db->table('procurement_roles');
        $roleInserted = 0;
        $roleUpdated = 0;

        foreach ($roles as $role) {
            $existing = $roleTable->where('key', $role['key'])->get()->getRowArray();
            if ($existing) {
                $roleTable->where('id', $existing['id'])->update([
                    'name'        => $role['name'],
                    'description' => $role['description'],
                    'is_active'   => 1,
                    'updated_at'  => $now,
                ]);
                $roleUpdated++;
            } else {
                $roleTable->insert([
                    'key'         => $role['key'],
                    'name'        => $role['name'],
                    'description' => $role['description'],
                    'is_active'   => 1,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $roleInserted++;
            }
        }
        CLI::write("Roles Seeding completed: {$roleInserted} inserted, {$roleUpdated} updated.", "green");

        return 0;
    }
}
