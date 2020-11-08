<?php

namespace Tests\Prod\Models\Traits;

use App\Models\Category;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\Stubs\Models\UploadFilesStub;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestProd;
use Tests\Traits\TestStorage;

class UploadFilesProdTest extends TestCase
{
    use TestStorage, TestProd;
    private $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->skipTestIfNotProd();
        $this->stub = new UploadFilesStub();
        \Config::set('filesystems.default', 'gcs');
        $this->deleteAllFiles();
    }

    public function testUploadfile()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadfiles()
    {
        $files = [
            UploadedFile::fake()->create('video1.mp4'),
            UploadedFile::fake()->create('video2.mp4')
        ];
        $this->stub->uploadFiles($files);
        \Storage::assertExists("1/{$files[0]->hashName()}");
        \Storage::assertExists("1/{$files[1]->hashName()}");
    }

    public function testDeleteFileByHashName()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        $this->stub->deleteFile($file->hashName());
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFileByUploadedFile()
    {
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        $this->stub->deleteFile($file);
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteOldFiles()
    {
        $files = [
            UploadedFile::fake()->create('video1.mp4'),
            UploadedFile::fake()->create('video2.mp4')
        ];
        $this->stub->uploadFiles($files);
        $this->stub->deleteOldFiles();
        $this->assertCount(2, \Storage::allFiles());

        $this->stub->oldFiles = [$files[0]->hashName()] ;
        $this->stub->deleteOldFiles();
        \Storage::assertMissing("1/{$files[0]->hashName()}");
        \Storage::assertExists("1/{$files[1]->hashName()}");
    }

    public function testDeleteFiles()
    {
        $files = [
            UploadedFile::fake()->create('video1.mp4'),
            UploadedFile::fake()->create('video2.mp4')
        ];
        $this->stub->uploadFiles($files);
        $this->stub->deleteFiles($files);
        \Storage::assertMissing("1/{$files[0]->hashName()}");
        \Storage::assertMissing("1/{$files[1]->hashName()}");
    }

    public function testExtractFilesWhenFilesIsEmpty()
    {
        $attributes = [];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(0, $attributes);
        $this->assertCount(0, $files);
    }

    public function testExtractFilesWhenFiles1()
    {
        $attributes = ['file1' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(1, $attributes);
        $this->assertEquals(['file1' => 'test'], $attributes);
        $this->assertCount(0, $files);
    }

}
