<?php

namespace Armincms\Orderable\Gutenberg\Templates; 
  
use Zareismail\Gutenberg\Template; 
use Zareismail\Gutenberg\Variable;
use Armincms\Arminpay\Models\ArminpayGateway;

class BillingWidget extends Template 
{    
    /**
     * The logical group associated with the widget.
     *
     * @var string
     */
    public static $group = 'Billing';

    /**
     * Register the given variables.
     * 
     * @return array
     */
    public static function variables(): array
    { 
        $conversions = (new ArminpayGateway)->conversions()->implode(',');

        return [
            Variable::make('id', __('The order Id')),

            Variable::make('name', __('The order Name')),

            Variable::make('code', __('The order tracking code')), 

            Variable::make('total', __('The order total price')), 

            Variable::make('subtotal', __('The order subtotal price')), 

            Variable::make('currency', __('The order currency')), 

            Variable::make('items', __('The order cart items')), 

            Variable::make('items.*.name', __('Name of the Cart itmes')), 

            Variable::make('items.*.description', __('Description of the Cart itmes')), 

            Variable::make('items.*.sale_price', __('Sale price of the Cart itmes')), 

            Variable::make('items.*.old_price', __('Old price of the Cart itmes')), 

            Variable::make('items.*.count', __('Number of the itme in the cart')), 

            Variable::make('items.*.currency', __('The order item currency')),             

            Variable::make('gates', __('Payment gateways')), 

            Variable::make('gates.*.name', __('The gateway name')),

            Variable::make('gates.*.id', __('The gateway id')), 

            Variable::make('gates.*.logo', __("The gateway logo. available conversions is:[{$conversions}]")), 

            Variable::make('from.action', __('Form submit url')), 

            Variable::make('from.method', __('Form submit method')), 

            Variable::make('from.csrf_token', __('Form csrf token field value')), 

            Variable::make('from.csrf_token_field', __('Form csrf token field name')), 

            Variable::make('from.gate_field', __('Form gateway field name')), 
        ];
    } 
}
