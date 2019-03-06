<?php

namespace Heloufir\SimpleWorkflow\Core;

use Carbon\Carbon;
use Heloufir\SimpleWorkflow\Models\Workflow;
use Illuminate\Support\Facades\DB;

trait WorkflowSystem
{

    /**
     * Find a user object by it's id
     *
     * @param int $id The user id
     *
     * @return mixed The user object retrieved
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function findUser(int $id)
    {
        return app(config('auth.providers.users.model'))->where(app(config('auth.providers.users.model'))->getKeyName(), $id)->first();
    }

    /**
     * Find a workflow object by it's id
     *
     * @param int $id The workflow id
     *
     * @return null|Workflow The workflow object retrieved
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function findWorkflow(int $id)
    {
        return Workflow::where('id', $id)->first();
    }

    /**
     * Find the object object from the module linked to the workflow object
     *
     * @param Workflow $workflow The workflow object
     *
     * @return mixed|null The object linked to the workflow
     *
     * @author EL OUFIR Hatim
     */
    public function findObject(Workflow $workflow)
    {
        return class_exists($workflow->module->model) ? app($workflow->module->model) : null;
    }

    /**
     * Generate the workflow history table name
     *
     * @param object $model The model object
     *
     * @return string The workflow history table name
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function historyTableName($model): string
    {
        return 'workflow_history_' . $model->getTable();
    }

    /**
     * Find the object selected by the workflow
     *
     * @param mixed $object The object class
     * @param int $id The object id to select
     *
     * @return mixed The object selected
     *
     * @author EL OUIFR Hatim <eloufirhatim@gmail.com>
     */
    public function findObjectById($object, int $id)
    {
        return $object->where($object->getKeyName(), $id)->first();
    }

    /**
     * Save the workflow history of the given object
     *
     * @param Workflow $workflow The workflow object
     * @param mixed $object The object to save history
     * @param int $user The user id
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function saveHistory(Workflow $workflow, $object, int $user)
    {
        DB::table($this->historyTableName(app(get_class($object))))
            ->insert([
                'refOrigin' => $object->{$object->getKeyName()},
                'refWorkflow' => $workflow->id,
                'refUser' => $user,
                'created_at' => Carbon::now()
            ]);
    }

    /**
     * Update the object's status based on the workflow object
     *
     * @param Workflow $workflow The workflow object
     * @param mixed $object The object to update it's status
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function updateStatus(Workflow $workflow, $object)
    {
        $object->refStatus = $workflow->refStatusTo;
        $object->save();
    }
}