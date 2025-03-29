<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'openingText', 'releaseDate', 'user_id'];

    // Define relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
