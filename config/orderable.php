<?php

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

        /*
         * The order resource
         */
        'invoice' => \Armincms\Orderable\Nova\Invoice::class,

        /*
         * The order resource
         */
        'order' => \Armincms\Orderable\Nova\Invoice::class,

        /*
         * The order resource
         */
        'items' => \Armincms\Orderable\Nova\OrderItem::class,

        /*
         * The customer resource
         */
        'customer' => \Armincms\Nova\User::class //\App\Nova\User::class,    	
    ],

    /*
    |--------------------------------------------------------------------------
    | Orderable default values
    |--------------------------------------------------------------------------
    |
    | This is an array of orderable default values. 
    |
    */

    'default' => [
        
    ],
];
