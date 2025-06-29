<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageMedia extends Model
{
    protected $fillable = [
        'path',
        'message_id',
        'type',

    ];

    // ----------- Relation --------------
    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
