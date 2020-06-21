<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\CastMember;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations, TestSaves;

    protected $model = CastMember::class;
    private $castMember;

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
        $response = $this->getJson(route('cast-members.index'));
        $castMember = $this->model::all();
        $response
            ->assertStatus(200)
            ->assertJson($castMember->toArray());
    }

    public function testShow()
    {
        $response = $this->get(route('cast-members.show', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(200)
            ->assertJson($this->castMember->toArray());
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
        $response->assertJsonStructure([
            'created_at',  'updated_at', 'deleted_at'
        ]);
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
        $response->assertJsonStructure([
            'id', 'name', 'type', 'created_at',  'updated_at', 'deleted_at'
        ]);
    }


    public function testDestroy()
    {
        $response = $this->delete(route('cast-members.destroy', ['cast_member' => $this->castMember->id]));
        $response->assertStatus(204);
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
