<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\AbstractCrudController;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends AbstractCrudController
{
    protected function model(): string
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name'        => 'required|max:255',
            'description' => 'nullable|string'
        ];
    }
}
