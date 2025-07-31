<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewStoryRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $story;
    public $userId;

    public function __construct($story, $userId)
    {
        $this->story  = $story;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting New Story Request');

        return [
            // new PrivateChannel('user.' . $this->post->user_id),
            new Channel("profile." . $this->userId),
        ];
    }

    public function broadcastAs()
    {
        return 'story.create';
    }

    public function broadcastWith()
    {
        $views = $this->story->watches()->pluck("user_id")->all();
        return [
            'story' => $this->story,
            'views' => $views,
        ];
    }
}
