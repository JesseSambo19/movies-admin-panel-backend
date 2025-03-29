<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory; // 🔥 This enables the factory() method
/**
     * The attributes that are mass assignable.
     * 
     * This allows Laravel to automatically assign values 
     * when using Note::create([...]) or Note::update([...]).
     */
    protected $fillable = ['note', 'user_id'];

    /**
     * Alternative approach: Using $guarded
     * 
     * If you want to allow all fields except certain ones, 
     * you can use $guarded instead.
     * 
     * Example:
     * protected $guarded = ['is_admin'];
     * 
     * This means ALL other fields can be mass assigned, 
     * EXCEPT 'is_admin'.
     */
}