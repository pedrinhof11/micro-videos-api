<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

abstract class AbstractCrudController extends Controller
{
    protected Model $modelInstance;

    protected abstract function model(): string;

    protected abstract function rulesStore(): array;

    protected abstract function rulesUpdate(): array;

    public function __construct()
    {
        $this->makeModelInstance();
    }

    public function index()
    {
        return $this->modelInstance::all();
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($data);
        $obj->refresh();
        return $obj;
    }


    public function show($key)
    {
        return $this->findOrFail($key);
    }

    /**
     * @param Request $request
     * @param $key
     * @return Model
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $key)
    {
        $model = $this->findOrFail($key);
        $data = $this->validate($request, $this->rulesUpdate());
        $model->update($data);
        return $model;
    }

    /**
     * @param $key
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy($key)
    {
        $model = $this->findOrFail($key);
        $model->delete();
        return response()->noContent();
    }

    protected function findOrFail($key): Model
    {
        $keyName = $this->modelInstance->getRouteKeyName();
        return $this->modelInstance->newQuery()->where($keyName, $key)->firstOrFail();
    }

    private function makeModelInstance(): void
    {
        $model = $this->model();
        $this->modelInstance = (new $model);
    }
}
