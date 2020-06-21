<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Exception;
use Illuminate\Http\Response;

class GenreController extends AbstractCrudController
{
    private array $rules = [
        'name'      => 'required|max:255',
        'is_active' => 'nullable|boolean'
    ];

    protected function model(): string
    {
        return Genre::class;
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
