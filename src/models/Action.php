<?php

namespace Heloufir\SimpleWorkflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Action extends Model
{
    protected $table = 'workflow_actions';
    public $primaryKey = 'id';
    public $timestamps = true;

    public $fillable = [
        'code',
        'designation'
    ];

    /**
     * Get workflows related to this action
     *
     * @return HasMany
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'refAction', 'id');
    }
}
