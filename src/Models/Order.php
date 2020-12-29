<?php

namespace Armincms\Orderable\Models;
 
use Armincms\Arminpay\Contracts\{Billable, Trackable};
use Armincms\Arminpay\Concerns\{HasTrackingCode, InteractsWithTransactions};  
use Armincms\Orderable\Orderable;

class Order extends Model implements Trackable, Billable
{  
    use HasTrackingCode, InteractsWithTransactions, InteractsWithSaleables;

    /**
     * List of added saleables.
     * 
     * @var array
     */
    protected $cart = []; 
    
    /**
     * Query the related orderable.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function orderable()
    {
        return $this->morphTo();
    } 

    /**
     * The payment amount.
     * 
     * @return float
     */
    public function amount(): float
    {
        return floatval($this->saleables->sum('sale_price'));
    }

    /**
     * The payment currency.
     * 
     * @return float
     */
    public function currency(): string
    {
        return data_get(optional($this->saleables)->first(), 'currency', 'IRR');
    }

    /**
     * Return the path that should be called after the payment.
     * 
     * @return float
     */
    public function callback(): string
    {
        return app('site')->get('orders')->url($this->trackingCode(). '/shipped');
    }  

    /**
     * Return the path that should be called after the payment.
     * 
     * @return float
     */
    public function finishCallback(): string
    {
        return $this->finish_callback;
    } 

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {   
        if($resource = Orderable::resourceForRelation($method)) {  
            $saleable = Orderable::saleableQuery($resource::newModel())->getModel();

            return $this->morphedByMany($saleable, 'saleable', Orderable::Table('saleables'))
                        ->withPivot('details', 'sale_price', 'old_price' ,'count', 'name', 'description')
                        ->using(OrderItem::class);  
        }   
            
        return parent::__call($method, $parameters); 	
    }
}
