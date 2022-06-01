<?php

namespace Armincms\Orderable\Nova\Actions;

use Armincms\Orderable\Nova\Order; 
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields; 
use Laravel\Nova\Fields\Select; 

class UpdateOrderStatus extends Action
{
    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    { 
        $models->first()->whereKey($models->map->getKey())->update([
            'marked_as' => $fields->get('marked_as'),
        ]); 
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make(__("Order Status"), "marked_as")
                ->options(forward_static_call([Order::newModel(), 'statuses']))
                ->required()
                ->rules("required"),         
        ];
    } 
}
