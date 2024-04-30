<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'action_name',
        'link',
        'md5',
        'size',
        'ua',
        'user_id',
        'account_id'
    ];
}
