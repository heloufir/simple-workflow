<?php

namespace Heloufir\SimpleWorkflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Heloufir\SimpleWorkflow\Core\Paginator;
use Heloufir\SimpleWorkflow\Models\Action;
use Heloufir\SimpleWorkflow\Rules\ActionExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActionController extends Controller
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
        $query = Action::query();
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
            'code' => [
                'required',
                'max:255',
                'unique:workflow_actions,code'
            ],
            'designation' => [
                'required',
                'max:255'
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $action = new Action();
        $action->code = $request->get('code');
        $action->designation = $request->get('designation');
        $action->save();
        return response()->json(Action::where('id', $action->id)->with(['workflows'])->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $query = Action::query();
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
     *      The action id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'code' => [
                'required',
                'max:255',
                new ActionExists($id),
                'unique:workflow_actions,code,' . $id
            ],
            'designation' => [
                'required',
                'max:255'
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $action = Action::where('id', $id)->first();
        $action->code = $request->get('code');
        $action->designation = $request->get('designation');
        $action->save();
        return response()->json(Action::where('id', $action->id)->with(['workflows'])->first(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *      The request object
     * @param  int $id
     *      The profile id
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $rules = [
            'id' => [
                new ActionExists($id)
            ]
        ];
        $request->request->add(['id' => $id]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        return response()->json(Action::where('id', $id)->delete(), 200);
    }
}
