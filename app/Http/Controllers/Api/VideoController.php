<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenreRequest;
use App\Models\CastMember;
use App\Models\Genre;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRule;
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
            'opened'        => 'nullable|boolean',
            'rating'        => 'required|in:'. implode(',', Video::RATING_LIST),
            'duration'      => 'required|integer',
            'video_file'    => 'nullable|file|mimetypes:video/mp4|max:2000',
            'categories_id' => ['required', 'array', 'exists:categories,id,deleted_at,NULL'],
            'genres_id'     => ['required', 'array', 'exists:genres,id,deleted_at,NULL'],
        ];
    }

    public function store(Request $request)
    {
        $this->addRuleIfGenreHasCategories($request);
        $data = $this->validate($request, $this->rulesStore());
        $video = Video::create($data);
        $video->refresh();
        return $video;
    }

    public function update(Request $request, $key)
    {
        $video = $this->findOrFail($key);
        $this->addRuleIfGenreHasCategories($request);
        $data = $this->validate($request, $this->rulesUpdate());
        $video->update($data);
        return $video;
    }

    private function addRuleIfGenreHasCategories(Request $request) {
        $this->rules['genres_id'][] = new GenresHasCategoriesRule(
            is_array($request->get('categories_id')) ? $request->get('categories_id') : []
        );
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
