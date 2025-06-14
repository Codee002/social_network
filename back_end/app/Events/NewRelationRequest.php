<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewRelationRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $owner;
    public $relation;
    /**
     * Create a new event instance.
     */
    public function __construct($owner, $relation)
    {
        $this->owner    = $owner;
        $this->relation = $relation;
        // dd('user.' . $this->relation->sender_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        Log::info('Broadcasting NewRelationRequest', ['relation' => $this->relation]);
        return [
            new PrivateChannel('user.' . $this->relation->received_id),
            new PrivateChannel('user.' . $this->relation->sender_id),
        ];
    }

    public function broadcastAs()
    {
        return 'relation.request';
    }
}
