<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Record extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "ip",
        "fs_id",
        "filename",
        "size",
        "url",
        "ua",
        "user_id",
        "account_id",
        "normal_account_id"
    ];
}
