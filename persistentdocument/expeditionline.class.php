<?php
/**
 * Class where to put your custom methods for document order_persistentdocument_expeditionline
 * @package modules.order.persistentdocument
 */
class order_persistentdocument_expeditionline extends order_persistentdocument_expeditionlinebase 
{
	/**
	 * @param string $moduleName
	 * @param string $treeType
	 * @param array<string, string> $nodeAttributes
	 */
//	protected function addTreeAttributes($moduleName, $treeType, &$nodeAttributes)
//	{
//	}
	
	/**
	 * @param string $actionType
	 * @param array $formProperties
	 */
//	public function addFormProperties($propertiesNames, &$formProperties)
//	{	
//	}

	/**
	 * @return order_persistentdocument_orderline
	 */
	private function getOrderLine()
	{
		return DocumentHelper::getDocumentInstance($this->getOrderlineid(), 'modules_order/orderline');
	}
	
	/**
	 * @return string
	 */	
	public function getCodeReference()
	{
		return $this->getOrderLine()->getCodeReference();	
	}
	
	/**
	 * @return catalog_persistentdocument_product or null
	 */	
	public function getProduct()
	{
		return $this->getOrderLine()->getProduct();	
	}
	
	/**
	 * @return double
	 */	
	public function getOrderProductQuantity()
	{
		return $this->getOrderLine()->getQuantity();
	}	
	
	private $URL = null;
	
	/**
	 * @return string
	 */
	public function getURL()
	{
		return $this->URL;
	}

	/**
	 * @param string $URL
	 */
	public function setURL($URL)
	{
		$this->URL = $URL;
	}
	
	/**
	 * @return integer
	 */
	public function getURLClick()
	{
		if ($this->hasMeta('MediaAccessGranted'))
		{
			return $this->getMeta('MediaAccessGranted');
		}
		return 0;
	}
}