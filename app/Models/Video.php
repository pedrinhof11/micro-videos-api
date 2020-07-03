<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait;

    const RATING_LIST = [
        'L'  => 'L',
        '10' => '10',
        '12' => '12',
        '14' => '14',
        '16' => '16',
        '18' => '18',
    ];

    protected $keyType = "string";

    public $incrementing = false;

    protected $fillable = [
        "title",
        "description",
        "year_launched",
        "opened",
        "rating",
        "duration"
    ];

    protected $casts = [
        "year_launched" => 'integer',
        "opened" => 'boolean',
        "duration" => 'integer',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

}
