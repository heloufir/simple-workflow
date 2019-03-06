<?php

Route::group(['prefix' => 'api'], function () {
    Route::resource('actions', 'Heloufir\SimpleWorkflow\Http\Controllers\ActionController')
        ->except(['create', 'edit'])
        ->middleware(config('simple-workflow.security.actions.secured') ? config('simple-workflow.security.actions.middlewares') : []);
    Route::resource('status', 'Heloufir\SimpleWorkflow\Http\Controllers\StatusController')
        ->except(['create', 'edit'])
        ->middleware(config('simple-workflow.security.status.secured') ? config('simple-workflow.security.status.middlewares') : []);
    Route::resource('modules', 'Heloufir\SimpleWorkflow\Http\Controllers\ModuleController')
        ->except(['create', 'edit'])
        ->middleware(config('simple-workflow.security.modules.secured') ? config('simple-workflow.security.modules.middlewares') : []);
    Route::resource('workflows', 'Heloufir\SimpleWorkflow\Http\Controllers\WorkflowController')
        ->except(['create', 'edit'])
        ->middleware(config('simple-workflow.security.workflows.secured') ? config('simple-workflow.security.workflows.middlewares') : []);
    Route::put('workflows/update-status/{workflow}/{object}', 'Heloufir\SimpleWorkflow\Http\Controllers\WorkflowController@doWorkflow')
        ->middleware(config('simple-workflow.security.workflows.secured') ? config('simple-workflow.security.workflows.middlewares') : []);
});
