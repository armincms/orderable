<?php

namespace Armincms\Orderable\Concerns;
  

trait HasTrackingCode
{ 
	/**
	 * Handles booting model.
	 * 
	 * @return void
	 */
	public static function bootHasTrackingCode()
	{
		static::saving(function($model) {
			$model->isTrackable() || $model->fillTrackingCode();
		});
	}

	/**
	 * Get the unique tracking code.
	 * 
	 * @return string
	 */
	public function trackingCode()
	{
		return $this->getAttribute($this->getTrackingCodeColumn());
	}  

	/**
	 * Determine if the model has tracking_code.
	 * 
	 * @return boolean
	 */
	public function isTrackable(): bool
	{
		return ! is_null($this->trackingCode());
	}

	/**
	 * Fill the tracking_code attribute.
	 * 
	 * @return $this
	 */
    public function fillTrackingCode()
    {
        $this->attributes[$this->getTrackingCodeColumn()] = $this->generateTrackingCode();

        return $this;
    } 

    /**
     * Generate unique string tracking_code.
     * 
     * @return string
     */
    public function generateTrackingCode()
    {
        while (static::viaCode($code = rand(999999,9999999))->whereKeyNot($this->id)->count());

        return $code;
    }

    /**
     * Query with the given tracking_code.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  string $code 
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeViaCode($query, string $code)
    {
        return $query->where($this->getQualifiedTrackingCodeColumn(), $code);
    }

    /**
     * Query where the tracking_code is not null.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query 
     * @return \Illuminate\Database\Eloquent\Builder        
     */
    public function scopeTrackable($query)
    {
        return $query->whereNotNull($this->getQualifiedTrackingCodeColumn());
    }

    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getTrackingCodeColumn()
    {
        return defined('static::TRACKING_CODE') ? static::TRACKING_CODE : 'tracking_code';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedTrackingCodeColumn()
    {
        return $this->qualifyColumn($this->getTrackingCodeColumn());
    }  
}