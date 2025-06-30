<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'score',
        'content',
        'parent_id',
        'user_id',
        'post_id',
        'type',
    ];

    // ------------ Relation ------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function medias()
    {
        return $this->hasMany(CommentMedia::class);
    }
}
