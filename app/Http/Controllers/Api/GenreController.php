<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Exception;
use Illuminate\Http\Response;

class GenreController extends Controller
{
    public function index()
    {
        return Genre::all();
    }

    public function store(GenreRequest $request)
    {
        $data = $request->validated();
        return Genre::query()->create($data);
    }

    public function show(Genre $genre)
    {
        return $genre;
    }

    public function update(GenreRequest $request, Genre $genre)
    {
        $data = $request->validated();
        $genre->update($data);
        return $genre;
    }

    /**
     * @param Genre $genre
     * @return Response
     * @throws Exception
     */
    public function destroy(Genre $genre)
    {
        $genre->delete();
        return response()->noContent();
    }
}
