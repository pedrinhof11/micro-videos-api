<?php


namespace Tests\Traits;


use Illuminate\Http\UploadedFile;

trait TestUploads
{
    protected abstract function routeStore();

    protected abstract function routeUpdate();

    protected function assertFileExistsInStorage($model, array $files)
    {
        foreach ($files as $file) {
            \Storage::assertExists($model->getFileRelativePath($file->hashName()));
        }
    }

    protected function assertInvalidationFile($field, $extension, $maxSize, $accept)
    {
        $routes = [
            'POST' => $this->routeStore(),
            'PUT' => $this->routeUpdate()
        ];
        foreach ($routes as $method => $route) {
            $this->invalidationFile($method, $route, $field);

            $this->invalidationMimeTypes($method, $route, $field, $extension, $accept);

            $this->invalidationMaxSize($method, $route, $field, $extension, $maxSize);
        }
    }

    private function invalidationFile ($method, $route, $field) {
        $response = $this->json($method, $route, [ $field => 'asdf' ]);
        $this->assertInvalidationFields($response, [$field], 'file');
    }

    private function invalidationMimeTypes ($method, $route, $field, $extension, $accept) {
        $file = UploadedFile::fake()->create("{$field}.1{$extension}");
        $response = $this->json($method, $route, [ $field => $file ]);
        $this->assertInvalidationFields($response, [$field], 'mimetypes', ['values' => $accept]);
    }

    private function invalidationMaxSize ($method, $route, $field, $extension, $maxSize) {
        $file = UploadedFile::fake()->create("{$field}.{$extension}")->size($maxSize+1);
        $response = $this->json($method, $route, [ $field => $file ]);
        $this->assertInvalidationFields($response, [$field], 'max.file', ['max' => $maxSize]);
    }
}
