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

class ReceiveRelationRequest implements ShouldBroadcastNow
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
        Log::info('Broadcasting ReceiveRelationRequest', ['relation' => $this->relation]);
        return [
            new PrivateChannel('user.' . $this->relation->received_id),
        ];
    }

    public function broadcastAs()
    {
        return 'receive.relation';
    }

    public function broadcastWith()
    {
        $listInvited = Relation::getInvited($this->relation->received_id);
        $friendList  = Relation::getFriends($this->relation->received_id);
        $relation    = $this->relation['status'] == 'delete' ? "stranger" : $this->relation;

        return [
            "listInvited" => $listInvited,
            "friendList"  => $friendList,
            "relation"    => $relation,
        ];
    }
}
