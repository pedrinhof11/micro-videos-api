<?php


namespace Tests\Traits;


use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Testing\TestResponse;

trait TestResources
{

    protected function assertResourceCollectionPaginateStructure(TestResponse $response)
    {
        $response->assertJsonStructure([
            'data' => [
               '*' => $this->resourceFields
            ],
            'links' => [],
            'meta' => []
        ]);
    }

    protected function assertResourceCollectionStructure(TestResponse $response)
    {
        $response->assertJsonStructure([
            'data' => [
                '*' => $this->resourceFields
            ]
        ]);
    }

    protected function assertResourceStructure(TestResponse $response)
    {
        $response->assertJsonStructure([
            'data' => $this->resourceFields
        ]);
    }

    protected function assertResource(TestResponse $response, JsonResource $resource)
    {
        $response->assertJson($resource->response()->getData(true));
    }
}
