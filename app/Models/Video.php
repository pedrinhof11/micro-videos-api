<?php

namespace App\Models;

use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UploadFilesTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Video extends Model
{
    use SoftDeletes, UuidTrait, SerializeDateTrait, UploadFilesTrait;

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
        "duration",
        "video_file",
        "thumb_file"
    ];

    protected $casts = [
        "year_launched" => 'integer',
        "opened" => 'boolean',
        "duration" => 'integer',
    ];

    protected static $fileFields = ['video_file', 'thumb_file'];

    public static function create(array $attributes = [])
    {
        $files = self::extractFiles($attributes);
        try{
            \DB::beginTransaction();
            /** @var Video $model */
            $model = static::query()->create($attributes);
            $model->handleRelations($attributes);
            $model->uploadFiles($files);
            \DB::commit();
            return $model;
        } catch (\Exception $e) {
            if(isset($model)) {
                $model->deleteFiles($files);
            }
            \DB::rollBack();
            throw $e;
        }

    }

    public function update(array $attributes = [], array $options = [])
    {
        $files = self::extractFiles($attributes);
        try{
            \DB::beginTransaction();
            $saved = parent::update($attributes, $options);
            $this->handleRelations($attributes);
            $this->uploadFiles($files);
            \DB::commit();
            return $saved;
        } catch (\Exception $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    public function handleRelations(array $data)
    {
        if (isset($data['categories_id'])) {
            $this->categories()->sync($data['categories_id']);
        }
        if (isset($data['genres_id'])) {
            $this->genres()->sync($data['genres_id']);
        }
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    protected function uploadDir()
    {
        return $this->id;
    }
}
