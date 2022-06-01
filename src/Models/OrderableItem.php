<?php

namespace Armincms\Orderable\Models;

use Armincms\Contract\Concerns\InteractsWithWidgets;
use Armincms\Orderable\Contracts\Salable;
use Illuminate\Database\Eloquent\Model;

class OrderableItem extends Model
{
    use InteractsWithWidgets;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        "detail" => "array",
    ];

    /**
     * Query related OrderableOrder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(OrderableOrder::class);
    }

    /**
     * Query related salable resource.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salable()
    {
        return $this->morphTo();
    }

    /**
     * Get total price of item.
     *
     * @return float
     */
    public function totalPrice(): float
    {
        return floatval($this->sale_price) * intval($this->count);
    }

    /**
     * Get total price of item.
     *
     * @return float
     */
    public function subtotalPrice(): float
    {
        return floatval($this->old_price) * intval($this->count);
    }

    /**
     * Serialize the model to pass into the client view.
     *
     * @param Zareismail\Cypress\Request\CypressRequest
     * @return array
     */
    public function serializeForWidget($request, $detail = true): array
    {
        return array_merge(parent::toArray(), [
            "total" => $this->totalPrice(),
            "subtotal" => $this->subtotalPrice(),
        ]);
    }

    /**
     * Create new item by the given salable.
     *
     * @param \Armincms\Orderable\Contracts\Salable $salable
     */
    public static function forSalable(Salable $salable)
    {
        return tap(new static(), function ($item) use ($salable) {
            $item->forceFill([
                "count"        => 1,
                "name"         => $salable->saleName(),
                "sale_price"   => $salable->salePrice(),
                "old_price"    => $salable->oldPrice(),
                "currency"     => $salable->saleCurrency(),
                "description"  => $salable->saleDescription(),
                "salable_type" => $salable->getMorphClass(),
                "salable_id"   => $salable->getKey(),
            ]);
        });
    }
}
