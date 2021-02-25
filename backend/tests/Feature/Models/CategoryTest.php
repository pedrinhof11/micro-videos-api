<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Lazy\LazyUuidFromString;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

    public function testListAll()
    {
        factory(Category::class, 5)->create();
        $categories = $this->category->all();
        $this->assertCount( 5, $categories);
        $this->assertEqualsCanonicalizing(
            [
                'id',
                "name",
                "description",
                "is_active",
                'created_at',
                'updated_at',
                'deleted_at',
            ],
            array_keys($categories->first()->getAttributes())
        );
    }

    public function testCreateOnlyName()
    {
        $category = $this->category->newQuery()->create([
            'name' => 'test Create'
        ]);
        $category->refresh();

        $uuid = Uuid::fromString($category->id);

        $this->assertTrue(Uuid::isValid($category->id));
        $this->assertInstanceOf(LazyUuidFromString::class, $uuid);
        $this->assertSame($category->id, $uuid->toString());
        $this->assertEquals('test Create', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testCreate()
    {
        $category = $this->category->newQuery()->create([
            'name' => 'test Create',
            'description' => 'test description',
            'is_active' => false
        ]);
        $category->refresh();

        $uuid = Uuid::fromString($category->id);

        $this->assertTrue(Uuid::isValid($category->id));
        $this->assertInstanceOf(LazyUuidFromString::class, $uuid);
        $this->assertSame($category->id, $uuid->toString());
        $this->assertEquals('test Create', $category->name);
        $this->assertEquals('test description', $category->description);
        $this->assertFalse($category->is_active);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'name' => 'test name',
            'description' => 'test description',
            'is_active' => false
        ]);

        $data = [
            'name' => 'test_name_updated',
            'description' => 'test_description_updated',
            'is_active' => true
        ];
        $category->update($data);

        $this->assertDatabaseHas($this->category->getTable(), $category->toArray());
    }

    public function testDestroy()
    {
        $category = factory(Category::class)->create();
        $categoryTable = $this->category->getTable();
        $this->assertDatabaseHas($categoryTable, $category->toArray());

        $category->delete();
        $this->assertSoftDeleted($categoryTable, $category->toArray());

    }
}
