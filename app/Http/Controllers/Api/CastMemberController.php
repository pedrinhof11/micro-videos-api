<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;
use Illuminate\Http\Request;

class CastMemberController extends AbstractCrudController
{
    private array $rules;

    public function __construct()
    {
        parent::__construct();
        $this->rules = [
            'name' => 'required|max:255',
            'type' => 'required|in:' . implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR])
        ];
    }

    protected function model(): string
    {
        return CastMember::class;
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
