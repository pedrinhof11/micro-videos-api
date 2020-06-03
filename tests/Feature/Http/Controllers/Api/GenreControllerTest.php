<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $genres = factory(Genre::class, 20)->create();
        $response = $this->get(route('genres.index'));

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
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);
    }

    public function testInvalidationNameMax()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => str_repeat('a', 256)
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    public function testInvalidationNameMaxAndIsActive()
    {
        $response = $this->json('POST', route('genres.store', [
            'name'      => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255]),
            ])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('genres.store', [
            'name'      => 'test',
            'is_active' => true
        ]));

        $genreId = $response->json('id');
        $genre = Genre::find($genreId);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'name'      => 'test',
                'is_active' => true
            ]);
    }

    public function testStoreIsActiveFalse()
    {
        $response = $this->json('POST', route('genres.store', [
            'name'        => 'test',
            'is_active'   => false
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
                'name'        => 'test updated',
                'is_active'   => true
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

}
