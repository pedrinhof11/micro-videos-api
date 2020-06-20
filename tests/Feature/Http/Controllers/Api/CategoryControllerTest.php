<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $model = Category::class;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory($this->model)->create();
    }

    public function testIndex()
    {
        factory($this->model, 20)->create();
        $response = $this->get(route('categories.index'));
        $categories = Category::all();
        $response
            ->assertStatus(200)
            ->assertJson($categories->toArray());
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
        $response->assertStatus(200)
            ->assertJson($this->category->toArray());
    }

    public function testInvalidationWhenDataEmpty()
    {
        $response = $this->json('POST', route('categories.store', []));
        $this->assertInvalidationFields($response, ['name'], 'required');
        $response->assertJsonMissingValidationErrors(['is_active', 'description']);
    }

    public function testInvalidationNameRequired()
    {
        $data = [ 'name' => '' ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationNameMax()
    {
        $data = [ 'name' => str_repeat('a', 256) ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationIsActive()
    {
        $data = [ 'is_active' => 'a' ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationNameMaxAndIsActive()
    {
        $response = $this->json('POST', route('categories.store', [
            'name'      => str_repeat('a', 256),
            'is_active' => 'a'
        ]));

        $this->assertInvalidationFields($response, ['name'], 'max.string', ['max' => 255]);
        $this->assertInvalidationFields($response, ['is_active'], 'boolean');
        $response->assertJsonMissingValidationErrors(['description']);
    }

    /**
     * @throws Exception
     */
    public function testStore()
    {
        $data = [
            'name'      => 'test',
            'is_active' => true
        ];
        $expected = $data + ['description' => null, 'deleted_at' => null];
        $response = $this->assertStore($data, $expected);
        $response->assertJsonStructure([
            'created_at',  'updated_at', 'deleted_at'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testStoreIsActiveFalseAndDescription()
    {
        $data =[
            'name'        => 'test',
            'description' => 'description',
            'is_active'   => false
        ];
        $expected = $data + ['deleted_at' => null];
        $response = $this->assertStore($data, $expected);
        $response->assertJsonStructure([
            'created_at',  'updated_at', 'deleted_at'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $this->category = factory($this->model)->create([
            'description' => 'Teste Description',
            'is_active' => false,
        ]);
        $data = [
            'name'        => 'test',
            'description' => 'test Updated',
            'is_active'   => true
        ];
        $expected = $data + ['deleted_at' => null];
        $response = $this->assertUpdate($data, $expected);
        $response->assertJsonStructure([
            'id', 'name', 'description', 'is_active', 'created_at',  'updated_at', 'deleted_at'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdateWhenDescriptionEmpty()
    {
        $data = [
            'name'        => 'test Name',
            'description' => '',
            'is_active'   => false
        ];
        $expected = array_merge($data + ['description' => null]);
        $response = $this->assertUpdate($data, $expected);
        $response->assertJsonStructure([
            'id', 'name', 'description', 'is_active', 'created_at',  'updated_at', 'deleted_at'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testUpdateWhenDescriptionNotNull()
    {
        $data = [
            'name'        => 'test Name',
            'description' => 'description not null',
            'is_active'   => false
        ];
        $expected = array_merge($data + ['description' => 'description not null']);
        $response = $this->assertUpdate($data, $expected);
        $response->assertJsonStructure([
            'id', 'name', 'description', 'is_active', 'created_at',  'updated_at', 'deleted_at'
        ]);
    }

    public function testDestroy()
    {
        $response = $this->delete(route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull(Category::find($this->category->id));
        $this->assertSoftDeleted($this->category->getTable(), $this->category->toArray());

    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate()
    {
        return route('categories.update', ['category' => $this->category->id]);
    }

    protected function getTable(): string
    {
        return (new $this->model)->getTable();
    }
}
