<?php


namespace Tests\Traits;


use Illuminate\Http\Response;
use Illuminate\Testing\TestResponse;

trait TestValidations
{
    protected abstract function routeStore();

    protected abstract function routeUpdate();

    protected function assertInvalidationInStoreAction(array $data, string $rule, array $ruleParams = [])
    {
        $response = $this->json('POST', $this->routeStore(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
        return $response;
    }

    protected function assertInvalidationInUpdateAction(array $data, string $rule, array $ruleParams = [])
    {
        $response = $this->json('PUT', $this->routeUpdate(), $data);
        $fields = array_keys($data);
        $this->assertInvalidationFields($response, $fields, $rule, $ruleParams);
        return $response;
    }

    protected function assertInvalidationFields(
        TestResponse $response,
        array $fields,
        string $rule,
        array $ruleParams = []
    )
    {
        $response
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment($this->ruleFieldFragment($rule, $fields, $ruleParams));
    }


    private function ruleFieldFragment(string $rule, array $fields, array $ruleParams = [])
    {
        $fragment = [];
        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            array_push($fragment, trans("validation.{$rule}",  ['attribute' => $fieldName] + $ruleParams));
        }
        return $fragment;
    }
}
