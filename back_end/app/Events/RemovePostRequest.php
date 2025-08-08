<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RemovePostRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    /**
     * Create a new event instance.
     */
    public function __construct($postId)
    {
        $this->postId  = $postId;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting Remove Post Request');
        return [
            new Channel('post.' . $this->postId),
        ];
    }

    public function broadcastAs()
    {
        return 'post.remove';
    }
}
