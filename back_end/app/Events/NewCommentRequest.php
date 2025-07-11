<?php
namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewCommentRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $postId;
    public $comment;
    /**
     * Create a new event instance.
     */
    public function __construct($postId, $comment)
    {
        $this->postId  = $postId;
        $this->comment = $comment;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting New Comment Request');
        return [
            new Channel('post.' . $this->postId),
        ];
    }

    public function broadcastAs()
    {
        return 'comment.request';
    }

    public function broadcastWith()
    {
        $user                         = User::find($this->comment['user_id']);
        $this->comment['user_name']   = $user->profile->name;
        $this->comment['user_avatar'] = $user->profile->avatar;
        $this->comment['medias']      = $this->comment->medias;
        return [
            'comment' => $this->comment,
            'postId'    => $this->postId,
        ];
    }
}
