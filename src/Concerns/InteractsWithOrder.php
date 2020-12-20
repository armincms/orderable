<?php

namespace Armincms\Orderable\Concerns;
  

trait InteractsWithOrder
{
	/**
	 * Get the price of the item.
	 * 
	 * @return decimal
	 */
	public function price(): float
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
		return floatval($this->old_price) ?: $this->price();
	}

	/**
	 * Get the item name.
	 * 
	 * @return decimal
	 */
	public function name(): string
	{
		return strval($this->name);
	}

	/**
	 * Get the item description.
	 * 
	 * @return decimal
	 */
	public function description(): string
	{
		return strval($this->description) ?: $this->name();
	}
}