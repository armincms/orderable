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
        return $this->addItem($saleable, $count);
    } 

    /**
     * Add new item to order.
     * 
     * @param \Armincms\Orderable\Contracts\Saleable    $saleable 
     * @param int|integer $count    
     */
    public function addItem(Saleable $saleable, int $count = 1)
    {
        $this->newItemQuery($saleable)->updateOrCreate([
            'saleable_id' => $saleable->id,
            'saleable_type' => $saleable->getMorphClass(),
        ],[
            'currency'      => $saleable->saleCurrency(),
            'sale_price'    => $saleable->salePrice(),
            'old_price'     => $saleable->oldPrice(),
            'description'   => $saleable->saleDescription(),
            'name'          => $saleable->saleName(),
            'count'         => 0,    
        ]); 

        return tap($this->newItemQuery($saleable)->first(), function($item) use ($count) {
            $item->update([
                'count' => /*$item->wasRecentlyCreated ? $count :*/ $count + $item->count
            ]);
        }); 
    } 

    public function newItemQuery(Saleable $saleable)
    {
        return $this->saleables()->whereHasMorph('saleable', [$saleable->getMorphClass()], function($query) use ($saleable) {
            return $query->whereKey($saleable->id);
        });
    }
}
