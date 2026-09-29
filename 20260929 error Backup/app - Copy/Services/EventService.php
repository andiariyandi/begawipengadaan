<?php

namespace App\Services;

class EventService
{
    public function availableQuota($quota, $registered)
    {
        return max(0, $quota - $registered);
    }

    public function canRegister($quota, $registered)
    {
        return $this->availableQuota($quota, $registered) > 0;
    }
}
