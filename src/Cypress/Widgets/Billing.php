<?php

namespace Armincms\Orderable\Cypress\Widgets;
  
use Armincms\Arminpay\Models\ArminpayGateway;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Select;    
use Zareismail\Cypress\Http\Requests\CypressRequest; 
use Zareismail\Gutenberg\Gutenberg; 
use Zareismail\Gutenberg\GutenbergWidget; 

class Billing extends GutenbergWidget
{      
    /**
     * The logical group associated with the widget.
     *
     * @var string
     */
    public static $group = 'Billing';

    /**
     * Bootstrap the resource for the given request.
     * 
     * @param  \Zareismail\Cypress\Http\Requests\CypressRequest $request 
     * @param  \Zareismail\Cypress\Layout $layout 
     * @return void                  
     */
    public function boot(CypressRequest $request, $layout)
    {   
        parent::boot($request, $layout); 

        $this->renderable(function($request) {
            return ! is_null($request->resolveFragment()->metaValue('resource'));
        });
    } 

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public static function fields($request)
    {  
        return [  
        ];
    } 

    /**
     * Serialize the widget fro template.
     * 
     * @return array
     */
    public function serializeForDisplay(): array
    {  
        $order = $this->getRequest()->resolveFragment()->metaValue('resource'); 

        return array_merge($order->serializeForWidget($this->getRequest()), [
            'gates' => ArminpayGateway::enable()->get()->map(function($gateway) {
                return array_merge($gateway->toArray(), [
                    'name' => $gateway->name
                ]);
            }),
            'form' => [
                'action' => route('orderable.order.register', $order->trackingCode()),
                'method' => 'post',
                'gate_field' => 'gateway',
                'csrf_token_field' => '_token',
                'csrf_token' => csrf_token(),
            ],
            'old' => session()->getOldInput(),
            'showMessage' => session()->has('success') || session()->has('errors'),
            'success' => session('success'),
            'message' => session('message'),
            'errors' => $this->validationErrors($this->getRequest()),
        ]);
    }

    /**
     * Query related tempaltes.
     * 
     * @param  $request [description]
     * @param  $query   [description]
     * @return          [description]
     */
    public static function relatableTemplates($request, $query)
    {
        return $query->handledBy(
            \Armincms\Orderable\Gutenberg\Templates\BillingWidget::class
        );
    } 

    /**
     * Request validation errors.
     * 
     * @param  CypressRequest $request 
     * @return array                  
     */
    protected function validationErrors(CypressRequest $request)
    {
        if (is_null($errors = $request->session()->get('errors'))) {
            return [];
        }

        return collect($errors->messages())->map(function($errors, $field) {
            return $errors[0] ?? null;
        })->toArray();
    }
}
