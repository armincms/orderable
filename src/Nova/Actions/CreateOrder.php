<?php

namespace Armincms\Orderable\Nova\Actions;

use Armincms\Orderable\Nova\Order;
use Armincms\Orderable\Orderable;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class CreateOrder extends Action
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
        $resourceName = config("orderable.resources." . Order::class);

        $resource = tap($resourceName::newModel(), function ($resource) use (
            $fields
        ) {
            $resource
                ->forceFill([
                    "name" => $fields->get("name"),
                    "resource" => $fields->get("resource"),
                ])
                ->save();
        });

        return [
            "push" => [
                "name" => "detail",
                "params" => [
                    "resourceName" => Order::uriKey(),
                    "resourceId" => $resource->getKey(),
                ],
            ],
        ];
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make(__("Order Handler"), "resource")
                ->options(static::salables())
                ->required()
                ->rules("required"),

            Text::make(__('Order Name'), 'name')
                ->required()
                ->rules('required'),
        ];
    }

    public static function salables($value = "")
    {
        return Orderable::salableResources(app(NovaRequest::class))->flip()->map(function ($value, $resource) {
                return $resource::label();
        });
    }
}
