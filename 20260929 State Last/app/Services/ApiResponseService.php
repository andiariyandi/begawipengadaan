<?php

namespace App\Services;

class ApiResponseService
{
    public function success($data = [])
    {
        return [
            'status' => true,
            'data' => $data
        ];
    }

    public function error($message)
    {
        return [
            'status' => false,
            'message' => $message
        ];
    }
}
