<?php
namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewShareRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $share;
    /**
     * Create a new event instance.
     */
    public function __construct($postId, $share)
    {
        $this->postId = $postId;
        $this->share  = $share;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting New Share Request');
        return [
            new Channel('post.' . $this->postId),
        ];
    }

    public function broadcastAs()
    {
        return 'share.request';
    }

    public function broadcastWith()
    {
        $post   = Post::find($this->postId);
        $shares = $post->shares()->pluck("user_id")->all();
        return [
            'shares' => $shares,
            'postId' => $this->postId,
        ];
    }
}
