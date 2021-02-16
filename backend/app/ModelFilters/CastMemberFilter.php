<?php

namespace App\ModelFilters;


class CastMemberFilter extends AbstractFilter
{
    protected array $sortable = ['name', 'type', 'created_at'];

    public function search($name)
    {
        return $this->where('name', 'LIKE', "%$name%");
    }
}
