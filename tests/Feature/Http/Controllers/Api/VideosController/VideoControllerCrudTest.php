<?php

namespace Tests\Feature\Http\Controllers\Api\VideosController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Support\Arr;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestSaves;

    public function testIndex()
    {
        factory($this->model, 20)->create();
        $response = $this->get(route('videos.index'));
        $videos = $this->model::all();
        $response
            ->assertStatus(200)
            ->assertJson($videos->toArray());
    }

    public function testInvalidationWhenDataEmpty()
    {
        $response = $this->postJson(route('videos.store'), []);
        $this->assertInvalidationFields($response, [
            'title', 'description', 'year_launched', 'rating', 'duration', 'categories_id', 'genres_id'
        ], 'required');
        $response->assertJsonMissingValidationErrors(['opened']);
    }

    public function testInvalidationRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 256),
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);

    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's',
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');

    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'year_launched' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);

    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'opened' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');

    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');

    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => ['1230']
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a',
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');


        $data = [
            'genres_id' => ['1230']
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

    }

    /**
     * @throws \Exception
     */
    public function testStore()
    {
        $this->storeVideos();
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithOpenedTrue()
    {
        $data = ['opened' => true];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithOpenedFalse()
    {
        $data = ['opened' => false];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_L()
    {
        $data = ['rating' => Video::RATING_LIST['L']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_10()
    {
        $data = ['rating' => Video::RATING_LIST['10']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_12()
    {
        $data = ['rating' => Video::RATING_LIST['12']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_14()
    {
        $data = ['rating' => Video::RATING_LIST['14']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_16()
    {
        $data = ['rating' => Video::RATING_LIST['16']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testStoreWithRating_18()
    {
        $data = ['rating' => Video::RATING_LIST['18']];
        $expected = $data + ['deleted_at' => null];
        $this->storeVideos($data, $expected);
    }

    /**
     * @param array $data
     * @param array $expected
     * @throws \Exception
     */
    private function storeVideos(array $data = [], array $expected = [])
    {
        $dataRaw = $this->data + $data;
        $expectedData = Arr::except($dataRaw, ['categories_id', 'genres_id']);
        $expectedRaw = $expectedData + ['deleted_at' => null, 'opened' => false] + $expected;
        $response = $this->assertStore($dataRaw, $expectedRaw);
        $response->assertJsonStructure(['created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @throws \Exception
     */
    public function testUpdate()
    {
        $this->updateVideos();
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithOpenedTrue()
    {
        $data = ['opened' => true];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithOpenedFalse()
    {
        $data = ['opened' => false];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_L()
    {
        $data = ['rating' => Video::RATING_LIST['L']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_10()
    {
        $data = ['rating' => Video::RATING_LIST['10']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_12()
    {
        $data = ['rating' => Video::RATING_LIST['12']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_14()
    {
        $data = ['rating' => Video::RATING_LIST['14']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_16()
    {
        $data = ['rating' => Video::RATING_LIST['16']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateWithRating_18()
    {
        $data = ['rating' => Video::RATING_LIST['18']];
        $expected = $data + ['deleted_at' => null];
        $this->updateVideos($data, $expected);
    }


    /**
     * @param array $data
     * @param array $expected
     * @throws \Exception
     */
    private function updateVideos(array $data = [], array $expected = [])
    {
        $dataRaw = $this->data + $data;
        $expectedData = Arr::except($dataRaw, ['categories_id', 'genres_id']);
        $expectedRaw = $expectedData + ['deleted_at' => null] + $expected;
        $response = $this->assertUpdate($dataRaw, $expectedRaw);
        $response->assertJsonStructure(['created_at', 'updated_at', 'deleted_at']);
    }

    private function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'category_id' => $categoryId,
            'video_id' => $videoId
        ]);
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class,5)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);

        $sendData = Arr::except($this->data, ['categories_id', 'genres_id']);

        $data = $sendData + [
            'genres_id' => [$genre->id],
            'categories_id' => [$categoriesId[0]]
        ];

        $response = $this->postJson($this->routeStore(), $data);
        $this->assertHasCategory($response->json('id'), $categoriesId[0]);

        $data = $sendData + [
            'genres_id' => [$genre->id],
            'categories_id' => [$categoriesId[1],$categoriesId[3]]
        ];

        $response = $this->putJson(
            route('videos.update', ['video' => $response->json('id')]),
            $data
        );
        $this->assertDatabaseMissing('category_video', [
            'video_id' => $response->json('id'),
            'category_id' => $categoriesId[0]
        ]);
        $this->assertHasCategory($response->json('id'), $categoriesId[1]);
        $this->assertHasCategory($response->json('id'), $categoriesId[3]);
    }

    private function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'genre_id' => $genreId,
            'video_id' => $videoId
        ]);
    }

    public function testSyncGenres()
    {
        $sendData = Arr::except($this->data, ['categories_id', 'genres_id']);

        $genres = factory(Genre::class,5)->create();
        $genresId = $genres->pluck('id')->all();
        $categoryId = factory(Category::class)->create()->id;
        $genres->each(function ($genre) use ($categoryId) {
            $genre->categories()->sync($categoryId);
        });

        $data = $sendData + [
            'genres_id' => [$genresId[0]],
            'categories_id' => [$categoryId]
        ];
        $response = $this->postJson($this->routeStore(), $data);
        $this->assertHasGenre($response->json('id'), $genresId[0]);

        $data = $sendData + [
            'genres_id' => [$genresId[1], $genresId[4]],
            'categories_id' => [$categoryId]
        ];
        $response = $this->putJson(
            route('videos.update', ['video' => $response->json('id')]),
            $data
        );
        $this->assertDatabaseMissing('genre_video', [
            'video_id' => $response->json('id'),
            'genres_id' => $genresId[0]
        ]);
        $this->assertHasGenre($response->json('id'), $genresId[1]);
        $this->assertHasGenre($response->json('id'), $genresId[4]);
    }

    public function testDestroy()
    {
        $response = $this->delete(route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertSoftDeleted($this->video);
    }


}
