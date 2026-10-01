<?php
namespace App\Services;

class LearningService
{
    public function calculateProgress($completed,$total)
    {
        if ($total <= 0) return 0;
        return round(($completed/$total)*100,2);
    }
}
