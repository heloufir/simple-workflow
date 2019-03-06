<?php

namespace Heloufir\SimpleWorkflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Workflow extends Model
{
    protected $table = 'workflows';
    public $primaryKey = 'id';
    public $timestamps = true;

    public $fillable = [
        'refStatusFrom',
        'refStatusTo',
        'refAction',
        'refModule'
    ];

    /**
     * Get action attached to this workflow
     *
     * @return BelongsTo
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function action(): BelongsTo
    {
        return $this->BelongsTo(Action::class, 'refAction', 'id');
    }

    /**
     * Get module attached to this workflow
     *
     * @return BelongsTo
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function module(): BelongsTo
    {
        return $this->BelongsTo(Module::class, 'refModule', 'id');
    }

    /**
     * Get status from attached to this workflow
     *
     * @return BelongsTo
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function statusFrom(): BelongsTo
    {
        return $this->BelongsTo(Status::class, 'refStatusFrom', 'id');
    }

    /**
     * Get status to attached to this workflow
     *
     * @return BelongsTo
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function statusTo(): BelongsTo
    {
        return $this->BelongsTo(Status::class, 'refStatusTo', 'id');
    }
}
