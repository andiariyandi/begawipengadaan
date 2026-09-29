<?php

namespace App\Services;

class NotificationService
{
    public function unreadCount(array $notifications)
    {
        return count(array_filter($notifications, function($item){
            return empty($item['is_read']);
        }));
    }
}
