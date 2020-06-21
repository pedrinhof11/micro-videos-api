<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\AbstractCrudController;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends AbstractCrudController
{
    private array $rules = [
        'name'        => 'required|max:255',
        'description' => 'nullable|string'
    ];

    protected function model(): string
    {
        return CategoryStub::class;
    }

    protected function rulesStore(): array
    {
        return $this->rules;
    }

    protected function rulesUpdate(): array
    {
        return $this->rules;
    }
}
