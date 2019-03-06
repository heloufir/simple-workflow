<?php

namespace Heloufir\SimpleWorkflow\Core;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Paginator
{

    /**
     * Paginate the data gotten from the builder based on the request parameters
     *
     * @param Builder $builder
     *      The builder object with what to get data
     * @param Request|null $request
     *      The request object
     *
     * @return array
     *
     * @author EL OUFIR Hatim <eloufirhatim@gmail.com>
     */
    public static function paginate(Builder $builder, Request $request = null): array
    {
        if ($request != null && $request->has('size')) {
            $count = $builder->count();
            $size = $request->size;
            $page = 1;
            if ($request->has('page')) {
                $page = $request->page;
            }
            $builder
                ->skip(($page - 1) * $size)
                ->take($size);
            $result = new PartialList($builder->get(), $count, $page, $size);
        } else {
            $result = new PartialList($builder->get(), $builder->count(), 1, $builder->count());
        }
        return $result->toArray();
    }

}