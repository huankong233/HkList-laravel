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
        "group_id",
        "name",
        "use_count",
        "can_count",
    ];

    public function group()
    {
        return $this->belongsTo(Group::class)->withTrashed();
    }
}
