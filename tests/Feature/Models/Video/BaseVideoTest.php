<?php

namespace Tests\Feature\Models\Video;

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class BaseVideoTest extends TestCase
{
    use DatabaseMigrations;

    protected $data;

    public function setUp(): void
    {
        parent::setUp();
        $this->data = [
            'title' => 'Test',
            'description' => 'test Description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST['L'],
            'duration' => 90,
            'opened'   => false
        ];
    }

}
