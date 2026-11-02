<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UniBridgeTestNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'UniBridge Notification Test',
            'message' => 'Your in-app notification system is working.',
            'type' => 'system_test',
        ];
    }
}
