<?php

namespace Armincms\Orderable\Contracts;
  

interface Levy
{
	/**
	 * Get the amount of the tax.
	 * 
	 * @return decimal
	 */
	public function rate(): float; 

	/**
	 * Get the carrier name.
	 * 
	 * @return decimal
	 */
	public function name(): string; 
}