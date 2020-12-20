<?php

namespace Armincms\Orderable\Contracts;
  

interface Courier
{
	/**
	 * Get the cost of the dispatching.
	 * 
	 * @return decimal
	 */
	public function cost(): float; 

	/**
	 * Get the dispatcher name.
	 * 
	 * @return decimal
	 */
	public function name(): string; 
}