<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CallAcceptRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $channel;
    /**
     * Create a new event instance.
     */
    public function __construct($message, $channel)
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting Call Accept');
        return [
            new PrivateChannel('user.' . $this->message->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'call.accept';
    }

    public function broadcastWith()
    {
        return [
            'channel' => $this->channel,
            'message' => $this->message,
        ];
    }

}
