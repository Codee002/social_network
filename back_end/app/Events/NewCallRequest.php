<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewCallRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fromUser;
    public $toUserId;
    public $channel;
    public $message;
    public $thumb;
    /**
     * Create a new event instance.
     */
    public function __construct($fromUser, $toUserId, $channel, $message, $thumb)
    {
        $this->fromUser = $fromUser;
        $this->toUserId = $toUserId;
        $this->channel  = $channel;
        $this->message  = $message;
        $this->thumb    = $thumb;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting Call');
        return [
            new PrivateChannel('user.' . $this->toUserId),
        ];
    }

    public function broadcastAs()
    {
        return 'call.request';
    }

    public function broadcastWith()
    {
        return [
            'fromUser' => $this->fromUser,
            'channel'  => $this->channel,
            'message'  => $this->message,
            'thumb'    => $this->thumb,
        ];
    }
}
