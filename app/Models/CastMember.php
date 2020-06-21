<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        "name",
        "type"
    ];
}
