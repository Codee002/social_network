<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Story extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'rule',
        'user_id',
    ];

    // ----------------- Relation -------------------
    public function watches()
    {
        return $this->hasMany(Watch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function medias()
    {
        return $this->hasMany(StoryMedia::class);
    }
}
