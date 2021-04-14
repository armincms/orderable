<?php

namespace Armincms\Orderable\Models;
 
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Armincms\Orderable\Orderable;

class OrderItem extends MorphPivot
{      
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    	'details' => 'array'
    ];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return Orderable::table('saleables');
    } 

    /**
     * Query the related saleable.
     * 
     * @return 
     */
    public function saleable()
    { 
        return $this->morphTo();
    }

    /**
     * Get the total of invoice.
     * 
     * @return float
     */
    public function totalPrice()
    { 
        return floatval($this->sale_price * $this->count);
    }
}
