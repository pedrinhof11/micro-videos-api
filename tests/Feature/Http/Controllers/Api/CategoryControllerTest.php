<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    protected $model = Category::class;
    private $category;

    protected $resourceFields = [
        'id',
        "name",
        "description",
        "is_active",
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = factory($this->model)->create();
    }

    public function testIndex()
    {
        factory($this->model, 20)->create();
        $perPage = 15;
        $response = $this->get(route('categories.index'));
        $response->assertOk()
            ->assertJson([
                'meta' => ['per_page' => $perPage]
            ]);
        $this->assertResourceCollectionPaginateStructure($response);
        $resource = CategoryResource::collection(Category::paginate($perPage));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('categories.show', ['category' => $this->category->id]));
        $response->assertOk();
        $this->assertResourceStructure($response);
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
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
        $this->assertResourceStructure($response);
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
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
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
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
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
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
        $expected = ['description' => null] + $data;
        $response = $this->assertUpdate($data, $expected);
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
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
        $resource = CategoryResource::make(Category::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }

    public function testDestroy()
    {
        $response = $this->delete(route('categories.destroy', ['category' => $this->category->id]));
        $response->assertStatus(204);
        $this->assertNull($this->model::find($this->category->id));
        $this->assertSoftDeleted($this->category);

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
