<?php


namespace Tests\Traits;


use Exception;
use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

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
        $this->assertInDatabase($response, $expectedData);
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
        $this->assertInDatabase($response, $expectedData);
        $this->assertJsonExpectedData($response, $expectedData);
        return $response;
    }

    private function assertJsonExpectedData(TestResponse $response, array $expectedData)
    {
        $response
            ->assertJsonFragment($expectedData + ['id' => $this->getIdFromResponse($response)]);
    }

    private function assertInDatabase(TestResponse $response, array $expectedData)
    {
        $this->assertDatabaseHas($this->getTable(), $expectedData + ['id' => $this->getIdFromResponse($response)]);
    }

    private function getDataFromResponse(TestResponse $response)
    {
        return $response->json('data') ?? $response->json();
    }

    private function getIdFromResponse(TestResponse $response)
    {
        return $response->json('id') ?? $response->json('data.id');
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
