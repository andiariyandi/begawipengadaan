<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Tabel `categories` dipakai bersama oleh KMS dan Regulasi (kolom `type`
 * membedakan keduanya). Model ini hanya untuk kategori KMS — filter type
 * selalu 'kms' supaya tidak tercampur dengan kategori regulasi.
 */
class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'type'];

    protected $validationRules = [
        'name' => 'required|max_length[100]',
    ];

    public function kmsCategories(): array
    {
        return $this->where('type', 'kms')->orderBy('name', 'ASC')->findAll();
    }
}
