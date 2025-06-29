<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    protected $fillable = [
        'path',
        'post_id',
        'type',
    ];

    // ----------- Relation --------------
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
