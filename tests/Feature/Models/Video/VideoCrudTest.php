<?php

namespace Tests\Feature\Models\Video;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoCrudTest extends BaseVideoTestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Video::class, 5)->create();
        $videos = Video::all();
        $this->assertCount( 5, $videos);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'title',
                'description',
                'year_launched',
                'rating',
                'duration',
                'opened',
                'thumb_file',
                'video_file',
                'created_at',
                'updated_at',
                'deleted_at'
            ],
            array_keys($videos->first()->getAttributes())
        );
    }

    public function testCreateWithoutRelations()
    {
        $video = Video::create($this->data);
        $video->refresh();

        $uuid = Uuid::fromString($video->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($video->id, $uuid->toString());
        $this->assertDatabaseHas($video->getTable(), $this->data);
    }

    public function testCreateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = Video::create($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
            ]);
        $video->refresh();

        $uuid = Uuid::fromString($video->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($video->id, $uuid->toString());
        $this->assertDatabaseHas($video->getTable(), $this->data);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
    }

    public function testRollbackCreate()
    {
        $hasError = false;
        try{
            Video::create([
                'title' => 'Test',
                'description' => 'test Description',
                'year_launched' => 2010,
                'rating' => Video::RATING_LIST['L'],
                'duration' => 90,
                'opened' => false,
                'categories_id' => [0, 1, 2]
            ]);
        } catch (QueryException $e){
            $this->assertCount(0, Video::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testUpdateWithoutRelations()
    {
        $video = factory(Video::class)->create();
        $video->update($this->data);
        $this->assertDatabaseHas($video->getTable(), $this->data);
    }

    public function testUpdateWithRelations()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $video = factory(Video::class)->create();
        $video->update($this->data + [
                'categories_id' => [$category->id],
                'genres_id' => [$genre->id],
            ]);

        $this->assertDatabaseHas($video->getTable(), $this->data);
        $this->assertHasCategory($video->id, $category->id);
        $this->assertHasGenre($video->id, $genre->id);
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

    public function testDestroy()
    {
        $video = factory(Video::class)->create();
        $categoryTable = $video->getTable();
        $this->assertDatabaseHas($categoryTable, $video->toArray());

        $video->delete();
        $this->assertSoftDeleted($categoryTable, $video->toArray());

    }

    private function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoryId,
            'video_id' => $videoId
        ]);
    }

    private function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genreId,
            'video_id' => $videoId
        ]);
    }
}
