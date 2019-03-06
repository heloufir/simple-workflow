<?php

namespace Heloufir\SimpleWorkflow\Commands;

use Heloufir\SimpleWorkflow\Models\Action;
use Heloufir\SimpleWorkflow\Models\Module;
use Heloufir\SimpleWorkflow\Models\Status;
use Heloufir\SimpleWorkflow\Models\Workflow;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class WorkflowConfiguration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:config {model : The model path to configure workflow into}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure a workflow into a specific model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');
        $this->info('Configuring workflow system into your model');
        $this->info('*******************************************');
        $this->info('Model: ' . $this->argument('model'));
        if (class_exists($model)) {
            $module = Module::where('model', $this->argument('model'))->first();
            if ($module != null) {
                $this->drawWorkflowTable($module);
                do {
                    $statusFrom = $this->chooseStatusFrom();
                    if (!$statusFrom && $statusFrom != null) {
                        return;
                    }
                    $statusTo = $this->chooseStatusTo();
                    $action = $this->chooseAction();
                    if (!$action) {
                        return;
                    }
                    if ($this->checkWorkflowExistence($statusFrom, $statusTo, $action, $module)) {
                        $this->warn('Warning: Cannot add this workflow, it is already configured.');
                    } else {
                        $workflow = new Workflow();
                        $workflow->code = strtoupper(Str::limit(Str::slug($module->model . ' ' . (Workflow::where('refModule', $module->id)->count() + 1), '_'), 255, ''));
                        $workflow->designation = ($statusFrom != null ? $statusFrom->designation : 'N/A') . ' to ' . $statusTo->designation;
                        $workflow->refStatusFrom = $statusFrom != null ? $statusFrom->id : null;
                        $workflow->refStatusTo = $statusTo->id;
                        $workflow->refAction = $action->id;
                        $workflow->refModule = $module->id;
                        $this->drawWorkflowBeforeSave($workflow, $statusFrom, $statusTo, $action);
                        if ($this->confirm('Please confirm the data above to save the workflow into the database')) {
                            $workflow->save();
                            $this->info('> Success: saving the workflow done!');
                        } else {
                            $this->info('> Canceled: Saving the workflow canceled!');
                        }
                    }
                    $this->info('');
                } while ($this->confirm('Would you add another workflow?'));
            } else {
                $this->warn('Error: The workflow system is not installed yet in this model! Please use the workflow:install command.');
            }
        } else {
            $this->warn('Error: The model specified does not exists!');
        }
        return;
    }

    /**
     * Draw the module's workflow already configured
     *
     * @param Module $module The module object
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function drawWorkflowTable(Module $module)
    {
        $this->info('');
        $workflows = Workflow::where('refModule', $module->id)->get();
        $this->info('- Workflow already configured:');
        if ($workflows->count() == 0) {
            $this->info('> No workflow configured yet!');
        } else {
            $headers = ['CODE', 'DESIGNATION', 'STATUS FROM', 'STATUS TO', 'ACTION'];
            $data = [];
            foreach ($workflows as $workflow) {
                array_push($data, [
                    'code' => $workflow->code,
                    'designation' => $workflow->designation,
                    'status_from' => $workflow->refStatusFrom != null ? $workflow->statusFrom->designation : 'N/A',
                    'status_to' => $workflow->statusTo->designation,
                    'action' => $workflow->action->designation
                ]);
            }
            $this->table($headers, $data);
        }
    }

    /**
     * Draw the status from list
     *
     * @return mixed FALSE if the status list is empty, NULL if the status from is set to null
     *          or the Status object if the status is selected
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function chooseStatusFrom()
    {
        $this->info('');
        $statuses = Status::get();
        $this->info('1. Workflow status from:');
        if ($statuses->count() == 0) {
            $this->info('> No statuses configured yet! Please fill the workflow_status table first.');
            return false;
        } else {
            $data = ['No status'];
            $map = ['No status' => null];
            foreach ($statuses as $status) {
                array_push($data, $status->designation);
                $map[$status->designation] = $status->id;
            }
            $choice = $this->choice('Choose a status from?', $data, 0);
            if ($map[$choice] == null) {
                return null;
            } else {
                return $statuses->where('id', $map[$choice])->first();
            }
        }
    }

    /**
     * Draw the status to list
     *
     * @return mixed FALSE if the status list is empty or the Status object if the status is selected
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function chooseStatusTo()
    {
        $this->info('');
        $statuses = Status::get();
        $this->info('2. Workflow status to:');
        if ($statuses->count() == 0) {
            $this->info('> No statuses configured yet! Please fill the workflow_status table first.');
            return false;
        } else {
            $data = [];
            $map = [];
            foreach ($statuses as $status) {
                array_push($data, $status->designation);
                $map[$status->designation] = $status->id;
            }
            $choice = $this->choice('Choose a status to?', $data, 0);
            return $statuses->where('id', $map[$choice])->first();
        }
    }

    /**
     * Draw the actions list
     *
     * @return mixed FALSE if the status list is empty or the Action object if the action is selected
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function chooseAction()
    {
        $this->info('');
        $actions = Action::get();
        $this->info('3. Workflow action:');
        if ($actions->count() == 0) {
            $this->info('> No actions configured yet! Please fill the workflow_actions table first.');
            return false;
        } else {
            $data = [];
            $map = [];
            foreach ($actions as $action) {
                array_push($data, $action->designation);
                $map[$action->designation] = $action->id;
            }
            $choice = $this->choice('Choose an action?', $data, 0);
            return $actions->where('id', $map[$choice])->first();
        }
    }

    /**
     * Check if the workflow already exists
     *
     * @param null|Status $statusFrom The status from object (can be null)
     * @param Status $statusTo The status to object
     * @param Action $action The action object
     * @param Module $module The module object
     *
     * @return bool TRUE if the workflow already exists, FALSE instead
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function checkWorkflowExistence($statusFrom, Status $statusTo, Action $action, Module $module): bool
    {
        $query = Workflow::query();
        if ($statusFrom == null) {
            $query->whereNull('refStatusFrom');
        } else {
            $query->where('refStatusFrom', $statusFrom->id);
        }
        $query->where('refStatusTo', $statusTo->id);
        $query->where('refAction', $action->id);
        $query->where('refModule', $module->id);
        return $query->count() != 0;
    }

    /**
     * Draw a table containing the workflow before save
     *
     * @param Workflow $workflow The workflow to draw
     * @param null|Status $statusFrom The status from object, can be null
     * @param Status $statusTo The status to object
     * @param Action $action The action object
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function drawWorkflowBeforeSave(Workflow $workflow, $statusFrom, Status $statusTo, Action $action)
    {
        $this->info('');
        $header = ['CODE', 'DESIGNATION', 'STATUS FROM', 'STATUS TO', 'ACTION'];
        $data = [[
            'code' => $workflow->code,
            'designation' => $workflow->designation,
            'status_from' => $statusFrom != null ? $statusFrom->designation : 'N/A',
            'status_to' => $statusTo->designation,
            'action' => $action->designation
        ]];
        $this->table($header, $data);
    }
}
