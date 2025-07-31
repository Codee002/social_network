<?php
namespace App\Events;

use App\Models\Story;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewViewStoryRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $storyId;
    public $view;
    public $userId;
    /**
     * Create a new event instance.
     */
    public function __construct($storyId, $view, $userId)
    {
        $this->storyId = $storyId;
        $this->view    = $view;
        $this->userId  = $userId;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting New Story View Request');
        return [
            new PrivateChannel('user.' . $this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'story.view.request';
    }

    public function broadcastWith()
    {
        $story = Story::find($this->storyId);
        $views = $story->watches()->pluck("user_id")->all();
        return [
            'views'   => $views,
            'storyId' => $this->storyId,
        ];
    }
}
