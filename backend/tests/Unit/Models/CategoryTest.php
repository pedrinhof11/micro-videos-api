<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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

    public function testFillableAttribute()
    {
        $fillable =  ["name", "description", "is_active"];
        $this->assertEquals($fillable, $this->category->getFillable());
    }


    public function testIfUseTraitsAttribute()
    {
        $traits =  [SoftDeletes::class, UuidTrait::class, SerializeDateTrait::class];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testKeyTypeAttribute()
    {
        $this->assertEquals("string", $this->category->getKeyType());
    }

    public function testCastsAttribute()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->category->getIncrementing());
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($this->category->getDates() as $date) {
            $this->assertContains($date, $dates);
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
