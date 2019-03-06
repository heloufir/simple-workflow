<?php

namespace Heloufir\SimpleWorkflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $table = 'workflow_status';
    public $primaryKey = 'id';
    public $timestamps = true;

    public $fillable = [
        'code',
        'designation'
    ];

    /**
     * Get workflows related to this status
     * >> Linked by the [FROM] clause
     *
     * @return HasMany
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function workflowsFrom(): HasMany
    {
        return $this->hasMany(Workflow::class, 'refStatusFrom', 'id');
    }

    /**
     * Get workflows related to this status
     * >> Linked by the [TO] clause
     *
     * @return HasMany
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function workflowsTo(): HasMany
    {
        return $this->hasMany(Workflow::class, 'refStatusTo', 'id');
    }
}
