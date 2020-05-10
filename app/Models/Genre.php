<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, UuidTrait;

    protected $keyType = "string";
    public $incrementing = false;

    protected $fillable = [
        "name",
        "is_active"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
