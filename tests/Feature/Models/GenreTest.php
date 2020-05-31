<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    private $genre;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = new Genre();
    }

    public function testListAll()
    {
        factory(Genre::class, 5)->create();
        $genres = $this->genre->all();
        $this->assertCount( 5, $genres);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                "name",
                "is_active",
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            array_keys($genres->first()->getAttributes())
        );
    }

    public function testCreateOnlyName()
    {
        $genre = $this->genre->newQuery()->create([
            'name' => 'test Create'
        ]);
        $genre->refresh();

        $uuid = Uuid::fromString($genre->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($genre->id, $uuid->toString());
        $this->assertEquals('test Create', $genre->name);
        $this->assertNull($genre->description);
        $this->assertTrue($genre->is_active);
    }

    public function testCreate()
    {
        $genre = $this->genre->newQuery()->create([
            'name' => 'test Create',
            'is_active' => false
        ]);
        $genre->refresh();

        $uuid = Uuid::fromString($genre->id);

        $uuid = Uuid::fromString($genre->id);

        $this->assertInstanceOf(Uuid::class, $uuid);
        $this->assertSame($genre->id, $uuid->toString());
        $this->assertEquals('test Create', $genre->name);
        $this->assertFalse($genre->is_active);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'name' => 'test name',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test_name_updated',
            'is_active' => true
        ];
        $genre->update($data);

        $this->assertDatabaseHas($this->genre->getTable(), $genre->toArray());

    }

    public function testDestroy()
    {
        $genre = factory(Genre::class)->create();
        $genreTable = $this->genre->getTable();
        $this->assertDatabaseHas($genreTable, $genre->toArray());

        $genre->delete();
        $this->assertSoftDeleted($genreTable, $genre->toArray());

    }
}
