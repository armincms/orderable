<?php

namespace Armincms\Orderable\Nova\Resolvers;

use Laravel\Nova\Nova;
use Whitecube\NovaFlexibleContent\Value\ResolverInterface;
use Armincms\Orderable\Orderable; 

class Saleable implements ResolverInterface
{
    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Whitecube\NovaFlexibleContent\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    {
        if($orderableResource = Nova::resourceForModel($resource->orderable)) {
            $relation = Orderable::resourceRelation($orderableResource);

            $resource->relationLoaded($relation) || $resource->load($relation); 

            return $resource->getRelation($relation)->map(function($saleable) use ($layouts) {
                $layout = $layouts->find('products');

                if(!$layout) return;

                return $layout->duplicateAndHydrate($saleable->id, [
                    'items' => $saleable->id,
                    'count' => $saleable->pivot->count,
                ]);
            })->filter(); 
        } 

        return collect([]); 
    }

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return string
     */
    public function set($model, $attribute, $groups)
    { 
        if($orderableResource = Nova::resourceForModel($model->orderable_type)) {  
            $groups = $groups->map->getAttributes()->pluck('count', 'items'); 
            $saleables = Orderable::saleableQuery($orderableResource::newModel()->findOrNew($model->orderable_id))
                            ->whereKey($groups->keys())->get();

            $model::saved(function($model) use ($saleables, $groups, $orderableResource) {
                $relationShip = call_user_func([$model, Orderable::resourceRelation($orderableResource)]);

                $relationShip->sync($saleables->keyBy('id')->map(function($saleable, $id) use ($groups) {
                    return [
                        'sale_price' => $saleable->salePrice(),
                        'old_price' => $saleable->oldPrice(),
                        'name' => $saleable->name(),
                        'description' => $saleable->description(),
                        'count' => intval($groups->get($id)),
                    ];
                })->all());
            }); 
        }  
    }
}
