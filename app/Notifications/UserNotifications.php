<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class UserNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $timeout = 30;

    public function __construct(public $data)
    {
        Log::info('Notification initialized', ['data' => $this->data]);
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        try {
            Log::info('Saving notification', [
                'notifiable' => $notifiable->id ?? 'no_id',
                'data' => $this->data
            ]);

            return $this->data;

        } catch (\Exception $e) {
            Log::error('Failed to save notification', [
                'error' => $e->getMessage(),
                'data' => $this->data
            ]);
            throw $e;
        }
    }

    public function toArray($notifiable)
    {
        return $this->data;
    }

    public function failed(\Exception $exception)
    {
        Log::error('Notification failed', [
            'error' => $exception->getMessage(),
            'data' => $this->data
        ]);
    }
}
