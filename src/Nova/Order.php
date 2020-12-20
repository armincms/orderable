<?php

namespace Armincms\Orderable\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Nova;
use Laravel\Nova\Panel;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\{ID, Badge, Heading, Text, Number, Select, BooleanGroup, BelongsTo, MorphTo, HasMany};
use Armincms\Fields\{Chain, BelongsToMany}; 
use Whitecube\NovaFlexibleContent\Flexible; 
use Armincms\Orderable\Orderable; 

class Order extends Resource 
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Orderable\Models\Order::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'tracking_code';

    /**
     * The relationships that should be eager loaded when performing an index query.
     *
     * @var array
     */
    public static $with = [
        'saleables.saleable'
    ];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'tracking_code'
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

            BelongsTo::make(__('Customer'), 'customer', Orderable::resource('customer'))
                ->withoutTrashed()
                ->searchable()
                ->inverse('orders'), 

            Select::make(__('Status'), 'marked_as')
                ->options([
                    'draft'     => __('Draft'),
                    'pending'   => __('Pending'),
                    'payment'   => __('Payment'),
                    'shipping'  => __('Shipping'),
                    'completed' => __('Completed'),
                ])
                ->onlyOnForms(),

            Badge::make(__('Status'), 'marked_as')
                ->map([
                    'completed' => 'success',
                    'pending'   => 'warning', 
                    'draft'     => 'warning', 
                    'payment'   => 'info', 
                ])
                ->sortable(),

            Text::make(__('Tracking Code'), 'tracking_code')
                ->sortable()
                ->readonly()
                ->hideWhenCreating(),

            Chain::as('orderable', function($request) {
                $options =  Orderable::orderableResources($request)->flip()->map(function($value, $resource) {
                    return $resource::label();
                });
                return [
                    Select::make(__('Order Item'), 'orderable_type')
                        ->options($options)
                        ->withMeta(array_filter([
                            'value' => $options->count() == 1 ? $options->keys()->first() : null
                        ]))
                        ->readonly(! is_null($this->orderable_type)),
                ];
            }), 

            Chain::with('orderable', function($request) {
                if($resource = $request->get('orderable_type')) { 
                    $model = $resource::newModel(); 

                    return $this->filter([
                        $this->when(! Orderable::isSaleable($model), function() use ($model, $resource) {
                            return Select::make($resource::singularLabeL(), 'orderable_id')->options(
                                $model::get()->keyBy($model->getKeyName())->mapInto($resource)->map->title()
                            ); 
                        })
                    ]);
                } 
            }, 'product'),

            Chain::with('orderable', function($request) {
                if($resource = $request->get('orderable_type')) {  
                    return $this->filter([
                        $this->when(Orderable::isDispatchable($resource::newModel()), function() use ($request) { 
                            return Select::make(__('Send Via'), 'courier_id')->options(
                                Orderable::courierResources($request)->pluck('name', 'id')
                            ); 
                        })
                    ]);
                } 
            }),

            Chain::with(['product', 'orderable'], function($request) { 
                if($resource = $request->get('orderable_type')) { 
                    $saleables = Orderable::saleableQuery($resource::newModel()->findOrNew($request->orderable_id))->get(); 

                    $resource = Nova::resourceForModel($saleable = $saleables->first());

                    if(is_null($resource) || ! Orderable::isSaleable($saleable)) return [];
                
                    return [
                        Flexible::make($resource::label(), 'saleables') 
                            ->required()
                            ->collapsed()
                            ->rules('required')
                            ->resolver(Resolvers\Saleable::class)
                            ->button(__('Add :resource', ['resource' => $resource::singularLabeL()]))
                            ->addLayout(__('Choose :product', ['product' => $resource::singularLabeL()]), 'products', [
                                Select::make($resource::singularLabeL(), 'items')
                                    ->options($saleables->keyBy('id')->mapInto($resource)->map->title())
                                    ->required()
                                    ->rules('required'), 

                                Number::make(__('Count'), 'count')
                                    ->min(1)
                                    ->required()
                                    ->rules('required', 'min:1'),
                            ]), 
                    ];
                }
            }, 'order-items'),

            Number::make(__('Total'), function() {
                return $this->numberFormat($this->saleables->sum('sale_price'), 3);
            }),   

            Number::make(__('Discount'), function() {
                return ;
            })->onlyOnDetail(),  

            Number::make(__('Coupons'), function() {
                return ;
            })->onlyOnDetail(),  

            new Panel('Items', $this->saleables->flatMap(function($saleable) {
                return [
                    Heading::make($saleable->saleable->name)->onlyOnDetail(),

                    Text::make(__('Name'), function() use ($saleable) {
                        return $saleable->name;
                    })->onlyOnDetail(),

                    Text::make(__('Sale Price'), function() use ($saleable) {
                        return $this->numberFormat($saleable->sale_price);
                    })->onlyOnDetail(),

                    Text::make(__('Old Price'), function() use ($saleable) {
                        return $this->numberFormat($saleable->old_price);
                    })->onlyOnDetail(),

                    Number::make(__('Count'), function() use ($saleable) {
                        return $saleable->count;
                    })->onlyOnDetail(),

                    Text::make(__('Description'), function() use ($saleable) {
                        return $saleable->description;
                    })->onlyOnDetail(),
                ];
            })),
        ];
    }  

    public function numberFormat($price)
    {
        return number_format($price, 2);
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
