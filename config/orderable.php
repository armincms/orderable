<?php

use Armincms\Orderable\Nova\Billing;
use Armincms\Orderable\Nova\Order;
use Armincms\Orderable\Nova\Item;

return [  
    /*
    |--------------------------------------------------------------------------
    | Orderable Resources
    |--------------------------------------------------------------------------
    |
    | This is an array of orderable resources. 
    |
    */ 
    'resources' => [  
        Billing::class => Billing::class,
        Order::class => Order::class,
        Item::class => Item::class,
    ], 
];
