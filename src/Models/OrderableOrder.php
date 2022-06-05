<?php

namespace Armincms\Orderable\Models;

use Armincms\Arminpay\Contracts\Billable;
use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\Arminpay\Models\ArminpayTransaction;
use Armincms\Contract\Concerns\Authorizable;   
use Armincms\Contract\Concerns\GeneratesTrackingCode; 
use Armincms\Contract\Concerns\InteractsWithFragments; 
use Armincms\Contract\Concerns\InteractsWithUri; 
use Armincms\Contract\Concerns\InteractsWithWidgets; 
use Armincms\Contract\Concerns\QueriesWithResource; 
use Armincms\Contract\Contracts\Authenticatable; 
use Armincms\Orderable\Contracts\Salable; 
use Armincms\Orderable\Nova\Billing; 
use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\SoftDeletes; 
use Zareismail\Cypress\Http\Requests\CypressRequest;   
use Zareismail\Markable\HasDraft;  
use Zareismail\Markable\HasPending;  
use Zareismail\Markable\Markable;
use Zareismail\Gutenberg\Gutenberg;

class OrderableOrder extends Model implements Billable
{  
    use Authorizable;     
    use GeneratesTrackingCode;     
    use HasCompletion; 
    use HasDraft; 
    use HasPayment; 
    use HasPending; 
    use InteractsWithFragments;
    use InteractsWithUri;
    use InteractsWithWidgets;
    use Markable; 
    use Progressable;
    use QueriesWithResource; 
    use SoftDeletes;     

    /**
     * Query related OrderableOrder 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany     
     */
    public function items()
    {
        return $this->hasMany(OrderableItem::class, 'order_id');
    } 

    /**
     * Query related OrderableOrder 
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany     
     */
    public function transactions()
    {
        return $this->morphMany(ArminpayTransaction::class, 'billable');
    } 

    /**
     * Get the corresponding cypress fragment.
     * 
     * @return 
     */
    public function cypressFragment(): string
    {
        return \Armincms\Orderable\Cypress\Fragments\Billing::class;
    }

    /**
     * Get the uri for the model.
     *
     * @return string
     */
    public function getUriName()
    {
        return $this->getTrackingCodeColumn();
    }

    /**
     * Get the url generator callbacks.
     * 
     * @return array
     */
    public function getUriGenerators()
    {
        return [
            function($model) {
                return $model->trackingCode();
            }
        ];
    }

    /**
     * Determin that order can be updated.
     * 
     * @return Boolean
     */
    public function canBeUpdated() {
        return $this->isDraft() || $this->isPending() || $this->isOnHold();
    } 

    /**
     * The order tracking states.
     * 
     * @return array
     */
    static public function statuses()
    {
        return [ 
            'draft'     => __('Draft Order'), // draft
            'pending'   => __('Pending Order'), // need confirm or accept
            'onhold'    => __('On Hold Order'), // need payment
            'verified'  => __('Verified Order'), // payment verified
            'inprogress'=> __('In Progressing Order'), // Progressing
            'shipping'  => __('Shipping Order'), // shipping
            'refunded'  => __('Refunded Order'), // Refunded
            'completed' => __('Completed Order'), // Completed
            'cancelled' => __('Cancelled Order'), // Cancelled
        ];
    }

    /**
     * Get the uri value.
     * 
     * @return string
     */
    public function getUri()
    {
        return $this->trackingCode();
    }

    /**
     * The payment amount.
     * 
     * @return float
     */
    public function amount(): float
    {
        return $this->items->sum->totalPrice();
    }

    /**
     * The payment currency.
     * 
     * @return float
     */
    public function currency(): string
    {
        return \Armincms\Orderable\Nova\Billing::cuurency();
    }

    /**
     * Return the path that should be called after the payment.
     * 
     * @return float
     */
    public function callback(): string
    {
        return route('orderable.order.verify', $this->trackingCode());
    } 

    /**
     * Serialize the model to pass into the client view for single item.
     *
     * @param Zareismail\Cypress\Request\CypressRequest
     * @return array
     */
    public function serializeForDetailWidget($request)
    {
        return array_merge($this->serializeForIndexWidget($request), [
            'items' => $this->items->map->serializeForWidget($request)->toArray(),
            'total' => $total = $this->items->sum->totalPrice(),
            'subTotal' => $subtotal = $this->items->sum->subtotalPrice(),
            'discount' => $total - $subtotal,
        ]);
    }

    /**
     * Serialize the model to pass into the client view for collection of items.
     *
     * @param Zareismail\Cypress\Request\CypressRequest
     * @return array
     */
    public function serializeForIndexWidget($request)
    {
        return [
            'id' => $this->getKey(),
            'code' => $this->trackingCode(),
            'state' => data_get(static::statuses(), $this->marked_as),
            'marked_as' => $this->marked_as,
            'currency' => \Armincms\Orderable\Nova\Billing::cuurency(),
        ];
    }

    /**
     * Add new salable item.
     * 
     * @param \Armincms\Orderable\Contracts\Salable $salable 
     */
    public function addItem(Salable $salable, $detail = [], $count = 1)
    {
        return tap(OrderableItem::forSalable($salable), function($item) use ($count, $detail) {
            $item->forceFill([
                'order_id' => $this->getKey(), 
                'count' => $count,
                'detail'=> $detail,
            ])->save();
        });
    }

    public function redirect($request, $billingPage = false)
    {
        $gateways = ArminpayGateway::enable()->get();

        if ($gateways->count() === 1 && ! $billingPage) {
            try { 
                return $gateways->pop()->checkout($request, $this);
            } catch(\Exception $e) {

            }
        }

        if ($fragment = Gutenberg::cachedFragments()->find(Billing::billingPage())) {
            return redirect($fragment->getUrl($this->getUri()));
        }

        abort(404, 'Billing page not found.');
    } 
}
