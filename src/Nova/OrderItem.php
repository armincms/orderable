<?php

namespace Armincms\Orderable\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{ID, Text, Number, Select, BooleanGroup, BelongsTo, MorphMany};
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;
use Armincms\Fields\{Chain, BelongsToMany};
use Armincms\Orderable\Orderable;
use Armincms\Orderable\Contracts\{Orderable as OrderableContract, Saleable};
use Orlyapps\NovaBelongsToDepend\NovaBelongsToDepend;
use Whitecube\NovaFlexibleContent\Flexible;

class OrderItem extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Orderable\Models\OrderItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
             
        ];
    }  

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }
}
