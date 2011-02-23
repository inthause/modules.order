<?php
/**
 * order_AddToCartMultipleAction
 * @package modules.order.actions
 */
class order_AddToCartMultipleAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		// Get parameters.
		$cs = order_CartService::getInstance();
		$cart = $cs->getDocumentInstanceFromSession();
		$shop = DocumentHelper::getDocumentInstance($request->getParameter('shopId'), 'modules_catalog/shop');
		$shopService = $shop->getDocumentService();
		$products = $this->getProductsFromRequest($request);
		$quantities = $this->getQuantitiesFromRequest($request, $products);
		$backUrl = $request->getParameter('backurl');
		
		// No product configuration : products are added with their default values.
		// TODO: handle product configuration here?
		
		// Check.
		$paramsToRedirect = $request->getParameters();
		$paramsToRedirect['addToCartModule'] = $this->getModuleName();
		$paramsToRedirect['addToCartAction'] = $this->getActionName();
		unset($paramsToRedirect['module']);
		unset($paramsToRedirect['action']);
		unset($paramsToRedirect['lang']);
		$cs->checkAddToCart($cart, $shop, $products, $quantities, true, $paramsToRedirect);

		// Add products.
		$productAdded = false;
		foreach ($products as $product)
		{
			if ($cs->addProductToCart($cart, $product, $quantities[$product->getId()]))
			{
				$this->addedProductLabels[] = $product->getLabelAsHtml();
				$productAdded = true;
			}
			else
			{
				$this->notAddedProductLabels[] = $product->getLabelAsHtml();
			}
		}
		
		// Refresh the cart.
		if ($productAdded)
		{
			$cart->refresh();
			$this->addMessagesToCart($cart);
		}
		
		// Redirect.
		if (!$backUrl)
		{
			$backUrl = LinkHelper::getTagUrl('contextual_website_website_modules_order_cart');
		}
		HttpController::getInstance()->redirectToUrl($backUrl);
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @return catalog_persistentdocument_product
	 */
	protected function getProductsFromRequest($request)
	{
		$products = array();
		if ($request->hasParameter('productIds'))
		{
			foreach ($request->getParameter('productIds') as $id)
			{
				$products[] = DocumentHelper::getDocumentInstance($id, 'modules_catalog/product');
			}
		}
		return $products;
	}
	
	/**
	 * @param f_mvc_Request $request
	 * @return catalog_persistentdocument_product
	 */
	protected function getQuantitiesFromRequest($request, $products)
	{
		$quantities = array();
		$quantitiesFormParam = $request->getParameter('quantities');
		foreach ($products as $product)
		{
			$productId = $product->getId();
			$quantities[$product->getId()] = 1;
			if (array_key_exists($product->getId(), $quantitiesFormParam))
			{
				$quantities[$product->getId()] = max(intval($quantitiesFormParam[$productId]), 1);
			}
		}
		return $quantities;
	}
	
	/**
	 * @var string[]
	 */
	protected $addedProductLabels = array();
	
	/**
	 * @var string[]
	 */
	protected $notAddedProductLabels = array();
	
	/**
	 * @param order_CartInfos $cart
	 */
	protected function addMessagesToCart($cart)
	{
		$message = $this->getMessage($this->addedProductLabels, 'added');
		if ($message !== null)
		{
			$cart->addSuccessMessage($message);
		}
		$this->addedProductLabels = null;
		$message = $this->getMessage($this->notAddedProductLabels, 'not-added');
		if ($message !== null)
		{
			$cart->addWarningMessage($message);
		}
		$this->notAddedProductLabels = null;
	}
	
	/**
	 * @param Array<String> $labels
	 * @param String $mode
	 * @return String
	 */
	private function getMessage($labels, $eventname)
	{
		switch (count($labels))
		{
			case 0 :
				$message = null;
				break;
			case 1 :
				$message = LocaleService::getInstance()->transFO('m.order.fo.product-label-'.$eventname, array('ucf'), array('label' => f_util_ArrayUtils::firstElement($labels)));
				break;
			default :
				$lastLabel = array_pop($labels);
				$firstLabels = implode(', ', $labels);
				$message = LocaleService::getInstance()->transFO('m.order.fo.product-labels-'.$eventname, array('ucf'), array('firstLabels' => $firstLabels, 'lastLabel' => $lastLabel));
				break;
		}
		return $message;
	}
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}
}