<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

        /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who received the message.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
