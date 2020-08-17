<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Resources\CastMemberResource;
use App\Http\Resources\CategoryResource;
use App\Models\CastMember;
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

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves, TestResources;

    protected $model = CastMember::class;
    private $castMember;

    protected $resourceFields = [
        'id',
        "name",
        "type",
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = factory($this->model)->create([
            'type' => CastMember::TYPE_DIRECTOR
        ]);
    }

    public function testIndex()
    {
        factory($this->model, 20)->create();
        $perPage = 15;
        $response = $this->getJson(route('cast-members.index'));
        $response->assertOk()
            ->assertJson([
                'meta' => ['per_page' => $perPage]
            ]);
        $this->assertResourceCollectionPaginateStructure($response);
        $resource = CastMemberResource::collection($this->model::paginate($perPage));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('cast-members.show', ['cast_member' => $this->castMember->id]));
        $response->assertOk();
        $this->assertResourceStructure($response);
        $resource = CastMemberResource::make($this->model::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationWhenDataEmpty()
    {
        $data = ['name' => '', 'type' => ''];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationNameMax()
    {
        $data = [ 'name' => str_repeat('a', 256) ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationTypeIn()
    {
        $data = [ 'type' => 'a' ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    /**
     * @throws Exception
     */
    public function testStoreCastMemberDirector()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $this->storeCastMember($data);
    }

    /**
     * @throws Exception
     */
    public function testStoreCastMemberActor()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $this->storeCastMember($data);
    }

    private function storeCastMember($data)
    {
        $expected = $data + ['deleted_at' => null];
        $response = $this->assertStore($data, $expected);
        $this->assertResourceStructure($response);
        $resource = CastMemberResource::make($this->model::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }

    /**
     * @throws Exception
     */
    public function testUpdate()
    {
        $data = [
            'name' => 'test',
            'type' => CastMember::TYPE_ACTOR
        ];
        $expected = $data + ['deleted_at' => null];
        $response = $this->assertUpdate($data, $expected);
        $this->assertResourceStructure($response);
        $resource = CastMemberResource::make($this->model::find($response->json('data.id')));
        $this->assertResource($response, $resource);
    }


    public function testDestroy()
    {
        $response = $this->delete(route('cast-members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertNoContent();
        $this->assertNull($this->model::find($this->castMember->id));
        $this->assertSoftDeleted($this->castMember);

    }

    protected function routeStore()
    {
        return route('cast-members.store');
    }

    protected function routeUpdate()
    {
        return route('cast-members.update', ['cast_member' => $this->castMember->id]);
    }

    protected function getTable(): string
    {
        return (new $this->model)->getTable();
    }
}
