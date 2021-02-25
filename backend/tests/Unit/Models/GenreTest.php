<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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

    public function testFillableAttribute()
    {
        $fillable =  ["name", "is_active"];
        $this->assertEquals($fillable, $this->genre->getFillable());
    }


    public function testIfUseTraitsAttribute()
    {
        $traits =  [SoftDeletes::class, UuidTrait::class, SerializeDateTrait::class, Filterable::class];
        $genreTraits = array_keys(class_uses(Genre::class));
        $this->assertEquals($traits, $genreTraits);
    }

    public function testKeyTypeAttribute()
    {
        $this->assertEquals("string", $this->genre->getKeyType());
    }

    public function testCastsAttribute()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEquals($casts, $this->genre->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->genre->getIncrementing());
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($this->genre->getDates() as $date) {
            $this->assertContains($date, $dates);
        }
        $this->assertCount(count($dates), $this->genre->getDates());
    }
}
