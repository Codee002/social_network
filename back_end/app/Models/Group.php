<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name',
        'thumb',
        'rule',
    ];

    // ----------- Relation --------------
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot("role", 'joined_at', 'has_created');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
