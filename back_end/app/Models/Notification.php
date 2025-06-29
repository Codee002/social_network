<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'content',
        'user_id',
    ];

    // ------------ Relation ------------
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
