<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoryMedia extends Model
{
    protected $fillable = [
        'path',
        'story_id',
    ];

    // ----------- Relation --------------
    public function story()
    {
        return $this->belongsTo(Story::class);
    }
}
