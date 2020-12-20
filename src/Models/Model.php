<?php

namespace Armincms\Orderable\Models;

use Illuminate\Database\Eloquent\{Model as LaravelModel, SoftDeletes};
use Armincms\Orderable\Orderable;
use Zareismail\Markable\{Markable, HasDraft, HasPending};

class Model extends LaravelModel
{
	use SoftDeletes;
    use Markable, HasDraft, HasPending, HasCompletion;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [];

    public function customer()
    {
    	$customerResource = config('orderable.resources.customer');

    	return $this->belongsTo($customerResource::$model);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return Orderable::table(parent::getTable());
    } 
}
