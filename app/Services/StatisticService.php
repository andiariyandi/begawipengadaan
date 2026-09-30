<?php

namespace App\Services;

class StatisticService
{
    public function percentage($value, $total)
    {
        if ($total <= 0) {
            return 0;
        }

        return round(($value / $total) * 100, 2);
    }
}
