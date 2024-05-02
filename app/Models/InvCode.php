<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'can_count',
        'use_count'
    ];
}
