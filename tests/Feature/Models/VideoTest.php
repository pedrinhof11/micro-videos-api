<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    public function testRollbackCreate()
    {
        $hasError = false;
        try {
            Video::create([
                'title' => 'Test',
                'description' => 'test Description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST['L'],
                'duration' => 90,
                'categories_id' => [1,2,3]
            ]);
        } catch (QueryException $e) {
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $video = factory(Video::class)->create();
        $hasError = false;
        try {
            $video->update([
                'title' => 'Test',
                'description' => 'test Description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST['L'],
                'duration' => 90,
                'categories_id' => [1,2,3]
            ]);
        } catch (QueryException $e) {
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }
}
