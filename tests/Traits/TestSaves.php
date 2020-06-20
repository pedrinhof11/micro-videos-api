<?php


namespace Tests\Traits;


use Exception;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;

trait TestSaves
{
    protected abstract function routeStore();

    protected abstract function routeUpdate();

    protected abstract function getTable(): string;

    /**
     * @param array $data
     * @param array $expectedData
     * @return TestResponse
     * @throws Exception
     */
    protected function assertUpdate(array $data, array $expectedData): TestResponse
    {
        /** @var $response TestResponse */
        $response = $this->putJson($this->routeUpdate(), $data);

        $this->assertStatusCode($response, Response::HTTP_OK);
        $this->assertJsonExpectedData($response, $expectedData);
        $this->assertInDatabase($response);
        return $response;
    }

    /**
     * @param array $data
     * @param array $expectedData
     * @return TestResponse
     * @throws Exception
     */
    protected function assertStore(array $data, array $expectedData): TestResponse
    {
        /** @var $response TestResponse */
        $response = $this->postJson($this->routeStore(), $data);

        $this->assertStatusCode($response, Response::HTTP_CREATED);
        $this->assertJsonExpectedData($response, $expectedData);
        $this->assertInDatabase($response);
        return $response;
    }

    private function assertJsonExpectedData(TestResponse $response, array $expectedData)
    {
        $response
            ->assertJson($expectedData + ['id' => $response->json('id')]);
    }

    private function assertInDatabase($response)
    {
        $this->assertDatabaseHas($this->getTable(), $response->json());
    }

    /**
     * @param TestResponse $response
     * @param int $expectedStatus
     * @throws Exception
     */
    private function assertStatusCode(TestResponse $response, int $expectedStatus)
    {
        if ($response->status() !== $expectedStatus) {
            throw new Exception("Expected status code {$expectedStatus} but received {$response->status()}:\n{$response->content()}");
        }

        $response->assertStatus($expectedStatus);
    }
}
