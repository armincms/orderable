<?php

namespace Armincms\Orderable\Http\Controllers;

use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\Orderable\Models\OrderableOrder;
use Armincms\Orderable\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Update the user profile
     *
     * @return array
     */
    public function __invoke(RegisterRequest $request, $order)
    {
        try { 
            return ArminpayGateway::enable()
                ->findOrFail($request->gateway)
                ->checkout($request, OrderableOrder::tracking($order)->firstOrFail());
        } catch (\Exception $e) {
            return back()->with([
                'success' => false,
                'message' => strval($e->getMessage()),
            ]);
        }
    }
}
