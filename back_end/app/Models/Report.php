<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'score',
        'user_id',
        'post_id',
        'content',
        'received_id',
    ];

    // ------------ Relation ------------
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_id', 'id');
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
