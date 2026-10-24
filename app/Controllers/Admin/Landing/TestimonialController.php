<?php

namespace App\Controllers\Admin\Landing;

use App\Controllers\BaseController;
use App\Services\AuditService;
use App\Services\LandingImageUploadService;
use CodeIgniter\Database\ConnectionInterface;

class TestimonialController extends BaseController
{
    private AuditService $auditService;
    private ConnectionInterface $db;

    public function __construct()
    {
        $this->auditService = new AuditService();
        $this->db           = \Config\Database::connect();
    }

    public function index()
    {
        $testimonials = $this->db->table('testimonials')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        $totalCount     = count($testimonials);
        $activeCount    = count(array_filter($testimonials, fn($t) => (int) ($t['is_active'] ?? 0) === 1));
        $inactiveCount  = $totalCount - $activeCount;

        // Rating average calculation
        $totalRating = 0;
        foreach ($testimonials as $t) {
            $totalRating += (float) ($t['rating'] ?: 5);
        }
        $avgRating = $totalCount > 0 ? round($totalRating / $totalCount, 1) : 5.0;

        // Category breakdown
        $opdCount      = count(array_filter($testimonials, fn($t) => in_array($t['category'] ?? '', ['opd', 'pimpinan', 'ppk', 'PPK Pengadaan', 'PPK Rumah Sakit', 'Tim Teknis'])));
        $pokjaCount    = count(array_filter($testimonials, fn($t) => in_array($t['category'] ?? '', ['pokja', 'Pokja Pemilihan'])));
        $penyediaCount = count(array_filter($testimonials, fn($t) => in_array($t['category'] ?? '', ['penyedia', 'Penyedia UMKM'])));
        $auditorCount  = count(array_filter($testimonials, fn($t) => in_array($t['category'] ?? '', ['auditor', 'Auditor APIP'])));

        $stats = [
            'total'          => $totalCount,
            'active_count'   => $activeCount,
            'inactive_count' => $inactiveCount,
            'avg_rating'     => number_format($avgRating, 1),
            'opd_count'      => $opdCount,
            'pokja_count'    => $pokjaCount,
            'penyedia_count' => $penyediaCount,
            'auditor_count'  => $auditorCount,
        ];

        return view('dashboard/landing/testimonial/index', [
            'title'         => 'Kelola Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Testimoni Pengguna & Mitra PBJ',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonials'  => $testimonials,
            'stats'         => $stats,
        ]);
    }

    public function create()
    {
        $maxOrderRow = $this->db->table('testimonials')
            ->selectMax('sort_order', 'max_order')
            ->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? 0)) + 1;

        return view('dashboard/landing/testimonial/form', [
            'title'         => 'Tambah Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Tambah Testimoni Pengguna',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonial'   => null,
            'nextOrder'     => $nextOrder,
        ]);
    }

    public function store()
    {
        $rules = [
            'name'  => 'required|max_length[150]',
            'quote' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $avatarUrl = trim((string) $this->request->getPost('avatar_url'));
        $avatarFile = $this->request->getFile('avatar_file');
        $uploaded = LandingImageUploadService::upload($avatarFile, 'testimonials', $avatarUrl ?: null, 'user_testimonial');
        if (! empty($uploaded)) {
            $avatarUrl = $uploaded;
        }
        if (empty($avatarUrl)) {
            $avatarUrl = 'assets/main/images/testimonial/testimonial-1-1.jpg';
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        $rating = (int) ($this->request->getPost('rating') ?: 5);
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        $data = [
            'name'             => trim((string) $this->request->getPost('name')),
            'role_title'       => trim((string) $this->request->getPost('role_title')),
            'organization'     => trim((string) $this->request->getPost('organization')),
            'avatar_url'       => $avatarUrl,
            'rating'           => $rating,
            'quote'            => trim((string) $this->request->getPost('quote')),
            'impact_statement' => trim((string) $this->request->getPost('impact_statement')),
            'category'         => trim((string) $this->request->getPost('category') ?: 'opd'),
            'sort_order'       => $sortOrder,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->table('testimonials')->insert($data);
        $insertId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.testimonial.created', 'landing', 'testimonials', (int) $insertId, $data);

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
        }

        return view('dashboard/landing/testimonial/form', [
            'title'         => 'Ubah Testimoni | Dashboard BegawiPengadaan',
            'pageTitle'     => 'Ubah Testimoni Pengguna',
            'activeMenu'    => 'landing',
            'activeSubmenu' => 'testimoni',
            'testimonial'   => $testimonial,
            'nextOrder'     => (int) ($testimonial['sort_order'] ?? 1),
        ]);
    }

    public function update($id)
    {
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
        }

        $rules = [
            'name'  => 'required|max_length[150]',
            'quote' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $avatarUrl = trim((string) $this->request->getPost('avatar_url'));
        $avatarFile = $this->request->getFile('avatar_file');
        $currentAvatar = $testimonial['avatar_url'] ?? 'assets/main/images/testimonial/testimonial-1-1.jpg';
        $uploaded = LandingImageUploadService::upload($avatarFile, 'testimonials', $avatarUrl ?: $currentAvatar, 'user_testimonial');
        if (! empty($uploaded)) {
            $avatarUrl = $uploaded;
        }
        if (empty($avatarUrl)) {
            $avatarUrl = $currentAvatar;
        }

        $rawOrder  = $this->request->getPost('sort_order');
        $sortOrder = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : (int) ($testimonial['sort_order'] ?? 1);

        $rating = (int) ($this->request->getPost('rating') ?: 5);
        if ($rating < 1) $rating = 1;
        if ($rating > 5) $rating = 5;

        $data = [
            'name'             => trim((string) $this->request->getPost('name')),
            'role_title'       => trim((string) $this->request->getPost('role_title')),
            'organization'     => trim((string) $this->request->getPost('organization')),
            'avatar_url'       => $avatarUrl,
            'rating'           => $rating,
            'quote'            => trim((string) $this->request->getPost('quote')),
            'impact_statement' => trim((string) $this->request->getPost('impact_statement')),
            'category'         => trim((string) $this->request->getPost('category') ?: 'opd'),
            'sort_order'       => $sortOrder,
            'is_active'        => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->table('testimonials')->where('id', $id)->update($data);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.testimonial.updated', 'landing', 'testimonials', (int) $id, ['before' => $testimonial, 'after' => $data]);

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil diperbarui.');
    }

    public function duplicate($id)
    {
        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
        }

        $maxOrderRow = $this->db->table('testimonials')->selectMax('sort_order', 'max_order')->get()->getRowArray();
        $nextOrder = ((int) ($maxOrderRow['max_order'] ?? $testimonial['sort_order'])) + 1;

        $newData = [
            'name'             => $testimonial['name'] . ' (Salinan)',
            'role_title'       => $testimonial['role_title'],
            'organization'     => $testimonial['organization'],
            'avatar_url'       => $testimonial['avatar_url'],
            'rating'           => $testimonial['rating'],
            'quote'            => $testimonial['quote'],
            'impact_statement' => $testimonial['impact_statement'],
            'category'         => $testimonial['category'],
            'sort_order'       => $nextOrder,
            'is_active'        => 0, // Duplicate starts as draft
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->table('testimonials')->insert($newData);
        $newId = $this->db->insertID();

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.testimonial.duplicated', 'testimonials', 'id', (int) $newId, $newData);

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil diduplikat sebagai draf.');
    }

    public function updateOrder()
    {
        $id       = (int) ($this->request->getPost('id') ?: $this->request->getVar('id') ?: ($_POST['id'] ?? 0));
        $rawOrder = $this->request->getPost('order') ?? $this->request->getVar('order') ?? ($_POST['order'] ?? null);
        $order    = ($rawOrder !== null && $rawOrder !== '') ? (int) $rawOrder : 1;

        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID testimoni tidak valid.'])->setStatusCode(400);
        }

        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if (! $testimonial) {
            return $this->response->setJSON(['success' => false, 'message' => 'Testimoni tidak ditemukan.'])->setStatusCode(404);
        }

        $this->db->table('testimonials')->where('id', $id)->update([
            'sort_order' => $order,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.testimonial.reordered', 'testimonials', 'sort_order', $id, ['order' => $order]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Urutan testimoni berhasil diperbarui.',
                'order'   => $order,
            ]);
        }

        return redirect()->to('dashboard/landing/testimoni')->with('success', 'Urutan testimoni berhasil diperbarui.');
    }

    public function delete($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if ($testimonial) {
            $this->db->table('testimonials')->where('id', $id)->delete();

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.testimonial.deleted', 'landing', 'testimonials', (int) $id, ['deleted' => $testimonial]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Testimoni berhasil dihapus.',
                ]);
            }

            return redirect()->to('dashboard/landing/testimoni')->with('success', 'Testimoni berhasil dihapus.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Testimoni tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
    }

    public function toggle($id)
    {
        if ($res = $this->validateSafeMutation()) {
            return $res;
        }

        $testimonial = $this->db->table('testimonials')->where('id', $id)->get()->getRowArray();
        if ($testimonial) {
            $newStatus = $testimonial['is_active'] ? 0 : 1;
            $this->db->table('testimonials')->where('id', $id)->update([
                'is_active'  => $newStatus,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $rawActorId = (int) (session()->get('user_id') ?: 0);
            $actorId    = $rawActorId > 0 ? $rawActorId : null;
            $this->auditService->log($actorId, 'landing.testimonial.toggled', 'testimonials', 'is_active', (int) $id, ['new_status' => $newStatus]);

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success'   => true,
                    'message'   => 'Status testimoni berhasil diubah.',
                    'is_active' => $newStatus,
                    'id'        => (int) $id,
                ]);
            }

            return redirect()->to('dashboard/landing/testimoni')->with('success', 'Status testimoni berhasil diubah.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Testimoni tidak ditemukan.'])->setStatusCode(404);
        }

        return redirect()->to('dashboard/landing/testimoni')->with('error', 'Testimoni tidak ditemukan.');
    }

    /**
     * Pre-populate standard canonical BegawiPengadaan PBJ stakeholder testimonials
     */
    public function seedRecommendations()
    {
        $presets = [
            [
                'name'             => 'Ahmad Zulkarnaen, S.T., M.T.',
                'role_title'       => 'Pejabat Pembuat Komitmen (PPK)',
                'organization'     => 'Dinas Bina Marga & Bina Konstruksi Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-1.jpg',
                'rating'           => 5,
                'quote'            => 'Penyusunan HPS dan spesifikasi teknis untuk paket infrastruktur jalan kini jauh lebih terstandarisasi berkat template dokumen di Bunga Rampai dan sinkronisasi regulasi terpusat. Risiko deviasi hukum saat audit probity berkurang drastis.',
                'impact_statement' => 'Efisiensi persiapan lelang 55% & probity nihil temuan',
                'category'         => 'opd',
                'sort_order'       => 1,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Rina Widyastuti, S.E., M.Si.',
                'role_title'       => 'Pokja Pemilihan / UKPBJ',
                'organization'     => 'Biro Pengadaan Barang & Jasa Setda Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-2.jpg',
                'rating'           => 5,
                'quote'            => 'Arena Belajar dan simulasi evaluasi penawaran sangat membantu peningkatan kapasitas anggota Pokja. Ujian sertifikasi dan materi terstruktur meningkatkan kepercayaan diri kami dalam tender bernilai strategis di tingkat provinsi.',
                'impact_statement' => 'Evaluasi tender 100% tepat waktu dan bebas sanggahan banding',
                'category'         => 'pokja',
                'sort_order'       => 2,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Hendra Kusuma, S.Kom.',
                'role_title'       => 'Mitra Penyedia & UMKM',
                'organization'     => 'PT Lampung Cipta Mandiri',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-1.jpg',
                'rating'           => 5,
                'quote'            => 'Sebagai pelaku usaha lokal di Lampung, portal BegawiPengadaan membuka transparansi penuh. Kami mudah memahami kriteria kualifikasi, etika penawaran, hingga tata cara etalase produk pada e-katalog lokal Pemprov Lampung.',
                'impact_statement' => 'Onboarding E-Katalog Lokal lebih cepat dan transparan',
                'category'         => 'penyedia',
                'sort_order'       => 3,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Drs. Bambang Irawan, Ak., CA.',
                'role_title'       => 'Pengawas APIP & Auditor',
                'organization'     => 'Inspektorat Provinsi Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-1-3.jpg',
                'rating'           => 5,
                'quote'            => 'BegawiPengadaan memfasilitasi audit kepatuhan secara objektif. Riwayat versi regulasi dan standar dokumen baku memudahkan kami memverifikasi kesesuaian prosedur dari tahap perencanaan sampai serah terima hasil.',
                'impact_statement' => 'Mitigasi deviasi hukum dan penguatan integritas pengadaan',
                'category'         => 'auditor',
                'sort_order'       => 4,
                'is_active'        => 1,
            ],
            [
                'name'             => 'dr. Nurul Hidayati, M.Kes.',
                'role_title'       => 'PPK Pengadaan Alkes',
                'organization'     => 'RSUD Dr. H. Abdul Moeloek',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-2.jpg',
                'rating'           => 5,
                'quote'            => 'Pengadaan obat dan alat medis memerlukan ketepatan waktu serta kepatuhan e-katalog nasional yang sangat ketat. Fitur konsultasi dan referensi regulasi di BegawiPengadaan memastikan ketersediaan logistik rumah sakit selalu terjamin.',
                'impact_statement' => 'Ketepatan pasokan medis 99.2% dengan e-katalog akurat',
                'category'         => 'opd',
                'sort_order'       => 5,
                'is_active'        => 1,
            ],
            [
                'name'             => 'Surya Darmawan, S.T.',
                'role_title'       => 'Tim Teknis & Pengawas',
                'organization'     => 'Dinas Perumahan & Kawasan Permukiman Lampung',
                'avatar_url'       => 'assets/main/images/testimonial/testimonial-2-3.jpg',
                'rating'           => 5,
                'quote'            => 'Standarisasi berita acara dan format pemeriksaan pekerjaan di Bunga Rampai mempermudah tim teknis melakukan serah terima fisik di lapangan secara presisi dan sesuai kontrak kerja.',
                'impact_statement' => '100% Berita Acara terverifikasi digital',
                'category'         => 'opd',
                'sort_order'       => 6,
                'is_active'        => 1,
            ],
        ];

        $inserted = 0;
        foreach ($presets as $item) {
            $exists = $this->db->table('testimonials')
                ->where('name', $item['name'])
                ->countAllResults();

            if ($exists === 0) {
                $item['created_at'] = date('Y-m-d H:i:s');
                $item['updated_at'] = date('Y-m-d H:i:s');
                $this->db->table('testimonials')->insert($item);
                $inserted++;
            }
        }

        $rawActorId = (int) (session()->get('user_id') ?: 0);
        $actorId    = $rawActorId > 0 ? $rawActorId : null;
        $this->auditService->log($actorId, 'landing.testimonial.seeded', 'landing', 'testimonials', 0, ['seeded_count' => $inserted]);

        return redirect()->to('dashboard/landing/testimoni')
            ->with('success', "Rekomendasi testimoni stakeholder berhasil disinkronisasi ({$inserted} ditambahkan).");
    }
}
