<?php
/**
 * Class where to put your custom methods for document order_persistentdocument_expedition
 * @package modules.order.persistentdocument
 */
class order_persistentdocument_expedition extends order_persistentdocument_expeditionbase 
{
	/**
	 * @return order_persistentdocument_expeditionline[]
	 */
	public function getLinesForDisplay()
	{
		return $this->getDocumentService()->getLinesForDisplay($this);
	}
	
	/**
	 * @return shipping_persistentdocument_mode
	 */
	public function getShippingMode()
	{
		$shippingId = intval($this->getShippingModeId());
		if ($shippingId > 0)
		{
			try 
			{
				return DocumentHelper::getDocumentInstance($shippingId, 'modules_shipping/mode');
			}
			catch (Exception $e)
			{
				Framework::exception($e);
			}
		}
		return null;
	}
	
	/**
	 * @return String
	 */
	public function getStatusLabel()
	{
		$key = '&modules.order.frontoffice.status.expedition.' . ucfirst($this->getStatus()) . ';';
		return f_Locale::translate($key);
	}
	
	/**
	 * @return String
	 */
	public function getBoStatusLabel()
	{
		$key = '&modules.order.frontoffice.status.expedition.' . ucfirst($this->getStatus()) . ';';
		return f_Locale::translateUI($key);
	}
	
	/**
	 * @return string
	 */
	public function getTrackingURL()
	{
		return str_replace('{NumeroColis}', $this->getTrackingNumber(), parent::getTrackingURL());
	}	
}