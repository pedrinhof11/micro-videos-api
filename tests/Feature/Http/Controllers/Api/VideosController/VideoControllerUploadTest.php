<?php

namespace Tests\Feature\Http\Controllers\Api\VideosController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerUploadTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestUploads;

    public function testInvalidationVideoFileField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            'video/mp4'
        );
    }

    public function testInvalidationTrailerFileField()
    {
        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::TRAILER_FILE_MAX_SIZE,
            'video/mp4'
        );
    }

    public function testInvalidationBannerFileField()
    {
        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image/jpeg, image/png'
        );
    }

    public function testInvalidationThumbFileField()
    {
        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            Video::THUMB_FILE_MAX_SIZE,
            'image/jpeg, image/png'
        );
    }

    /**
     * @throws \Exception
     */
    public function testStoreVideosWithFiles()
    {
        \Storage::fake();
        $categoriesId = factory(Category::class,2)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);
        $files = $this->getFiles();
        $data = $this->data + [
            'categories_id' => $categoriesId,
            'genres_id' => [$genre->id]
        ] + $files;
        $response = $this->postJson(
            $this->routeStore(),
            $data
        );
        $response->assertCreated();
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    /**
     * @throws \Exception
     */
    public function testUpdateVideosWithFiles()
    {
        \Storage::fake();
        $categoriesId = factory(Category::class,2)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);
        $files = $this->getFiles();
        $data = $this->data + [
                'categories_id' => $categoriesId,
                'genres_id' => [$genre->id]
            ] + $files;
        $response = $this->putJson(
            $this->routeUpdate(),
            $data
        );
        $response->assertOk();
        $id = $response->json('id');
        foreach ($files as $file) {
            \Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    private function getFiles()
    {
        return [
            'video_file'   => UploadedFile::fake()->create('video.mp4'),
            'banner_file'  => UploadedFile::fake()->image('banner.jpg'),
            'thumb_file'   => UploadedFile::fake()->image('thumb.jpg'),
            'trailer_file' => UploadedFile::fake()->create('trailer.mp4')
        ];
    }
}
