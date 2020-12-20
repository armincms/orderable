<?php

namespace Armincms\Orderable;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Nova\Nova;

class Orderable
{
    /**
     * The tables prefix string.
     * 
     * @var string
     */
    public static $prefix = 'ord';

    /**
     * Get the resource class.
     *
     * @return void
     */
    public static function resource(string $name)
    {
        return config("orderable.resources.{$name}");
    } 

    /**
     * Determine if the given model implements Saleable.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model 
     * @return boolean 
     */
    public static function isSaleable($model)
    {
        return $model instanceof Contracts\Saleable;
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
     * Determine if the given model implements Dispatchable.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model 
     * @return boolean 
     */
    public static function isDispatchable($model)
    {
        return $model instanceof Contracts\Dispatchable;
    }

    /**
     * Determine if the given model implements Dispatchable.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $model 
     * @return boolean 
     */
    public static function isCourier($model)
    {
        return $model instanceof Contracts\Courier;
    }

    /**
     * Get the courier resources.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function courierResources(Request $request)
    {
        return collect(Nova::availableResources($request))->filter(function($resource) {
            return static::isCourier($resource::newModel()); 
        });
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
        });
    } 

    /**
     * Get the orderable resource by the key.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function resourceForKey($key)
    {
        return static::orderableResources(app('request'))->first(function($resource) use ($key) {
            return $resource::uriKey() === $key;
        });
    }  

    /**
     * Get the orderable resource by the key.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public static function resourceForRelation($relation)
    { 
        return static::orderableResources(app('request'))->first(function($resource) use ($relation) { 
            return static::resourceRelation($resource) === $relation;
        });
    } 

    /**
     * Guess relation-shipt for the given resource.
     * 
     * @param  \Laravel\Nova\Resource $resource 
     * @return string           
     */
    public static function resourceRelation($resource)
    {
        return Str::camel(Str::snake($resource::uriKey()));
    }  

    /**
     * Get the Query for the given model.
     * 
     * @param  \Illuminate\Database\Eloquent\Model $resource 
     * @return \Illuminate\Database\Eloquent\Builder          
     */
    public static function saleableQuery($model)
    {
        return static::isSaleable($model) ? $model : $model->saleables();  
    } 

    /**
     * Get the prefixed table name.
     * 
     * @param  string $name
     * @return string      
     */
    public static function table(string $name)
    {
        return Str::startsWith($name, static::prefix('')) ? $name : static::prefix($name); 
    }

    /**
     * Prepend the prefix to the given string.
     * 
     * @param  string $suffix 
     * @return string         
     */
    public static function prefix(string $suffix)
    {
        return implode('_', [static::$prefix, $suffix]);
    }

    /**
     * Set the table prefix string.
     * 
     * @param  string $prefix 
     * @return static         
     */
    public static function prefixUsing(string $prefix)
    {
        static::$prefix = rtrim($prefix, '_');

        return static::class;
    }
}
