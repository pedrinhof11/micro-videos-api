<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\CastMember;
use App\Models\Genre;
use App\Models\Video;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VideoController extends AbstractCrudController
{
    private array $rules;

    public function __construct()
    {
        parent::__construct();
        $this->rules = [
            'title'         => 'required|max:255',
            'description'   => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened'        => 'boolean',
            'rating'        => 'required|in:'. implode(',', Video::RATING_LIST),
            'duration'      => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id'     => 'required|array|exists:genres,id',
        ];
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());

        $video = \DB::transaction(function () use ($request, $data) {
            $video = $this->model()::create($data);
            $this->handleRelations($video, $request);
            return $video;
        });

        $video->refresh();
        return $video;
    }

    public function update(Request $request, $key)
    {
        $video = $this->findOrFail($key);
        $data = $this->validate($request, $this->rulesUpdate());
        $video = \DB::transaction(function () use ($request, $data, $video) {
            $video->update($data);
            $this->handleRelations($video, $request);
            return $video;
        });
        return $video;
    }

    protected function handleRelations($video, Request $request)
    {
        $video->categories()->sync($request->get('categories_id'));
        $video->genres()->sync($request->get('genres_id'));
    }

    protected function model(): string
    {
        return Video::class;
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
