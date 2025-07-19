<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'gender',
        'birthday',
        'avatar',
        'thumb',
        'bio',
        'user_id',
        'address',
    ];

    // ------------ Relation -----------
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
