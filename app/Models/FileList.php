<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileList extends Model
{
    use HasFactory;

    protected $fillable = [
        "surl",
        "pwd",
        "fs_id",
        "size",
        "filename",
        "md5"
    ];
}
