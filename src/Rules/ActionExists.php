<?php

namespace Heloufir\SimpleWorkflow\Rules;

use Heloufir\SimpleWorkflow\Models\Action;
use Illuminate\Contracts\Validation\Rule;

class ActionExists implements Rule
{
    private $id;

    public function __construct(int $id = null)
    {
        $this->id = $id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Action::where('id', $this->id == null ? $value : $this->id)->count() != 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.exists', ['attribute' => 'action']);
    }
}
