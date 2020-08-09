<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UploadFilesTrait;
use App\Models\Traits\UuidTrait;
use App\Models\Video;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    private $video;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = new Video();
    }

    public function testFillableAttribute()
    {
        $fillable =  [
            "title",
            "description",
            "year_launched",
            "opened",
            "rating",
            "duration",
            "video_file",
            "thumb_file"
        ];
        $this->assertEquals($fillable, $this->video->getFillable());
    }


    public function testIfUseTraitsAttribute()
    {
        $traits = [SoftDeletes::class, UuidTrait::class, SerializeDateTrait::class, UploadFilesTrait::class];
        $videoTraits = array_keys(class_uses(Video::class));
        $this->assertEquals($traits, $videoTraits);
    }

    public function testKeyTypeAttribute()
    {
        $this->assertEquals("string", $this->video->getKeyType());
    }

    public function testCastsAttribute()
    {
        $casts = [
            "year_launched" => 'integer',
            "opened" => 'boolean',
            "duration" => 'integer'
        ];
        $this->assertEquals($casts, $this->video->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->video->getIncrementing());
    }

    public function testDatesAttribute()
    {
        $dates = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($this->video->getDates() as $date) {
            $this->assertContains($date, $dates);
        }
        $this->assertCount(count($dates), $this->video->getDates());
    }
}
