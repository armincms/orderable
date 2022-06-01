<?php

namespace Armincms\Orderable;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;

class Orderable
{  
    /**
     * Determine if the given model implements Salable.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model 
     * @return boolean 
     */
    public static function isSalable($model)
    {
        return $model instanceof Contracts\Salable;
    } 

    /**
     * Determine if the given model implements Orderabl.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model 
     * @return boolean 
     */
    public static function isOrderable($model)
    {
        return $model instanceof Contracts\Orderable;
    }  

    /**
     * Get the orderable resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function orderableResources(Request $request)
    {
        return collect(Nova::availableResources($request))->filter(function($resource) {
            return static::isOrderable($resource::newModel()); 
        })->values();
    } 

    /**
     * Get the orderable resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function salableResources(Request $request)
    {
        return collect(Nova::availableResources($request))->filter(function($resource) {
            return static::isSalable($resource::newModel()); 
        })->values();
    }  
}
