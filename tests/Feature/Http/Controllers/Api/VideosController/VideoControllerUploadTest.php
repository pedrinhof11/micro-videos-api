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
        \Storage::fake();
        $this->assertInvalidationFile('video_file', 'mp4', 2000, 'video/mp4');
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
            'video_file' => UploadedFile::fake()->create('video.mp4')
        ];
    }
}
