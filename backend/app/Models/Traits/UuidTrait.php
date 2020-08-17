<?php


namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

trait UuidTrait
{
    public static function bootUuidTrait() {
        static::creating(function (Model $model) {
            $model->setAttribute($model->getKeyName(), Uuid::uuid4()->toString());
        });
    }
}
