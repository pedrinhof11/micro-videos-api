<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

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
        $this->assertInvalidationFields($response, ['categories_id'], 'required');
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

    public function testInvalidationCategoriesIdField()
    {
        $response = $this->json('POST', route('genres.store', [
            'name' => "teste",
            'categories_id' => [123]
        ]));

        $this->assertInvalidationFields($response, ['categories_id'], 'exists');
    }

    public function testInvalidationCategoriesIdDeleted()
    {
        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    private function storeGenre(array $data = [], array $expected = [])
    {
        $categories = factory(Category::class)->create()->refresh();
        $dataRaw = [
            'name' => 'test'
        ] + $data;

        $expectedRaw = $dataRaw + ['deleted_at' => null, 'is_active' => true] + $expected;
        $response = $this->assertStore($dataRaw + [
                'categories_id' => [$categories->id],
            ], $expectedRaw);
        $response->assertJsonStructure(['created_at', 'updated_at', 'deleted_at']);
        $this->assertHasCategory($response->json('id'), $categories->id);
    }

    public function testStore()
    {
        $this->storeGenre();
    }

    public function testStoreIsActiveFalse()
    {
        $this->storeGenre(['is_active' => false]);
    }

    private function updateGenre(array $data = [], array $expected = [])
    {
        $categories = factory(Category::class)->create()->refresh();
        $dataRaw = [
                'name' => 'test updated'
            ] + $data;

        $expectedRaw = $dataRaw + ['deleted_at' => null] + $expected;
        $response = $this->assertUpdate($dataRaw + [
                'categories_id' => [$categories->id],
            ], $expectedRaw);
        $response->assertJsonStructure(['created_at', 'updated_at', 'deleted_at']);
        $this->assertHasCategory($response->json('id'), $categories->id);
    }

    private function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre', [
           'genre_id' => $genreId,
           'category_id' => $categoryId
        ]);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class,5)->create()->pluck('id')->all();
        $data = [
            'name' => 'test',
            'categories_id' => [(string) $categoriesId[0]]
        ];
        $response = $this->postJson($this->routeStore(), $data);
        $this->assertHasCategory($response->json('id'), (string) $categoriesId[0]);

        $data = [
            'name' => 'test',
            'categories_id' => [(string) $categoriesId[1], (string) $categoriesId[3]]
        ];
        $response = $this->putJson(
            route('genres.update', ['genre' => $response->json('id')]),
            $data
        );
        $this->assertDatabaseMissing('category_genre', [
            'genre_id' => $response->json('id'),
            'category_id' => (string) $categoriesId[0]
        ]);
        $this->assertHasCategory($response->json('id'), (string) $categoriesId[1]);
        $this->assertHasCategory($response->json('id'), (string) $categoriesId[3]);
    }

    public function testUpdate()
    {
        $data = [
            'is_active' => false,
        ];
        $this->updateGenre($data);
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
