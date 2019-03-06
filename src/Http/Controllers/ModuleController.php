<?php

namespace Heloufir\SimpleWorkflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Heloufir\SimpleWorkflow\Core\Paginator;
use Heloufir\SimpleWorkflow\Models\Module;
use Heloufir\SimpleWorkflow\Rules\ModuleExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ModuleController extends Controller
{
    use Paginator;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *      The request object
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function index(Request $request): JsonResponse
    {
        $query = Module::query();
        $query->with(['workflows']);
        return response()->json(self::paginate($query, $request), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *      The request object
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function store(Request $request): JsonResponse
    {
        $rules = [
            'model' => [
                'required',
                'max:500',
                'unique:workflow_modules,model'
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $module = new Module();
        $module->model = $request->get('model');
        $module->save();
        return response()->json(Module::where('id', $module->id)->with(['workflows'])->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $query = Module::query();
        $query->where('id', $id);
        $query->with(['workflows']);
        return response()->json($query->first(), $query->count() == 0 ? 404 : 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *      The request object
     * @param  int $id
     *      The module id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'model' => [
                'required',
                'max:500',
                new ModuleExists($id),
                'unique:workflow_modules,model,' . $id
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $module = Module::where('id', $id)->first();
        $module->model = $request->get('model');
        $module->save();
        return response()->json(Module::where('id', $module->id)->with(['workflows'])->first(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *      The request object
     * @param  int $id
     *      The module id
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $rules = [
            'id' => [
                new ModuleExists($id)
            ]
        ];
        $request->request->add(['id' => $id]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        return response()->json(Module::where('id', $id)->delete(), 200);
    }
}
