<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;

class CategoryController extends AbstractCrudController
{

    private array $rules = [
        'name'        => 'required|max:255',
        'description' => 'nullable|string',
        'is_active'   => 'nullable|boolean'
    ];

    protected function model(): string
    {
        return Category::class;
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
