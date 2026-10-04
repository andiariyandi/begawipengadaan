<?php

namespace App\Models\Regulation;

use CodeIgniter\Model;

class RegulationRelationModel extends Model
{
    protected $table         = 'regulation_relations';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'source_regulation_id', 'target_regulation_id',
        'relation_type', 'notes', 'created_by', 'created_at',
    ];

    public function listForAdmin(): array
    {
        return $this->select('regulation_relations.*,
                              r_src.title AS source_title, r_src.document_number AS source_number,
                              r_tgt.title AS target_title, r_tgt.document_number AS target_number,
                              u.username AS creator_username')
            ->join('regulations r_src', 'r_src.id = regulation_relations.source_regulation_id', 'left')
            ->join('regulations r_tgt', 'r_tgt.id = regulation_relations.target_regulation_id', 'left')
            ->join('users u', 'u.id = regulation_relations.created_by', 'left')
            ->orderBy('regulation_relations.created_at', 'DESC')
            ->findAll();
    }
}