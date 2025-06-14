<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'name',
        'thumb',
        'type',
    ];

    // ----------- Relation --------------
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('name', "role", 'joined_at', 'has_created');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // -------------- Function -----------

    public static function findConversationUser($ownerId, $userId)
    {
        $conversations = Conversation::with('users')->get();
        foreach ($conversations as $conversation) {
            $usersIdArray = $conversation->users()->pluck('user_id')->all();
            if (in_array($ownerId, $usersIdArray) && in_array($userId, $usersIdArray))
            {
                return $conversation;
            }
        }
        return null;
    }

    public function addUser($userId, $role = 'member', $joined_at = null, $has_created = null)
    {
        $this->users()->attach($userId, [
            'role'        => $role ?? "member",
            'joined_at'   => $joined_at ?? now(),
            'has_created' => $has_created ?? false,
        ]);

    }
}
