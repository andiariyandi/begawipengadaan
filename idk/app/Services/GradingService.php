<?php
namespace App\Services;

class GradingService
{
    public function calculateFinalScore($quizScore, $assignmentScore)
    {
        return ($quizScore + $assignmentScore) / 2;
    }
}
