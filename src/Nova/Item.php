<?php

namespace Armincms\Orderable\Nova;

use Armincms\Orderable\Orderable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class Item extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Orderable\Models\OrderableItem::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = "name";

    /**
     * Indicates if the resource should be displayed in the sidebar.
     *
     * @var bool
     */
    public static $displayInNavigation = false;

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
    public static $search = ["id", "name"];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        $salableResource = $request->isResourceDetailRequest()
            ? Nova::resourceForModel($request->findResourceOrFail()->salable)
            : data_get($request->findParentResourceOrFail(), "resource");

        if (is_callable([$salableResource, "relatableFields"])) {
            return $salableResource::relatableItemFields($request);
        }

        return [
            ID::make()->sortable(),

            BelongsTo::make(__("Order Name"), "order", Order::class),

            MorphTo::make(__("Relatable product"), "salable", $salableResource)
                ->types(Orderable::salableResources($request)->all())
                ->required()
                ->rules([
                    "required", 
                    Rule::unique(static::$model, 'salable_id')
                        ->ignore($this->resource->getKey())
                        ->where(function ($query) {
                            $resource = Nova::resourceForKey(request('salable_type'));

                            return $query->where([
                                'salable_type' => $resource::newModel()->getMorphClass()
                            ]);
                        })
                ])
                ->withoutTrashed()
                ->sortable()
                ->showCreateRelationButton(),

            Number::make(__("Number of item"), "count")
                ->required()
                ->rules("required")
                ->default(1)
                ->min(1),

            Text::make(__("The item name"), "name")
                ->nullable()
                ->help(__("Leave empty to use the product name."))
                ->fillUsing(function ($request, $model, $attribute) {
                    if (!$request->filled($attribute)) {
                        $salable = once(function () use ($model) {
                            return $model->salable()->first();
                        });

                        $model->setAttribute($attribute, $salable->saleName());
                    }
                }),

            $this->currencyField(__("The item real price"), "old_price")
                ->nullable()
                ->exceptOnForms()
                ->fillUsing(function ($request, $model, $attribute) {
                    if (!$request->filled($attribute)) {
                        $salable = once(function () use ($model) {
                            return $model->salable()->first();
                        });

                        $model->setAttribute($attribute, $salable->oldPrice());
                    }
                }),

            $this->currencyField(__("The item sale price"), "sale_price")
                ->nullable()
                ->help(__("Leave empty to use the product sale price."))
                ->fillUsing(function ($request, $model, $attribute) {
                    if (!$request->filled($attribute)) {
                        $salable = once(function () use ($model) {
                            return $model->salable()->first();
                        });

                        $model->setAttribute($attribute, $salable->salePrice());
                    }
                }),

            Textarea::make(__("The item description"), "description")
                ->nullable()
                ->help(__("Leave empty to use the product description."))
                ->fillUsing(function ($request, $model, $attribute) {
                    if (!$request->filled($attribute)) {
                        $salable = once(function () use ($model) {
                            return $model->salable()->first();
                        });

                        $model->setAttribute(
                            $attribute,
                            $salable->saleDescription()
                        );
                    }
                }),
        ];
    }  

    /**
     * Determine if the current user can view the given resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $ability
     * @return bool
     */
    public function authorizedTo(Request $request, $ability)
    {
        if (in_array($ability, ['view', 'create'])) {
            return parent::authorizedTo($request, $ability);
        } 

        if (is_null($order = $request->findParentModel())) {
            return parent::authorizedTo($request, $ability);
        }  

        return $order->canBeUpdated();
    } 

    /**
     * Return the location to redirect the user after creation.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.Order::uriKey().'/'.$resource->order_id;
    } 

    /**
     * Return the location to redirect the user after update.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Laravel\Nova\Resource  $resource
     * @return string
     */
    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/'.Order::uriKey().'/'.$resource->order_id;
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
