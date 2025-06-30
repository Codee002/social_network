<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewNotificationEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notification;
    /**
     * Create a new event instance.
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting Notification', ['notification' => $this->notification]);
        return [
            new PrivateChannel('user.' . $this->notification->received_id),
        ];
    }

    public function broadcastAs()
    {
        return 'notification.request';
    }

    public function broadcastWith()
    {
        $notification               = $this->notification;
        $notification['userName']   = $notification->sender->profile->name;
        $notification['userAvatar'] = $notification->sender->profile->avatar;
        return [
            'notification' => $notification,
            'userId'       => $notification->received_id,
        ];
    }
}
