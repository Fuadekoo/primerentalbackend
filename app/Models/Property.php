<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;
    protected $guarded = [];

    // One Property -> Many Bookings
    public function bookings()
    {
        return $this->hasMany(Book::class);
    }

    // One Property -> Many Feedback
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    // One Property -> One HomeType
    public function homeType()
    {
        return $this->belongsTo(HomeType::class);
    }
}
