<?php
class order_UpdateCartAction extends f_action_BaseAction
{
	/**
	 * @param Context $context
	 * @param Request $request
	 */
	public function _execute($context, $request)
	{
		$moduleParams =	$request->getParameter('orderParam');
		$cartService = order_CartService::getInstance();
		$cart = $cartService->getDocumentInstanceFromSession();

		if (!is_null($cart))
		{
			
			// -- Lines management.
			if (array_key_exists('lines', $moduleParams))
			{
				foreach ($moduleParams['lines'] as $index => $cartLine)
				{
					// If there is no line for this index skip it.
					$cartLineInfo = $cart->getCartLine($index);
					if (is_null($cartLineInfo))
					{
						continue;
					}
					
					// TODO: add a configuration to allow floating quantities.
					$quantity = array_key_exists('quantity', $cartLine) ? $cartLine['quantity'] : null;
					if (!is_numeric($quantity) || $quantity != intval($quantity) || $quantity <= 0)
					{
						$cart->addWarningMessage(f_Locale::translate('&modules.order.frontoffice.Invalid-quantity;', array('quantity' => $quantity)));
						continue;
					}
					$quantity = intval($quantity);
					
					$productId = array_key_exists('productId', $cartLine) ? $cartLine['productId'] : null;
					
					// Basic lines.
					if ($cartLineInfo->isBasicLine())
					{
						if (!is_null($quantity) && !is_null($productId))
						{	
							$product = DocumentHelper::getDocumentInstance($productId);
							$cartService->updateLine($cart, $index, $product, $quantity);
						}
					}
				}
			}			
			
			// We must refresh here to remove the cart if it is empty.
			$cartService->refresh($cart);
		}

		$pageId = $request->getParameter(K::PAGE_REF_ACCESSOR, null);
		$url = null;
		if (is_numeric($pageId))
		{
			$url = LinkHelper::getUrl(DocumentHelper::getDocumentInstance($pageId));
		}
		
		$context->getController()->redirectToUrl(str_replace('&amp;', '&', $url));
		
		return View::NONE;		
	}	
	
	/**
	 * @return boolean
	 */
	public function isSecure()
	{
		return false;
	}	
}