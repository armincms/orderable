<?php

namespace Armincms\Orderable\Concerns;

use Armincms\Orderable\Models\Order;

trait InteractsWithInvoice
{
	/**
	 * Query the related invoices.
	 * 
	 * @return \Illuminate\Database\Eloquent\Relations\HasOneOrMany
	 */
	public function invoice()
	{
		return $this->belongsTo(Order::class, 'invoice_id');
	}

	/**
	 * Get the price of the item.
	 * 
	 * @return decimal
	 */
	public function salePrice(): float
	{
		return floatval($this->price);
	}

	/**
	 * Get the real price of the item.
	 * 
	 * @return decimal
	 */
	public function oldPrice(): float
	{
		return floatval($this->old_price) ?: floatval($this->price);
	}

	/**
	 * Get the item name.
	 * 
	 * @return decimal
	 */
	public function slaeName(): string
	{
		return strval($this->name);
	}

	/**
	 * Get the item description.
	 * 
	 * @return decimal
	 */
	public function saleDescription(): string
	{
		return strval($this->description) ?: $this->name();
	}
}