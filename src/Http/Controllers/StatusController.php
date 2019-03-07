<?php

namespace Heloufir\SimpleWorkflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Heloufir\SimpleWorkflow\Core\BuilderSpecification;
use Heloufir\SimpleWorkflow\Core\Paginator;
use Heloufir\SimpleWorkflow\Models\Status;
use Heloufir\SimpleWorkflow\Rules\StatusExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    use Paginator, BuilderSpecification;

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
        $query = Status::query();
        $query->with(['workflowsFrom', 'workflowsTo']);
        $query = $this->addSpecifications($query, $request);
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
                'unique:workflow_status,code'
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
        $status = new Status();
        $status->code = $request->get('code');
        $status->designation = $request->get('designation');
        $status->save();
        return response()->json(Status::where('id', $status->id)->with(['workflowsFrom', 'workflowsTo'])->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $query = Status::query();
        $query->where('id', $id);
        $query->with(['workflowsFrom', 'workflowsTo']);
        return response()->json($query->first(), $query->count() == 0 ? 404 : 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *      The request object
     * @param  int $id
     *      The status id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'code' => [
                'required',
                'max:255',
                new StatusExists($id),
                'unique:workflow_status,code,' . $id
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
        $status = Status::where('id', $id)->first();
        $status->code = $request->get('code');
        $status->designation = $request->get('designation');
        $status->save();
        return response()->json(Status::where('id', $status->id)->with(['workflowsFrom', 'workflowsTo'])->first(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *      The request object
     * @param  int $id
     *      The status id
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $rules = [
            'id' => [
                new StatusExists($id)
            ]
        ];
        $request->request->add(['id' => $id]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        return response()->json(Status::where('id', $id)->delete(), 200);
    }
}
