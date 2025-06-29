<?php
namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewLikeRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $like;
    /**
     * Create a new event instance.
     */
    public function __construct($postId, $like)
    {
        $this->postId = $postId;
        $this->like   = $like;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting New Like Request');
        return [
            new Channel('post.' . $this->postId),
        ];
    }

    public function broadcastAs()
    {
        return 'like.request';
    }

    public function broadcastWith()
    {
        $post     = Post::find($this->postId);
        $likes = $post->likes()->pluck("user_id")->all();
        return [
            'likes' => $likes,
            'postId'   => $this->postId,
        ];
    }
}
