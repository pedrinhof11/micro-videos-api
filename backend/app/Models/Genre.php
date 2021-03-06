<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait, Filterable;

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "name",
        "is_active"
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
