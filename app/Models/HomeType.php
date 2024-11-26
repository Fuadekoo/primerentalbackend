<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomeType extends Model
{
    use HasFactory;
    protected $guarded = [];

    // One HomeType -> Many Properties
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
