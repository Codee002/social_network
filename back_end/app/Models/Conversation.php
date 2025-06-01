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
        return $this->belongsToMany(User::class)->withPivot("role", 'joined_at', 'has_created');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
