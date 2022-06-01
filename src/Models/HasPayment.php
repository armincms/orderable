<?php

namespace Armincms\Orderable\Models;
 
/**
 * When Order pending for payment going to the `onhold` status. 
 * This means stock decreased and wait for payment.
 * When need to validate order for decrease stock, order go to the `pending` status
 */
trait HasPayment 
{    
    /**
     * Mark the model with the "onhold" value.
     *
     * @return $this
     */
    public function asOnHold()
    {
        return $this->markAs($this->getOnHoldValue());
    } 

    /**
     * Determine if the value of the model's "marked as" attribute is equal to the "onhold" value.
     * 
     * @param  string $value 
     * @return bool       
     */
    public function isOnHold()
    {
        return $this->markedAs($this->getOnHoldValue());
    }

    /**
     * Query the model's `marked as` attribute with the "onhold" value.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query  
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeOnHold($query)
    {
        return $this->mark($this->getOnHoldValue());
    }

    /**
     * Set the value of the "marked as" attribute as "onhold" value.
     * 
     * @return $this
     */
    public function setOnHold()
    {
        return $this->setMarkedAs($this->getOnHoldValue());
    }
    /**
     * Mark the model with the "verified" value.
     *
     * @return $this
     */
    public function asVerified()
    {
        return $this->markAs($this->getVerifiedValue());
    } 

    /**
     * Determine if the value of the model's "marked as" attribute is equal to the "verified" value.
     * 
     * @param  string $value 
     * @return bool       
     */
    public function isVerified()
    {
        return $this->markedAs($this->getVerifiedValue());
    }

    /**
     * Query the model's `marked as` attribute with the "verified" value.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query  
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeVerified($query)
    {
        return $this->mark($this->getVerifiedValue());
    }

    /**
     * Set the value of the "marked as" attribute as "verified" value.
     * 
     * @return $this
     */
    public function setVerified()
    {
        return $this->setMarkedAs($this->getVerifiedValue());
    }

    /**
     * Get the value of the "onhold" mark.
     *
     * @return string
     */
    public function getOnHoldValue()
    {
        return defined('static::ONHOLD_VALUE') ? static::ONHOLD_VALUE : 'onhold';
    }

    /**
     * Get the value of the "onhold" mark.
     *
     * @return string
     */
    public function getVerifiedValue()
    {
        return defined('static::VERIFIED_VALUE') ? static::VERIFIED_VALUE : 'verified';
    }
}
