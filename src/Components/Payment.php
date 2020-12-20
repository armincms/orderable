<?php 
namespace Armincms\Orderable\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component; 
use Core\Document\Document; 
use Armincms\Orderable\Models\Order;
use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\Arminpay\Transaction;

class Payment extends Component
{        
	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = '{id}/payment';

	public function toHtml(Request $request, Document $docuemnt) : string
	{           
		$order = Order::viaCode($request->route('id'))->with('saleables', 'orderable')->firstOrFail();
 

		return $order->isCompleted()
					? redirect(app('site')->get('orders')->url($order->trackingCode(). '/shipped'))
					: ArminpayGateway::findOrFail($request->gateway)->checkout($request, $order); 
	} 

	public function method()
	{
	 	return 'post';
	}  
}
