<?php

namespace App\Models;

use CodeIgniter\Model;

class CaseStudyModel extends Model
{
    protected $table          = 'case_studies';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['category_id', 'title', 'content', 'author_id'];

    protected $validationRules = [
        'title'   => 'required|max_length[200]',
        'content' => 'required',
    ];

    public function browse(?int $categoryId = null, ?string $keyword = null): array
    {
        $builder = $this->select('case_studies.*, categories.name AS category_name, COALESCE(user_profiles.full_name, users.username) AS author_name, users.profile_type AS author_profile_type, user_asn_profiles.institution AS author_asn_institution, user_asn_profiles.position AS author_asn_position, user_provider_profiles.business_name AS author_provider_business, user_academic_profiles.institution AS author_academic_institution')
            ->join('categories', 'categories.id = case_studies.category_id')
            ->join('users', 'users.id = case_studies.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->join('user_asn_profiles', 'user_asn_profiles.user_id = users.id', 'left')
            ->join('user_provider_profiles', 'user_provider_profiles.user_id = users.id', 'left')
            ->join('user_academic_profiles', 'user_academic_profiles.user_id = users.id', 'left')
            ->orderBy('case_studies.created_at', 'DESC');

        if ($categoryId) {
            $builder->where('case_studies.category_id', $categoryId);
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('case_studies.title', $keyword)
                ->orLike('case_studies.content', $keyword)
                ->groupEnd();
        }

        return $builder->findAll();
    }

    public function withDetail(int $id): ?array
    {
        return $this->select('case_studies.*, categories.name AS category_name, COALESCE(user_profiles.full_name, users.username) AS author_name, users.profile_type AS author_profile_type, user_asn_profiles.institution AS author_asn_institution, user_asn_profiles.position AS author_asn_position, user_provider_profiles.business_name AS author_provider_business, user_academic_profiles.institution AS author_academic_institution')
            ->join('categories', 'categories.id = case_studies.category_id')
            ->join('users', 'users.id = case_studies.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->join('user_asn_profiles', 'user_asn_profiles.user_id = users.id', 'left')
            ->join('user_provider_profiles', 'user_provider_profiles.user_id = users.id', 'left')
            ->join('user_academic_profiles', 'user_academic_profiles.user_id = users.id', 'left')
            ->where('case_studies.id', $id)
            ->first();
    }
}
