<?php
/**
 * @package modules.order
 * @method order_ShippingaddressService getInstance()
 */
class order_ShippingaddressService extends customer_AddressService
{
	/**
	 * @return order_persistentdocument_shippingaddress
	 */
	public function getNewDocumentInstance()
	{
		return $this->getNewDocumentInstanceByModelName('modules_order/shippingaddress');
	}

	/**
	 * Create a query based on 'modules_order/shippingaddress' model.
	 * Return document that are instance of modules_order/shippingaddress,
	 * including potential children.
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_order/shippingaddress');
	}
	
	/**
	 * Create a query based on 'modules_order/shippingaddress' model.
	 * Only documents that are strictly instance of modules_order/shippingaddress
	 * (not children) will be retrieved
	 * @return f_persistentdocument_criteria_Query
	 */
	public function createStrictQuery()
	{
		return $this->getPersistentProvider()->createQuery('modules_order/shippingaddress', false);
	}
	
	/**
	 * @param customer_persistentdocument_address $address
	 * @param boolean $includeTitle
	 * @return string
	 */
	public function getFullName($address, $includeTitle = true)
	{
		if ($address->getFirstname() != '-' && $address->getLastname() != '-')
		{
			return parent::getFullName($address, $includeTitle);
		}
		return $address->getLabel();
	}
	
	/**
	 * @param order_persistentdocument_shippingaddress $document
	 * @param integer $parentNodeId Parent node ID where to save the document (optionnal => can be null !).
	 * @return void
	 */
	protected function preSave($document, $parentNodeId)
	{
		parent::preSave($document, $parentNodeId);
		
		if (!$document->getFirstname())
		{
			$document->setFirstname('-');
		}
		if (!$document->getLastname())
		{
			$document->setFirstname('-');
		}
		if (!$document->getEmail())
		{
			$document->setEmail(Framework::getDefaultNoReplySender());
		}
	}
}