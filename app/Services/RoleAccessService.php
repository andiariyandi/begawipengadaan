<?php

namespace App\Services;

class RoleAccessService
{
    public function workspaceAllowed(string $workspace, array $roles): bool
    {
        $map = [
            'trainer' => ['trainer'],
            'evaluator' => ['evaluator'],
            'peserta' => ['peserta'],
        ];

        return isset($map[$workspace])
            && count(array_intersect($map[$workspace], $roles)) > 0;
    }

    public function adminAllowed(array $roles): bool
    {
        return count(array_intersect(
            ['administrator', 'super_administrator'],
            $roles
        )) > 0;
    }
}
