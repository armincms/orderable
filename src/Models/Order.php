<?php

namespace Armincms\Orderable\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Armincms\Arminpay\Contracts\{Billable, Trackable};
use Armincms\Arminpay\Concerns\{HasTrackingCode, InteractsWithTransactions};  
use Armincms\Orderable\Contracts\Orderable as OrderableContract;
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
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(function($query) { 
            return $query->tap(function($query) {
                if($morphs = Orderable::morphs()->all()) {
                    return $query->forResources($morphs);
                }
            });
        });
    }
    
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
     * Query with gieven morph types.
     *
     * @param array morphs
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForResources($query, array $morphs)
    {
        return $query->whereIn($query->qualifyColumn('orderable_type'), $morphs);
    }

    /**
     * The payment amount.
     * 
     * @return float
     */
    public function amount(): float
    {
        return floatval($this->saleables->sum->totalPrice());
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
     * Create new order with given model.
     * 
     * @param  \Illuminate\Database\Eloqeunt\Model $model
     * @param \Illuminate\Contracts\Auth\Authenticatable|Null $user
     * @return $this       
     */
    public static function createFromModel(OrderableContract $orderable, Authenticatable $user = null)
    {
        return tap(new static, function($order) use ($orderable, $user) {
            $order->orderable()->associate($orderable);
            $order->customer()->associate($user ?: request()->user());
            $order->asPending();
        });
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
