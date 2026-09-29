<?php
namespace App\Services;

class WorkspaceService
{
    public function resolveRoleWorkspace(string $role): string
    {
        return match($role) {
            'operator' => 'operator',
            'trainer' => 'trainer',
            'evaluator' => 'evaluator',
            'peserta' => 'peserta',
            default => 'dashboard',
        };
    }
}
