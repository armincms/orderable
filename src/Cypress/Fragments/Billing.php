<?php

namespace Armincms\Orderable\Cypress\Fragments;
 
use Armincms\Contract\Concerns\InteractsWithModel; 
use Zareismail\Cypress\Fragment; 
use Zareismail\Cypress\Contracts\Resolvable; 

class Billing extends Fragment implements Resolvable
{   
    use InteractsWithModel;

    /**
     * Get the resource Model class.
     * 
     * @return
     */
    public function model(): string
    {
        return \Armincms\Orderable\Models\OrderableOrder::class;
    } 

    /**
     * Apply custom query to the given query.
     *
     * @param  \Zareismail\Cypress\Http\Requests\CypressRequest $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyQuery($request, $query)
    {
        return $query->with([
            'items',  
        ]);
    } 
}
