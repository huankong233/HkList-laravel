<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'used',
        'account_id',
        'vcode_str'
    ];
}
