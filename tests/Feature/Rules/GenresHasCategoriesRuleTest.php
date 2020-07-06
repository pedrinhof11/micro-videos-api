<?php

namespace Tests\Feature\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Mockery\Mock;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenresHasCategoriesRuleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Collection
     */
    private $categories;
    /**
     * @var Collection
     */
    private $genres;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categories = factory(Category::class, 4)->create();
        $this->genres = factory(Genre::class, 2)->create();

        $this->genres[0]->categories()->sync([
            (string) $this->categories[0]->id,
            (string) $this->categories[1]->id
        ]);
        $this->genres[1]->categories()->sync([
            (string) $this->categories[2]->id
        ]);
    }

    public function testPasseIsValid()
    {
        $rule = new GenresHasCategoriesRule([(string) $this->categories[2]->id]);
        $passes = $rule->passes('', [
            (string) $this->genres[1]->id
        ]);
        $this->assertTrue($passes);

        $rule = new GenresHasCategoriesRule([
            (string) $this->categories[0]->id,
            (string) $this->categories[1]->id
        ]);
        $passes = $rule->passes('', [
            (string) $this->genres[0]->id
        ]);
        $this->assertTrue($passes);

        $rule = new GenresHasCategoriesRule([
            (string) $this->categories[0]->id,
            (string) $this->categories[1]->id,
            (string) $this->categories[2]->id
        ]);
        $passes = $rule->passes('', [
            (string) $this->genres[0]->id,
            (string) $this->genres[1]->id
        ]);
        $this->assertTrue($passes);
    }

    public function testPasseIsInvalid()
    {
        $rule = new GenresHasCategoriesRule([(string) $this->categories[0]->id]);
        $passes = $rule->passes('', [
            (string) $this->genres[1]->id
        ]);
        $this->assertFalse($passes);

        $rule = new GenresHasCategoriesRule([(string) $this->categories[0]->id, (string) $this->categories[3]->id]);
        $passes = $rule->passes('', [
            (string) $this->genres[0]->id,
            (string) $this->genres[1]->id
        ]);
        $this->assertFalse($passes);
    }

}
