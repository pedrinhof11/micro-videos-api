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

    const THUMB_FILE_MAX_SIZE   = 1024 * 5; // 5MB
    const BANNER_FILE_MAX_SIZE  = 1024 * 10; // 10MB
    const TRAILER_FILE_MAX_SIZE = 1024 * 1024 * 1; // 1GB
    const VIDEO_FILE_MAX_SIZE   = 1024 * 1024 * 50; // 50GB

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
        "thumb_file",
        "banner_file",
        "trailer_file"
    ];

    protected $casts = [
        "year_launched" => 'integer',
        "opened" => 'boolean',
        "duration" => 'integer',
    ];

    protected static $fileFields = [
        'video_file',
        'thumb_file',
        'banner_file',
        'trailer_file'
    ];

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
            if($saved) {
                $this->uploadFiles($files);
            }
            \DB::commit();
            if($saved && !empty($files)) {
                $this->deleteOldFiles();
            }
            return $saved;
        } catch (\Exception $e) {
            $this->deleteFiles($files);
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

    public function getThumbFileUrlAttribute()
    {
        return $this->thumb_file ? $this->getFileUrl($this->thumb_file) : null;
    }

    public function getVideoFileUrlAttribute()
    {
        return $this->video_file ? $this->getFileUrl($this->video_file) : null;
    }

    public function getBannerFileUrlAttribute()
    {
        return $this->banner_file ? $this->getFileUrl($this->banner_file) : null;
    }

    public function getTrailerFileUrlAttribute()
    {
        return $this->trailer_file ? $this->getFileUrl($this->trailer_file) : null;
    }
}
