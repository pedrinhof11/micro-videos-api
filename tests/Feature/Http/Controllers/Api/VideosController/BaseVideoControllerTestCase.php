<?php


namespace Tests\Feature\Http\Controllers\Api\VideosController;

use Tests\TestCase;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class BaseVideoControllerTestCase extends TestCase
{
    use DatabaseMigrations;

    protected $model = Video::class;
    protected $video;
    protected $data;


    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory($this->model)->create();
        $this->data = [
            'title' => 'Title',
            'description' => 'Test Description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST['L'],
            'duration' => 90
        ];
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function getTable(): string
    {
        return (new $this->model)->getTable();
    }
}
