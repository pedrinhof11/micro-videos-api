<?php

namespace App\ModelFilters;

use App\Models\CastMember;

class CastMemberFilter extends AbstractFilter
{
    protected array $sortable = ['name', 'type', 'created_at'];

    public function search($name)
    {
        $this->where('name', 'LIKE', "%$name%");
    }

    public function type($type)
    {
        $type = (int) $type;
        if (in_array($type, CastMember::$types)) {
            $this->where('type', $type);
        }
    }
}
