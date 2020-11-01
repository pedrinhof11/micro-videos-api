<?php

namespace App\Rules;

use App\Models\Genre;
use Illuminate\Contracts\Validation\Rule;

class GenresHasCategoriesRule implements Rule
{
    /**
     * @var array
     */
    private array $categoriesId;
    private array $genresId;

    /**
     * Create a new rule instance.
     *
     * @param array $categoriesId
     */
    public function __construct(array $categoriesId)
    {
        $this->categoriesId = array_unique($categoriesId);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(!is_array($value)) {
            $value = [];
        }
        $this->genresId = array_unique($value);
        if(empty($this->categoriesId) || empty($this->genresId)){
            return false;
        }

        $categoriesFound = [];
        foreach ($this->genresId as $genreId) {
            $rows = $this->getRows($genreId, $this->categoriesId);
            if($rows->isEmpty()) {
                return false;
            }
            array_push($categoriesFound, ...$rows->pluck('category_id')->toArray());
        }
        if (count($this->categoriesId) !== count(array_unique($categoriesFound))) {
            return false;
        }

        return true;
    }

    protected function getRows($genreId, $categoriesId)
    {
        return \DB::table('category_genre')
            ->where('genre_id', $genreId)
            ->whereIn('category_id', $categoriesId)
            ->get();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.genre_has_categories');
    }
}
