<?php

namespace Armincms\Orderable\Nova;

use Armincms\Arminpay\Nova\Transaction;
use Armincms\Contract\Nova\User;
use Illuminate\Http\Request; 
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Order extends Resource
{    
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \Armincms\Orderable\Models\OrderableOrder::class;

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
            BelongsTo::make(__('Order Customer'), 'auth', User::class)
                ->required()
                ->rules('required')
                ->showCreateRelationButton(),

            Text::make(__('Order Name'), 'name')->sortable(),

            Badge::make(__('Order Status'), 'marked_as')
                ->labels(forward_static_call([static::$model, 'statuses']))
                ->map([
                    'draft'     => 'warning',
                    'pending'   => 'warning',
                    'onhold'    => 'warning',
                    'packing'   => 'info',
                    'shipping'  => 'info',
                    'verified'  => 'info',
                    'refunded'  => 'danger',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                ]),

            $this->currencyField(__('Total Price'))->withMeta([
                'value' => $this->items->sum->totalPrice(),
            ])->onlyOnDetail(),

            HasMany::make(__('Order Items'), 'items', Item::class),

            HasMany::make(__('Order Transactions'), 'transactions', Transaction::class),
        ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [ 
            Actions\CreateOrder::make()
                ->standalone()
                ->onlyOnIndex()
                ->canSee(function($request) {
                    return $request->user()->can('create', static::newModel()) &&
                         ! $request->viaRelationship();
                }),

            Actions\UpdateOrderStatus::make() 
                ->showOnTableRow() 
                ->canSee(function($request) {
                    return $request->user()->can('update', static::newModel()) &&
                         ! $request->viaRelationship();
                }), 
        ];
    } 

    /**
     * Determine if the current user can create new resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    /**
     * Determine if the user can add / associate models of the given type to the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return bool
     */
    public function authorizedToAdd(NovaRequest $request, $model)
    { 
        return $this->resource->canBeUpdated() && parent::authorizedTo($request, $model);
    }
}
