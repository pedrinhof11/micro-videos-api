<?php

namespace Tests\Stubs\Models;

use App\Models\Traits\UploadFilesTrait;
use Illuminate\Database\Eloquent\Model;

class UploadFilesStub extends Model
{
    use UploadFilesTrait;

    protected static $fileFields = ['file1', 'file2'];

    protected function uploadDir()
    {
        return '1';
    }
}
