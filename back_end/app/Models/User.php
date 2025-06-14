<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
        'email',
        'email_active',
        'two_step_auth',
        'token',
        'status',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ------------- Relation ------------
    public function sendRelations()
    {
        return $this->hasMany(Relation::class, 'sender_id', 'id');
    }

    public function receivedRelations()
    {
        return $this->hasMany(Relation::class, 'received_id', 'id');
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class)->withPivot('name', 'role', 'joined_at', 'has_created');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // ------------- My func ------------
    public function isNotBanned()
    {
        return $this->status == "actived";
    }

    // Lấy ra danh sách bạn bè
    public function getFriends()
    {
        // $this->sender
    }

    public function getAddFriend()
    {
        $sentRelations = $this->sentRelations;
        foreach ($sentRelations as $relation) {
            echo $relation->ReceivedId;
            echo $relation->status;
        }
    }
}
