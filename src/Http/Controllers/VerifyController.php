<?php

namespace Armincms\Orderable\Http\Controllers;

use Armincms\Arminpay\Models\ArminpayTransaction;
use Armincms\Orderable\Events\OrderVerified;
use Armincms\Orderable\Models\OrderableOrder;
use Armincms\Orderable\Nova\Billing;
use Armincms\Orderable\Http\Requests\VerifyRequest;

class VerifyController extends Controller
{
    /**
     * Update the user profile
     *
     * @return array
     */
    public function __invoke(VerifyRequest $request, $trackingCode)
    { 
        $order = OrderableOrder::tracking($trackingCode)->firstOrFail();

        $transaction = ArminpayTransaction::whereHasMorph(
            "billable",
            [OrderableOrder::class],
            function ($query) use ($trackingCode) {
                return $query->tracking($trackingCode);
            }
        )->firstOrFail();

        if ($transaction->isFailed()) {
            return $order->redirect($request, true)->with([
                'success' => false,
                'message' => __("Payment verification failed.")
            ]);
        }

        OrderVerified::dispatch($order->asVerified());

        return redirect($order->callback_url);
    }
}
