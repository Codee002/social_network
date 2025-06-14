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

}
