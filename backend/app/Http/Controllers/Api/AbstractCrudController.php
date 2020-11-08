<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class AbstractCrudController extends Controller
{
    protected Model $modelInstance;

    protected $hasPagination = true;
    protected $perPage = 15;

    protected abstract function model(): string;

    protected abstract function resource(): string;

    protected abstract function resourceCollection(): string;

    protected abstract function rulesStore(): array;

    protected abstract function rulesUpdate(): array;

    public function __construct()
    {
        $this->makeModelInstance();
    }

    public function index(Request $request)
    {
        $data = $this->hasPagination
            ? $this->modelInstance::paginate($request->get('per_page', $this->perPage))
            : $this->modelInstance::all();

        $resource = $this->resourceCollection();
        $refResource = new \ReflectionClass($resource);

        return $refResource->isSubclassOf(ResourceCollection::class)
            ? $resource::make($data)
            : $resource::collection($data);
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
        return $this->resource()::make($obj);
    }


    public function show($key)
    {
        return $this->resource()::make($this->findOrFail($key));
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
        return $this->resource()::make($model);
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
