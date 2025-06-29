<?php
namespace App\Events;

use App\Models\Relation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRelationRequest implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $relation;
    /**
     * Create a new event instance.
     */
    public function __construct($relation)
    {
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
        Log::info('Broadcasting SendRelationRequest', ['relation' => $this->relation]);
        return [
            new PrivateChannel('user.' . $this->relation->sender_id),
        ];
    }

    public function broadcastAs()
    {
        return 'send.relation';
    }

    public function broadcastWith()
    {
        $listInvited = Relation::getInvited($this->relation->sender_id);
        $friendList  = Relation::getFriends($this->relation->sender_id);
        $relation    = $this->relation['status'] == 'delete' ? "stranger" : $this->relation;

        return [
            "listInvited" => $listInvited,
            "friendList"  => $friendList,
            "relation"    => $relation,
        ];
    }
}
