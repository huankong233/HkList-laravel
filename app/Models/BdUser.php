<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BdUser extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'netdisk_name',
        'baidu_name',
        'svip_end_time',
        'switch',
        'state',
        'vip_type'
    ];
}
