<?php

namespace Heloufir\SimpleWorkflow\Http\Controllers;

use App\Http\Controllers\Controller;
use Heloufir\SimpleWorkflow\Core\BuilderSpecification;
use Heloufir\SimpleWorkflow\Core\Paginator;
use Heloufir\SimpleWorkflow\Core\WorkflowSystem;
use Heloufir\SimpleWorkflow\Models\Workflow;
use Heloufir\SimpleWorkflow\Rules\ActionExists;
use Heloufir\SimpleWorkflow\Rules\ModuleExists;
use Heloufir\SimpleWorkflow\Rules\StatusExists;
use Heloufir\SimpleWorkflow\Rules\WorkflowExists;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkflowController extends Controller
{
    use Paginator, WorkflowSystem, BuilderSpecification;

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
        $query = Workflow::query();
        $query->with(['action', 'statusFrom', 'statusTo', 'module']);
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
                'unique:workflows,code'
            ],
            'designation' => [
                'required',
                'max:255'
            ],
            'action' => [
                'required',
                'integer',
                new ActionExists
            ],
            'module' => [
                'required',
                'integer',
                new ModuleExists
            ],
            'statusFrom' => [
                'required',
                'integer',
                new StatusExists
            ],
            'statusTo' => [
                'required',
                'integer',
                new StatusExists
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $workflow = new Workflow();
        $workflow->code = $request->get('code');
        $workflow->designation = $request->get('designation');
        $workflow->refAction = $request->get('action');
        $workflow->refModule = $request->get('module');
        $workflow->refStatusFrom = $request->get('statusFrom');
        $workflow->refStatusTo = $request->get('statusTo');
        $workflow->save();
        return response()->json(Workflow::where('id', $workflow->id)->with(['action', 'statusFrom', 'statusTo', 'module'])->first(), 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $query = Workflow::query();
        $query->where('id', $id);
        $query->with(['action', 'statusFrom', 'statusTo', 'module']);
        return response()->json($query->first(), $query->count() == 0 ? 404 : 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request $request
     *      The request object
     * @param  int $id
     *      The workflow id
     *
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rules = [
            'code' => [
                'required',
                'max:255',
                new WorkflowExists($id),
                'unique:workflows,code,' . $id
            ],
            'designation' => [
                'required',
                'max:255'
            ],
            'action' => [
                'required',
                'integer',
                new ActionExists
            ],
            'module' => [
                'required',
                'integer',
                new ModuleExists
            ],
            'statusFrom' => [
                'required',
                'integer',
                new StatusExists
            ],
            'statusTo' => [
                'required',
                'integer',
                new StatusExists
            ]
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        $workflow = Workflow::where('id', $id)->first();
        $workflow->code = $request->get('code');
        $workflow->designation = $request->get('designation');
        $workflow->refAction = $request->get('action');
        $workflow->refModule = $request->get('module');
        $workflow->refStatusFrom = $request->get('statusFrom');
        $workflow->refStatusTo = $request->get('statusTo');
        $workflow->save();
        return response()->json(Workflow::where('id', $workflow->id)->with(['action', 'statusFrom', 'statusTo', 'module'])->first(), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *      The request object
     * @param  int $id
     *      The workflow id
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $rules = [
            'id' => [
                new WorkflowExists($id)
            ]
        ];
        $request->request->add(['id' => $id]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(collect($validator->getMessageBag())->flatten()->toArray(), 403);
        }
        return response()->json(Workflow::where('id', $id)->delete(), 200);
    }

    /**
     * Execute the workflow system on an object
     *
     * @param Request $request
     *      The request object
     * @param int $workflow
     *      The workflow id
     * @param int $object
     *      The object id
     *
     * @return JsonResponse
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function doWorkflow(Request $request, int $workflow, int $object): JsonResponse
    {
        $user = $request->user() != null ? $request->user()->{app(get_class($request->user()))->getKeyName()} : $request->user;
        if ($user != null) {
            $uObj = $this->findUser($user);
            if ($uObj != null) {
                $wObj = $this->findWorkflow($workflow);
                if ($wObj != null) {
                    $oObj = $this->findObject($wObj);
                    if ($oObj != null) {
                        $oObj = $this->findObjectById($oObj, $object);
                        if ($oObj != null) {
                            $this->saveHistory($wObj, $oObj, $user);
                            $this->updateStatus($wObj, $oObj);
                            return response()->json(['status' => 'success', 'messages' => ['The workflow is executed successfully']], 200);
                        } else {
                            return response()->json(['status' => 'error', 'messages' => ['The object does not exists.']], 404);
                        }
                    } else {
                        return response()->json(['status' => 'error', 'messages' => ['The object class does not exists.']], 404);
                    }
                } else {
                    return response()->json(['status' => 'error', 'messages' => ['The workflow does not exists.']], 404);
                }
            } else {
                return response()->json(['status' => 'error', 'messages' => ['The user does not exists.']], 404);
            }
        } else {
            return response()->json(['status' => 'error', 'messages' => ['The request does not contain a user instance.']], 403);
        }
    }
}
