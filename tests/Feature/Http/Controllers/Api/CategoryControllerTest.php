<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testIndex()
    {
        $categories = factory(Category::class, 20)->create();
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson($categories->toArray());
    }

    public function testShow()
    {
        $category = factory(Category::class)->create();
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationWhenDataEmpty()
    {
        $response = $this->json('POST', route('categories.store', []));
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active', 'description'])
            ->assertJsonFragment([
                trans('validation.required', ['attribute' => 'name'])
            ]);
    }

    public function testInvalidationNameMax()
    {
        $response = $this->json('POST', route('categories.store', [
            'name' => str_repeat('a', 256)
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name'])
            ->assertJsonMissingValidationErrors(['is_active', 'description'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255])
            ]);
    }

    public function testInvalidationNameMaxAndIsActive()
    {
        $response = $this->json('POST', route('categories.store', [
            'name'      => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'is_active'])
            ->assertJsonMissingValidationErrors(['description'])
            ->assertJsonFragment([
                trans('validation.max.string', ['attribute' => 'name', 'max' => 255]),
            ])
            ->assertJsonFragment([
                trans('validation.boolean', ['attribute' => 'is active'])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store', [
            'name'      => 'test',
            'is_active' => true
        ]));

        $categoryId = $response->json('id');
        $category = Category::find($categoryId);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'is_active' => true,
                'description' => null
            ]);
    }

    public function testStoreIsActiveFalseAndDescription()
    {
        $response = $this->json('POST', route('categories.store', [
            'name'        => 'test',
            'description' => 'description',
            'is_active'   => false
        ]));

        $categoryId = $response->json('id');
        $category = Category::find($categoryId);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'is_active' => false,
                'description' => 'description'
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'Teste Description',
            'is_active' => false,
        ]);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]),
            [
                'name'        => 'test',
                'description' => 'test Updated',
                'is_active'   => true
            ]
        );

        $categoryId = $response->json('id');
        $category = Category::find($categoryId);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'is_active' => true,
                'description' => 'test Updated'
            ]);
    }

    public function testUpdateWithDescriptionEmpty()
    {
        $category = factory(Category::class)->create([
            'description' => 'Teste Description',
            'is_active' => false,
        ]);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]),
            [
                'name' => 'test Name',
                'description' => '',
                'is_active' => false
            ]
        );

        $categoryId = $response->json('id');
        $category = Category::find($categoryId);


        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => null
            ]);
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $response = $this->delete(route('categories.destroy', ['category' => $category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertSoftDeleted($category->getTable(), $category->toArray());

    }

}
