<?php

namespace Tests\Unit\Models;

use App\Models\CastMember;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
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

    public function testFillableAttribute()
    {
        $fillable =  ["name", "type"];
        $this->assertEquals($fillable, $this->castMember->getFillable());
    }


    public function testIfUseTraitsAttribute()
    {
        $traits =  [SoftDeletes::class, UuidTrait::class, SerializeDateTrait::class];
        $castMemberTraits = array_keys(class_uses(CastMember::class));
        $this->assertEquals($traits, $castMemberTraits);
    }

    public function testKeyTypeAttribute()
    {
        $this->assertEquals("string", $this->castMember->getKeyType());
    }

    public function testCastsAttribute()
    {
        $casts = ['type' => 'integer'];
        $this->assertEquals($casts, $this->castMember->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->castMember->getIncrementing());
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($this->castMember->getDates() as $date) {
            $this->assertContains($date, $dates);
        }
        $this->assertCount(count($dates), $this->castMember->getDates());
    }
}
