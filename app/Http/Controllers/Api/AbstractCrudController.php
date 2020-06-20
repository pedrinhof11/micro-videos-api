<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

abstract class AbstractCrudController extends Controller
{
    protected Model $modelInstance;

    protected abstract function model(): string;

    protected abstract function rulesStore();

    public function __construct()
    {
        $this->makeModelInstance();
    }

    public function index()
    {
        return $this->model()::all();
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, $this->rulesStore());
        $obj = $this->model()::create($data);
        $obj->refresh();
        return $obj;
    }

    protected function findOrFail($id)
    {
        $keyName = $this->modelInstance->getRouteKeyName();
        return $this->modelInstance->newQuery()->where($keyName, $id)->firstOrFail();
    }

    private function makeModelInstance()
    {
        $model = $this->model();
        $this->modelInstance = (new $model);
    }

//    public function show(Category $category)
//    {
//        return $category;
//    }

//    public function update(CategoryRequest $request, Category $category)
//    {
//        $data = $request->validated();
//        $category->update($data);
//        return $category;
//    }
//
//    /**
//     * @param Category $category
//     * @return Response
//     * @throws Exception
//     */
//    public function destroy(Category $category)
//    {
//        $category->delete();
//        return response()->noContent();
//    }
}
