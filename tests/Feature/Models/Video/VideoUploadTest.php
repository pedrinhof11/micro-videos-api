<?php

namespace Tests\Feature\Models\Video;

use App\Models\Video;
use Illuminate\Database\Events\TransactionCommitted;
use Illuminate\Http\UploadedFile;
use Tests\Exceptions\TestException;

class VideoUploadTest extends BaseVideoTestCase
{

    public function testCreateWithFiles()
    {
        \Storage::fake();
        $video = Video::create(
            $this->data + [
                "thumb_file" => UploadedFile::fake()->image('thumb.jpg'),
                "video_file" => UploadedFile::fake()->create('video.mp4'),
                "banner_file" => UploadedFile::fake()->image('banner.mp4'),
                "trailer_file" => UploadedFile::fake()->create('trailer.mp4')
            ]
        );
        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
    }

    public function testCreateIfRollbackFiles()
    {
        \Storage::fake();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            $video = Video::create(
                $this->data + [
                    "thumb_file" => UploadedFile::fake()->image('thumb.jpg'),
                    "video_file" => UploadedFile::fake()->create('video.mp4'),
                    "banner_file" => UploadedFile::fake()->image('banner.mp4'),
                    "trailer_file" => UploadedFile::fake()->create('trailer.mp4')
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithFiles()
    {
        \Storage::fake();
        $files = [
            "thumb_file" => UploadedFile::fake()->image('thumb.jpg'),
            "video_file" => UploadedFile::fake()->create('video.mp4'),
            "banner_file" => UploadedFile::fake()->image('banner.mp4'),
            "trailer_file" => UploadedFile::fake()->create('trailer.mp4')
        ];
        $video = Video::create($this->data + $files);

        $newFiles = [
            "video_file" => UploadedFile::fake()->create('video.mp4'),
            "banner_file" => UploadedFile::fake()->image('banner.mp4')
        ];

        $video->update($newFiles);

        \Storage::assertExists("{$video->id}/{$video->thumb_file}");
        \Storage::assertExists("{$video->id}/{$video->video_file}");
        \Storage::assertExists("{$video->id}/{$video->banner_file}");
        \Storage::assertExists("{$video->id}/{$video->trailer_file}");
        \Storage::assertMissing("{$video->id}/{$files['video_file']->hashName()}");
        \Storage::assertMissing("{$video->id}/{$files['banner_file']->hashName()}");
    }

    public function testUpdateIfRollbackFiles()
    {
        \Storage::fake();
        $video = factory(Video::class)->create();
        \Event::listen(TransactionCommitted::class, function () {
            throw new TestException();
        });
        $hasError = false;

        try {
            $video->update(
                $this->data + [
                    "thumb_file" => UploadedFile::fake()->image('thumb.jpg'),
                    "video_file" => UploadedFile::fake()->create('video.mp4'),
                    "banner_file" => UploadedFile::fake()->image('banner.mp4'),
                    "trailer_file" => UploadedFile::fake()->create('trailer.mp4')
                ]
            );
        } catch (TestException $e) {
            $this->assertCount(0, \Storage::allFiles());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }
}
