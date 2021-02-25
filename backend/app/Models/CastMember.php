<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait, Filterable;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    public static $types = [
        self::TYPE_DIRECTOR,
        self::TYPE_ACTOR
    ];

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        "name",
        "type"
    ];

    protected $casts = [
        'type' => 'integer'
    ];
}
