<?php

namespace Tests\Feature\Models;

use App\Models\CastMember;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CastMemberTest extends TestCase
{
    use DatabaseMigrations;

    private CastMember $castMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->castMember = new CastMember();
    }

    public function testListAll()
    {
        factory(CastMember::class, 5)->create();
        $castMembers = $this->castMember->all();
        $this->assertCount( 5, $castMembers);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                "name",
                "type",
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            array_keys($castMembers->first()->getAttributes())
        );
    }

    public function testCreateCastMemberDirector()
    {
        $castMember = $this->castMember->newQuery()->create([
            'name' => 'test Create',
            'type' => CastMember::TYPE_DIRECTOR
        ]);
        $castMember->refresh();

        $uuid = Uuid::fromString($castMember->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($castMember->id, $uuid->toString());
        $this->assertEquals('test Create', $castMember->name);
        $this->assertEquals(CastMember::TYPE_DIRECTOR, $castMember->type);
    }

    public function testCreateCastMemberActor()
    {
        $castMember = $this->castMember->newQuery()->create([
            'name' => 'test Create',
            'type' => CastMember::TYPE_ACTOR
        ]);
        $castMember->refresh();

        $uuid = Uuid::fromString($castMember->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($castMember->id, $uuid->toString());
        $this->assertEquals('test Create', $castMember->name);
        $this->assertEquals(CastMember::TYPE_ACTOR, $castMember->type);
    }

    public function testUpdate()
    {
        $castMember = factory(CastMember::class)->create([
            'name' => 'test name',
            'type' => CastMember::TYPE_ACTOR
        ]);

        $data = [
            'name' => 'test_name_updated',
            'type' => CastMember::TYPE_DIRECTOR
        ];
        $castMember->update($data);

        $this->assertDatabaseHas($this->castMember->getTable(), $castMember->toArray());

    }

    public function testDestroy()
    {
        $castMember = factory(CastMember::class)->create();
        $castMemberTable = $this->castMember->getTable();
        $this->assertDatabaseHas($castMemberTable, $castMember->toArray());

        $castMember->delete();
        $this->assertSoftDeleted($castMemberTable, $castMember->toArray());
    }
}
