<?php

namespace Tests\Unit\Rules;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Traits\SerializeDateTrait;
use App\Models\Traits\UuidTrait;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use Mockery\Mock;
use Mockery\MockInterface;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenresHasCategoriesRuleTest extends TestCase
{
    public function testAsRuleInstance()
    {
        $rule = new GenresHasCategoriesRule([1]);
        $this->assertInstanceOf( Rule::class, $rule);
    }

    public function testCategoriesIdField()
    {
        $categoriesId = [1,1,2,2,3];
        $rule = new GenresHasCategoriesRule($categoriesId);
        $reflection = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflection->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);
        $expected = array_unique($categoriesId);
        $actual = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    public function testGenresIdField()
    {
        $genresId = [1,1,2,2,3];
        $rule = new GenresHasCategoriesRule([]);
        $rule->passes('', $genresId);
        $reflection = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflection->getProperty('genresId');
        $reflectionProperty->setAccessible(true);
        $expected = array_unique($genresId);
        $actual = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing($expected, $actual);
    }

    public function testReturnFalseWhenCategoriesIdAndGenresIdIsEmpty()
    {
        $rule = new GenresHasCategoriesRule([]);
        $passes = $rule->passes('', []);
        $this->assertFalse($passes);
    }

    public function testReturnFalseWhenCategoriesIdIsEmpty()
    {
        $rule = new GenresHasCategoriesRule([]);
        $passes = $rule->passes('', [1,2,3]);
        $this->assertFalse($passes);
    }

    public function testReturnFalseWhenGenresIdIsEmpty()
    {
        $rule = new GenresHasCategoriesRule([1,2,3]);
        $passes = $rule->passes('', []);
        $this->assertFalse($passes);
    }

    public function testReturnFalseWhenGetRowIsEmpty()
    {
        $rule = $this->createRuleMock([1]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([]));
        $passes = $rule->passes('', []);
        $this->assertFalse($passes);
    }

    public function testReturnFalseWhenHasCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([['category_id' => 1]]));
        $passes = $rule->passes('', [1]);
        $this->assertFalse($passes);
    }

    public function testPasseIsValid()
    {
        $rule = $this->createRuleMock([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([['category_id' => 1], ['category_id' => 2]]));
        $passes = $rule->passes('', [1]);
        $this->assertTrue($passes);
    }

    /**
     * @param array $categoriesId
     * @return MockInterface
     */
    private function createRuleMock(array $categoriesId) : MockInterface
    {
        return \Mockery::mock(GenresHasCategoriesRule::class, [$categoriesId],
        )->makePartial()->shouldAllowMockingProtectedMethods();
    }
}
