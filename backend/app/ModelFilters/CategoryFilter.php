<?php

namespace App\ModelFilters;


class CategoryFilter extends AbstractFilter
{
    protected array $sortable = ['name', 'is_active', 'created_at'];

    public function search($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }
}
