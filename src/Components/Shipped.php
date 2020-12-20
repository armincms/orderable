<?php 
namespace Armincms\Orderable\Components;
 
use Illuminate\Http\Request; 
use Core\HttpSite\Component;
use Component\Blog\Blog;
use Core\HttpSite\Contracts\Resourceable;
use Core\HttpSite\Concerns\IntractsWithResource;
use Core\HttpSite\Concerns\IntractsWithLayout;
use Core\Document\Document; 
use Armincms\Orderable\Models\Order;
use Armincms\Arminpay\Models\ArminpayGateway;
use Armincms\Orderable\Events\OrderCompleted;

class Shipped extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = '{id}/shipped';

	public function toHtml(Request $request, Document $docuemnt) : string
	{  
		$order = Order::viaCode($request->route('id'))->with('saleables', 'orderable', 'transactions')
							->firstOrFail();

		$order->asCompleted()->save();

		event(new OrderCompleted($order));

		$this->resource($order);  

		$docuemnt->title($order->trackingCode()); 
		$docuemnt->description($order->trackingCode());  

		return  $this->firstLayout($docuemnt, $this->config('layout'), 'clean-shipped')
					->display($order->toArray())
					->toHtml(); 
	} 

	public function method()
	{
	 	return 'any';
	}

	public function trackingCode()
	{
	 	return $this->resource->trackingCode();
	} 

	public function finishCallback()
	{
	 	return $this->resource->finish_callback;
	} 

	public function saleables()
	{
	 	return $this->resource->saleables;
	} 

	public function transactions()
	{
	 	return $this->resource->transactions;
	}   
}
