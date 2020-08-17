<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GenreController extends AbstractCrudController
{
    private array $rules = [
        'name'          => 'required|max:255',
        'is_active'     => 'nullable|boolean',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
    ];

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());
        $genre = \DB::transaction(function () use ($request, $data) {
            /** @var Genre $genre */
            $genre = $this->model()::create($data);
            $genre->categories()->sync($request->get('categories_id'));
            return $genre;
        });
        $genre->refresh();
        return $this->resource()::make($genre);
    }

    /**
     * @param Request $request
     * @param $key
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Throwable
     */
    public function update(Request $request, $key)
    {
        $genre = $this->findOrFail($key);
        $data = $this->validate($request, $this->rulesUpdate());
        $genre = \DB::transaction(function () use ($request, $data, $genre) {
            $genre->update($data);
            $genre->categories()->sync($request->get('categories_id'));
            return $genre;
        });

        return  $this->resource()::make($genre);
    }

    protected function model(): string
    {
        return Genre::class;
    }

    protected function resource(): string
    {
        return GenreResource::class;
    }

    protected function resourceCollection(): string
    {
        return $this->resource();
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
