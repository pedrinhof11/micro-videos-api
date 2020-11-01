<?php

use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = Storage::getDriver()->getAdapter()->getPathPrefix();
        File::deleteDirectory($dir, true);
        $genres = \App\Models\Genre::all();
        \Illuminate\Database\Eloquent\Model::reguard();
        factory(\App\Models\Video::class, 100)
            ->create()
            ->each(function (\App\Models\Video $video) use ($genres) {
                $subGenres = $genres->random(5)->load('categories');
                $categoriesId = [];
                foreach ($subGenres as $genre) {
                    array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
                }
                $video->categories()->attach(array_unique($categoriesId));
                $video->genres()->attach($subGenres->pluck('id')->toArray());
            });
    }
}
