<?php

namespace Armincms\Orderable\Nova;

use Alvinhu\ChildSelect\ChildSelect;
use Armincms\Contract\Nova\Bios;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Select;
use Zareismail\Gutenberg\Gutenberg;

class Billing extends Bios
{
    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Select::make(__('Billing Currency'), 'billing_currency')->options([
                'IRR' => __('Iranina Rials'),
            ]),

            Select::make(__('Billing Website'), "billing_website")->options(function() {
                    return Gutenberg::cachedWebsites()->keyBy->getKey()->map->name;
                })
                ->required()
                ->rules('required')
                ->displayUsingLabels(),

                Select::make(__('Billing Page'), "billing_fragment")->options(function() {
                    return Gutenberg::cachedFragments()->keyBy->getKey()->map->name;
                })
                ->required()
                ->rules('required')
                ->displayUsingLabels()
                ->exceptOnForms(),

                ChildSelect::make(__('Billing Page'), "billing_fragment")->options(function ($website) {
                    return Gutenberg::cachedFragments()->where('website_id', $website)->keyBy->getKey()->map->name;
                })
                ->parent("billing_website")
                ->required()
                ->rules('required')
                ->hideFromDetail(),

        ];
    }

    /**
     * Get billing page.
     *
     * @return integer
     */
    public static function billingPage()
    {
        return static::option("billing_fragment");
    }

    /**
     * Get billing page.
     *
     * @return integer
     */
    public static function cuurency()
    {
        return static::option("billing_currency");
    }
}
