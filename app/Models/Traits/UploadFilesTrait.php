<?php


namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use phpDocumentor\Reflection\Types\Self_;
use Ramsey\Uuid\Uuid;

trait UploadFilesTrait
{
    public $oldFiles = [];

    protected abstract function uploadDir();

    public static function bootUploadFilesTrait()
    {
        static::updating(function (Model $model) {
            $fieldsUpdated = array_keys($model->getDirty());
            $filesUpdated = array_intersect($fieldsUpdated, self::$fileFields);
            $originalModel = $model->getOriginal();
            $oldFiles = [];
            foreach ($filesUpdated as $field) {
                if(isset($originalModel[$field])) {
                    $oldFiles[] = $originalModel[$field];
                }
            }
            $model->oldFiles = $oldFiles;

        });
    }

    public function getFileRelativePath($fileName)
    {
        return "{$this->uploadDir()}/{$fileName}";
    }

    public function getFileUrl($fileName)
    {
        return \Storage::url($this->getFileRelativePath($fileName));
    }

        /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    /**
     * @param UploadedFile $file
     */
    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadDir());
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile($file)
    {
        $fileName = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadDir()}/{$fileName}");
    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileFields as $field) {
            if(isset($attributes[$field]) && $attributes[$field] instanceof UploadedFile) {
                $files[] = $attributes[$field];
                $attributes[$field] = $attributes[$field]->hashName();
            }
        }
        return $files;
    }
}
