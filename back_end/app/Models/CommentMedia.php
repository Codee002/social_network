<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentMedia extends Model
{
    protected $fillable = [
        'content',
        'comment_id',
        'path',
        'type',

    ];

    // ----------- Relation --------------
    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
