<?php

namespace Armincms\Orderable\Contracts;
  

interface Saleable
{
	/**
	 * Get the sale price currency.
	 * 
	 * @return decimal
	 */
	public function currency(): string;

	/**
	 * Get the sale price of the item.
	 * 
	 * @return decimal
	 */
	public function salePrice(): float;

	/**
	 * Get the real price of the item.
	 * 
	 * @return decimal
	 */
	public function oldPrice(): float;

	/**
	 * Get the item name.
	 * 
	 * @return decimal
	 */
	public function name(): string;

	/**
	 * Get the item description.
	 * 
	 * @return decimal
	 */
	public function description(): string;
}