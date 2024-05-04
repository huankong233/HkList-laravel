<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvCode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'can_count',
        'use_count'
    ];
}
