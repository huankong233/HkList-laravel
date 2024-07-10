<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "inv_code_id",
        "username",
        "password",
        "role"
    ];

    public function inv_code()
    {
        return $this->belongsTo(InvCode::class)->withTrashed();
    }

    public function group()
    {
        return $this->hasOneThrough(Group::class, InvCode::class, "id", "id", "inv_code_id", "group_id")->withTrashed()->withTrashedParents();
    }

    public function records()
    {
        return $this->hasMany(Record::class)->withTrashed();
    }
}
