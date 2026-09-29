<?php

namespace App\Services;

class PPBJService
{
    public function activePersonnel($records)
    {
        return array_filter($records, function ($item) {
            return ($item['status'] ?? null) === 'active';
        });
    }
}
