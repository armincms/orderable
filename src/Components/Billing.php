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

class Billing extends Component implements Resourceable
{       
	use IntractsWithResource, IntractsWithLayout; 

	/**
	 * Route of Component.
	 * 
	 * @var null
	 */
	protected $route = '{id}/billing';

	public function toHtml(Request $request, Document $docuemnt) : string
	{         
		$order = Order::viaCode($request->route('id'))->with('saleables', 'orderable')->firstOrFail();

		$this->resource($order);  

		$docuemnt->title($order->trackingCode()); 
		$docuemnt->description($order->trackingCode());  

		return  $this->firstLayout($docuemnt, $this->config('layout'), 'clean-billing')
					->display($order->toArray())
					->toHtml(); 
	} 

	public function trackingCode()
	{
	 	return $this->resource->trackingCode();
	} 

	public function saleables()
	{
	 	return $this->resource->saleables;
	} 

	public function gateways()
	{
		return ArminpayGateway::enabled()->get();
	}

	public function billableName()
	{
		return $this->resource->getMorphClass();
	}
}
