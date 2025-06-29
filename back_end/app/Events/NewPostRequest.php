<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewPostRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $post;

    public function __construct($post)
    {
        $this->post = $post->load('user.profile');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting New Post Request', ['post' => $this->post]);

        return [
            // new PrivateChannel('user.' . $this->post->user_id),
            new Channel("profile." . $this->post->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'post.create';
    }

    public function broadcastWith()
    {
        $likes    = $this->post->likes()->pluck("user_id")->all();
        $views    = $this->post->watches()->pluck("user_id")->all();
        $comments = $this->post->comments()->orderBy("created_at", 'desc')->get();
        $shares   = $this->post->shares()->orderBy("created_at", 'desc')->get();
        return [
            'post'     => $this->post,
            'likes'    => $likes,
            'views'    => $views,
            'comments' => $comments,
            'shares'   => $shares,
        ];
    }
}
