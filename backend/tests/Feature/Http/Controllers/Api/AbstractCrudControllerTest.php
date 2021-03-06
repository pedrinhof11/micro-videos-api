<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\AbstractCrudController;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use phpDocumentor\Reflection\DocBlock\Description;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class AbstractCrudControllerTest extends TestCase
{

    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    /**
     * @throws \Throwable
     */
    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }

    public function testIndex()
    {
        $stub = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);

        $request = \Mockery::mock(Request::class);
        $request->shouldReceive('get')
            ->with('per_page', 15)
            ->andReturn(15);
        $result = $this->controller->index($request)->response()->getData(true);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('links', $result);

        $expected = [$stub->toArray()];
        $this->assertEquals($expected, $result['data']);
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid');
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'test_description']);
        $result = $this->controller->store($request)->response()->getData(true);
        $expected = CategoryStub::find(1)->toArray();
        $this->assertEquals($expected, $result['data']);
    }

    public function testIfFindOrFailFetchModelInstance()
    {
        $stub = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $reflectionClass = new \ReflectionClass(AbstractCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invokeArgs($this->controller, [$stub->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowExceptionWhenInvalid()
    {
        $this->expectException(ModelNotFoundException::class);
        $reflectionClass = new \ReflectionClass(AbstractCrudController::class);
        $reflectionMethod = $reflectionClass->getMethod('findOrFail');
        $reflectionMethod->setAccessible(true);

        $reflectionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $stub = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $result = $this->controller->show($stub->id)->response()->getData(true);
        $expected = CategoryStub::find(1)->toArray();
        $this->assertEquals($expected, $result['data']);
    }

    public function testUpdate()
    {
        $stub = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $request = $this->makeRequest();
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test_name', 'description' => 'test_description']);
        $result = $this->controller->update($request, $stub->id)->response()->getData(true);
        $expected = CategoryStub::find(1)->toArray();
        $this->assertEquals($expected, $result['data']);
    }

    public function testDestroy()
    {
        $stub = CategoryStub::create(['name' => 'test_name', 'description' => 'test_description']);
        $response = $this->controller->destroy($stub->id);
        $this->createTestResponse($response)
            ->assertNoContent();
        $this->assertDeleted($stub);
    }

    private function makeRequest()
    {
        return \Mockery::mock(Request::class);
    }

}
