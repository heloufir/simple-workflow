<?php

namespace Heloufir\SimpleWorkflow\Core;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait BuilderSpecification
{

    /**
     * Add specification from http request to the builder object
     *
     * @param Builder $builder The builder object
     * @param Request $request The request object
     *
     * @return Builder The builder containing the specifications
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public function addSpecifications(Builder $builder, Request $request): Builder
    {
        if ($request->has('specifications')) {
            foreach ($request->get('specifications') as $key => $value) {
                $builder->where($key, $value);
            }
        }
        return $builder;
    }

}