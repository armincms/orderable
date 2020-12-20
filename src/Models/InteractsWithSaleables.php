<?php

namespace Armincms\Orderable\Models;

use Armincms\Orderable\Contracts\Saleable;
  
trait InteractsWithSaleables 
{   
    /**
     * Query the related orderable items.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function saleables()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Add new item to order.
     * 
     * @param \Armincms\Orderable\Contracts\Saleable    $saleable 
     * @param int|integer $count    
     */
    public function add(Saleable $saleable, int $count = 1)
    {
        $this->saleables()->whereHasMorph('saleable', [$saleable->getMorphClass()], function($query) use ($saleable) {
            return $query->whereKey($saleable->id);
        })->updateOrCreate([
            'saleable_id' => $saleable->id,
            'saleable_type' => $saleable->getMorphClass(),
        ],[
            'count'         => $count,
            'currency'      => $saleable->currency(),
            'sale_price'    => $saleable->salePrice(),
            'old_price'     => $saleable->oldPrice(),
            'description'   => $saleable->description(),
            'name'          => $saleable->name(),
        ]);

        return $this;
    } 
}
