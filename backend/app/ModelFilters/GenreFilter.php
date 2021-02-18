<?php

namespace App\ModelFilters;

use Illuminate\Database\Eloquent\Builder;

class GenreFilter extends AbstractFilter
{
    protected array $sortable = ['name', 'is_active', 'created_at'];

    public function search($name)
    {
        $this->where('name', 'LIKE', "%$name%");
    }

    public function categories($categories)
    {
        $this->whereHas('categories', function (Builder $query) use ($categories) {
            $query->whereIn('id', $categories)
                ->orWhereIn('name', $categories)
        });
    }
}
