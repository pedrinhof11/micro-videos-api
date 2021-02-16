<?php

namespace App\ModelFilters;


class GenreFilter extends AbstractFilter
{
    protected array $sortable = ['name', 'is_active', 'created_at'];

    public function search($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }
}
