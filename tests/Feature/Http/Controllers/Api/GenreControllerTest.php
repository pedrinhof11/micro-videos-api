<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;

    protected $model = Genre::class;
    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory($this->model)->create();
    }

    public function testIndex()
    {
        factory($this->model, 20)->create();
        $response = $this->get(route('genres.index'));
        $genres = $this->model::all();
        $response
            ->assertStatus(200)
            ->assertJson($genres->toArray());
    }

    public function testShow()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationWhenDataEmpty()
    {

        $response = $this->json('POST', route('genres.store', []));

        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active']);

    }

    public function testInvalidationNameMax()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => str_repeat('a', 256)
        ]));

        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
        $response->assertJsonMissingValidationErrors(['is_active']);
    }

    public function testInvalidationNameMaxAndIsActive()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
    }

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => 'test',
            'is_active' => true
        ]));

        $genreId = $response->json('id');
        $genre = Genre::find($genreId);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'name' => 'test',
                'is_active' => true
            ]);
    }

    public function testStoreIsActiveFalse()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => 'test',
            'is_active' => false
        ]));

        $genreId = $response->json('id');
        $genre = Genre::find($genreId);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => false
            ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false,
        ]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]),
            [
                'name' => 'test updated',
                'is_active' => true
            ]
        );

        $genreId = $response->json('id');
        $genre = Genre::find($genreId);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true,
                'name' => 'test updated'
            ]);
    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();
        $response = $this->delete(route('genres.destroy', ['genre' => $genre->id]));
        $response->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertSoftDeleted($genre->getTable(), $genre->toArray());
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function getTable(): string
    {
        return (new $this->model)->getTable();
    }
}
