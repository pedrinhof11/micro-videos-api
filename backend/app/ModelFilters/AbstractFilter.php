<?php


namespace App\ModelFilters;


use EloquentFilter\ModelFilter;
use Illuminate\Support\Str;

abstract class AbstractFilter extends ModelFilter
{
    protected array $sortable = [];

    protected function setup()
    {
        $this->blacklistMethod('isSortable');
        $noSort =  $this->input('sort', "") === "";
        if($noSort) {
            $this->orderByDesc('created_at');
        }
    }

    public function sort($column)
    {
        if(method_exists($this, $method = "sortBy" . Str::studly($column))) {
            $this->$method();
        }
        if($this->isSortable($column)) {
            $dir = strtolower($this->input("dir") == 'asc' ? 'asc' : 'desc');
            $this->orderBy($column, $dir);
        }
    }

    protected function isSortable($column)
    {
        return in_array($column, $this->sortable);
    }
}
