<?php

namespace Tests\Unit\Models\Traits;

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

class UploadFilesTest extends TestCase
{
    use DatabaseMigrations;

    private $stub;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stub = new UploadFilesStub();
    }

    public function testUploadfile()
    {
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        \Storage::assertExists("1/{$file->hashName()}");
    }

    public function testUploadfiles()
    {
        \Storage::fake();
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
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        $this->stub->deleteFile($file->hashName());
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFileByUploadedFile()
    {
        \Storage::fake();
        $file = UploadedFile::fake()->create('video.mp4');
        $this->stub->uploadFile($file);
        $this->stub->deleteFile($file);
        \Storage::assertMissing("1/{$file->hashName()}");
    }

    public function testDeleteFiles()
    {
        \Storage::fake();
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

    public function testExtractFiles()
    {
        $attributes = ['file1' => 'test', 'file2' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => 'test', 'file2' => 'test'], $attributes);
        $this->assertCount(0, $files);


        $file1 = UploadedFile::fake()->create('video1.mp4');
        $attributes = [ 'file1' => $file1, 'file2' => 'test'];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'file2' => 'test'], $attributes);
        $this->assertEquals([$file1], $files);

        $file2 = UploadedFile::fake()->create('video2.mp4');
        $attributes = [ 'file1' => $file1, 'file2' => $file2];
        $files = UploadFilesStub::extractFiles($attributes);
        $this->assertCount(2, $attributes);
        $this->assertEquals(['file1' => $file1->hashName(), 'file2' => $file2->hashName()], $attributes);
        $this->assertEquals([$file1, $file2], $files);
    }

}
