<?php

namespace Armincms\Orderable\Models;
 

trait Progressable 
{    
    /**
     * Mark the model with the "INPROGRESS" value.
     *
     * @return $this
     */
    public function inProgress()
    {
        return $this->markAs($this->getInProgressValue());
    } 

    /**
     * Determine if the value of the model's "marked as" attribute is equal to the "INPROGRESS" value.
     * 
     * @param  string $value 
     * @return bool       
     */
    public function inProgressing()
    {
        return $this->markedAs($this->getInProgressValue());
    }

    /**
     * Query the model's `marked as` attribute with the "INPROGRESS" value.
     * 
     * @param  \Illuminate\Database\Eloquent\Builder $query  
     * @return \Illuminate\Database\Eloquent\Builder       
     */
    public function scopeInProgressing($query)
    {
        return $this->mark($this->getInProgressValue());
    }

    /**
     * Set the value of the "marked as" attribute as "INPROGRESS" value.
     * 
     * @return $this
     */
    public function setInProgress()
    {
        return $this->setMarkedAs($this->getInProgressValue());
    }

    /**
     * Get the value of the "INPROGRESS" mark.
     *
     * @return string
     */
    public function getInProgressValue()
    {
        return defined('static::INPROGRESS_VALUE') ? static::INPROGRESS_VALUE : 'INPROGRESS';
    }
}
