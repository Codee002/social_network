<?php
namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewViewRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $view;
    /**
     * Create a new event instance.
     */
    public function __construct($postId, $view)
    {
        $this->postId = $postId;
        $this->view   = $view;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting New View Request');
        return [
            new Channel('post.' . $this->postId),
        ];
    }

    public function broadcastAs()
    {
        return 'view.request';
    }

    public function broadcastWith()
    {
        $post     = Post::find($this->postId);
        $views = $post->watches()->pluck("user_id")->all();
        return [
            'views' => $views,
            'postId'   => $this->postId,
        ];
    }
}
