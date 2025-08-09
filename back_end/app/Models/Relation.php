<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relation extends Model
{
    protected $fillable = [
        'sender_id',
        'received_id',
        'type',
        'status',
    ];

    // -------------- Relation -------------------
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_id', 'id');
    }

    // -------------- Function -------------------
    public static function getRelationShip($ownerId, $userId)
    {
        $relation = Relation::query()
            ->where("sender_id", $ownerId)
            ->where("received_id", $userId)
            ->first();

        if (! $relation) {
            $relation = Relation::query()
                ->where("sender_id", $userId)
                ->where("received_id", $ownerId)
                ->first();
        }

        return $relation;
    }

    public static function getRelationStatus($ownerId, $userId)
    {
        if ($ownerId == $userId) {
            return "owner";
        }

        $relation = Relation::getRelationShip($ownerId, $userId);
        if (! $relation || $relation['status'] == "reject") {
            return "stranger";
        }

        if ($relation['type'] == "friend") {
            if ($relation['status'] == 'completed') {
                return 'friend';
            } else {
                return 'friend_pending';
            }
        }

        return "stranger";

    }

    public static function getFriends($userId)
    {
        $send = User::find($userId)->sendRelations()
            ->where("type", 'friend')
            ->where("status", 'completed')
            ->get();
        $receive = User::find($userId)->receivedRelations()
            ->where("type", 'friend')
            ->where("status", 'completed')
            ->get();

        $friendList = $send->merge($receive);
        foreach ($friendList as $friend) {
            if ($friend['sender_id'] != $userId) {
                $friend['user'] = User::find($friend['sender_id'])->load("profile");
            } else if ($friend['received_id'] != $userId) {
                $friend['user'] = User::find($friend['received_id'])->load("profile");
            }
        }
        return $friendList;
    }

    public static function getFriendsId($userId)
    {
        $friendList    = Relation::getFriends($userId);
        $friendListIds = [];
        foreach ($friendList as $relation) {
            $friendListIds[] = $relation->user->id;
        }
        return $friendListIds;
    }

    public static function getInvited($userId)
    {
        $listInvited = User::find($userId)->receivedRelations()
            ->where("type", 'friend')
            ->where("status", 'pending')
            ->get();

        foreach ($listInvited as $invited) {
            $invited['user'] = User::find($invited['sender_id'])->load("profile");
        }
        return $listInvited;
    }

}
