<?php

namespace Heloufir\SimpleWorkflow\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $table = 'workflow_modules';
    public $primaryKey = 'id';
    public $timestamps = true;

    public $fillable = [
        'model'
    ];

    /**
     * Get workflows related to this module
     *
     * @return HasMany
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function workflows(): HasMany
    {
        return $this->hasMany(Workflow::class, 'refModule', 'id');
    }
}
