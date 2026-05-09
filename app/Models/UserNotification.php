<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'link', 'is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
