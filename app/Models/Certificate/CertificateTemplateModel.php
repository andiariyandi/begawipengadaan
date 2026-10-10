<?php

namespace App\Models\Certificate;

use CodeIgniter\Model;

/**
 * certificate_templates (v4).
 *
 * Kolom yang dipakai:
 *  - name           VARCHAR(255)
 *  - source_type    ENUM('learning','event')         -- sumber sertifikat
 *  - template_payload JSON                            -- payload teks/atribut
 *  - background_file_id BIGINT NULL → file_assets.id
 *  - is_active      TINYINT(1) DEFAULT 1
 *  - created_by, updated_by → users.id (NULL)
 *
 * TIDAK memakai kolom legacy seperti file_path.
 */
class CertificateTemplateModel extends Model
{
    protected $table         = 'certificate_templates';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'source_type',
        'template_payload',
        'background_file_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * Dropdown sumber template: hanya yang aktif, diurut naik.
     */
    public function listActive(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Ambil template default yang aktif dan cocok dengan source_type.
     * Fallback: kalau tidak ada yang cocok, return null.
     *
     * @param string $sourceType 'learning' | 'event'  (nilai certificate_templates.source_type,
     *                          BUKAN certificates.source_type).
     */
    public function findDefaultForSource(string $sourceType): ?array
    {
        $row = $this->where('is_active', 1)
            ->where('source_type', $sourceType)
            ->orderBy('id', 'ASC')
            ->first();
        if ($row) {
            return $row;
        }

        // Fallback to any active template so generation is robust
        return $this->where('is_active', 1)->orderBy('id', 'ASC')->first();
    }
}
