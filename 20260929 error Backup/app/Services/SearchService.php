<?php
namespace App\Services;

class SearchService
{
    public function normalize($keyword)
    {
        return strtolower(trim($keyword));
    }
}
