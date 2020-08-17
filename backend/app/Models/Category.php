<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait;

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "name",
        "description",
        "is_active"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
