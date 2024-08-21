<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "id",
        "baidu_name",
        "account_type",
        "cookie",
        "access_token",
        "refresh_token",
        "cid",
        "expired_at",
        "vip_type",
        "switch",
        "reason",
        "prov",
        "svip_end_at",
        "last_use_at",
        "uk"
    ];

    public function records()
    {
        return $this->hasMany(Record::class)->withTrashed();
    }
}
