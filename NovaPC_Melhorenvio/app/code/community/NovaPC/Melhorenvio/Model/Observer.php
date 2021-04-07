<?php

class NovaPC_Melhorenvio_Model_Observer
{
	public function getOrderSaveAfter(Varien_Event_Observer $event)
	{
		$order = $event->getEvent()->getOrder();
		if($order->getStatus() == 'canceled') {
			$etiquetaExists = Mage::helper('melhorenvio')->isEtiquetaValidFromOrder($order->getId());
		}
	}
}

?>