<?php

namespace Heloufir\SimpleWorkflow\Commands;

use Heloufir\SimpleWorkflow\Core\WorkflowSystem;
use Heloufir\SimpleWorkflow\Models\Module;
use Heloufir\SimpleWorkflow\Models\Workflow;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class WorkflowInstallation extends Command
{
    use WorkflowSystem;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:install {model : The model path to install workflow into}
                                             {--namespace= : (optional) The namespace to create into the workflow history model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a workflow into a specific model';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');
        $this->info('Installing workflow system into your model');
        $this->info('******************************************');
        $this->info('Model: ' . $this->argument('model'));
        if (class_exists($model)) {
            $model = app($model);
            if (Module::where('model', $this->argument('model'))->count() == 0) {
                $this->info('> Creating the workflow history table: [' . $this->historyTableName($model) . ']');
                $this->doMigration($model);
                $this->info('> Creating a new line into the modules table: [' . $this->argument('model') . ']');
                $module = new Module();
                $module->model = $this->argument('model');
                $module->save();
                $this->info('> Scaffolding your workflow history model into: [' . ($this->option('namespace') != null ? str_replace('App\\', '', $this->option('namespace')) : '') . '\\' . ucfirst(Str::camel($this->historyTableName($model))) . '.php' . ']');
                $this->scaffoldModel($model);
                $this->info('******************************************');
                $this->info('DONE');
            } else {
                $this->warn('Error: The workflow system is already installed in this model!');
            }
        } else {
            $this->warn('Error: The model specified does not exists!');
        }
        return;
    }

    /**
     * Create the workflow history table into the database
     *
     * @param object $model The model object
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function doMigration($model)
    {
        Schema::table($model->getTable(), function (Blueprint $table) {
            $table->unsignedInteger('refStatus')->nullable(true);
            $table->foreign('refStatus')->references('id')->on('workflow_status');
        });
        Schema::create($this->historyTableName($model), function (Blueprint $table) use ($model) {
            $table->increments('id');
            $table->{$this->getFieldType($model)}('refOrigin');
            $table->foreign('refOrigin')->references($model->getKeyName())->on($model->getTable());
            $table->{$this->getFieldType(new Workflow)}('refWorkflow');
            $table->foreign('refWorkflow')->references('id')->on('workflows');
            $table->{$this->getFieldType(app(config('auth.providers.users.model')))}('refUser');
            $table->foreign('refUser')->references(app(config('auth.providers.users.model'))->getKeyName())->on(app(config('auth.providers.users.model'))->getTable());
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Get the model primary key field type
     *
     * @param object $model The model object
     *
     * @return string The field type, formated for the migration execution
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function getFieldType($model)
    {
        switch (DB::connection()->getDoctrineColumn($model->getTable(), $model->getKeyName())->getType()->getName()) {
            case 'integer':
                return 'unsignedInteger';
            case 'bigint':
                return 'unsignedBigInteger';
            default:
                return 'unsignedInteger';
        }
    }

    /**
     * Create the model file of the workflow history table
     *
     * @param object $model The model object
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    private function scaffoldModel($model)
    {
        $file = file_get_contents(__DIR__ . '../../stubs/Model.stub');
        $modeTemplate = str_replace(
            [
                '{{namespace}}',
                '{{model}}',
                '{{table}}',
                '{{origin}}',
                '{{user}}'
            ],
            [
                $this->option('namespace') != null ? $this->option('namespace') : 'App',
                ucfirst(Str::camel('workflow_history_users')),
                $this->historyTableName($model),
                get_class($model),
                config('auth.providers.users.model')
            ],
            $file);
        if ($this->option('namespace') != null && !file_exists(str_replace('App\\', '', $this->option('namespace')))) {
            File::makeDirectory(str_replace('App\\', 'app\\', $this->option('namespace')), 0755, true);
        }

        File::put(
            app_path($this->option('namespace') != null ? str_replace('App\\', '', $this->option('namespace')) : '') . '\\' . ucfirst(Str::camel($this->historyTableName($model))) . '.php',
            $modeTemplate
        );
    }
}
