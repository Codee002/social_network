<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Watch extends Model
{
    protected $fillable = [
        'score',
        'user_id',
        'post_id',
        'story_id',
    ];

    // ------------ Relation ------------
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->belongsTo(Post::class);
    }

    public function stories()
    {
        return $this->belongsTo(Story::class);
    }
}
