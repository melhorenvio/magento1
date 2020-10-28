<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Rastreio_Edit extends Mage_Adminhtml_Block_Template
{
    
	public function getRastreio()
	{
		$params = $this->getRequest()->getParams();
		$model = Mage::getModel('melhorenvio/orders')->load($params['id'], 'increment_id');

		$ordersId = $model->getData('melhorenvio_id');
		$ordersId = explode(',', $ordersId);

		$retorno = array();

		foreach($ordersId as $orderId) {
			if(!is_null($orderId)) {
				$rastreio = Mage::helper('melhorenvio')->webServiceRequest(Mage::helper('melhorenvio')->getEnvironment() . "api/v2/me/shipment/tracking", array("orders" => array($orderId)), "POST");
				array_push($retorno, array("pedido" => $params['id'], "rastreio" => $rastreio->$orderId));
			} else {
				array_push($retorno, array("pedido" => $params['id'], "rastreio" => null));
			}
		}
		
		return $retorno;
	}

	public function translateText($text)
	{
		$traduzidos = array(
			"Liberado para impressão" => "released",
			"Envio entregue" => "delivered",
			"Pendente de pagamento" => "pending",
			"Etiqueta cancelada" => "canceled",
			"Envio não entregue" => "undelivered",
			"Envio postado" => "posted",
			"Vazio" => "null"
		);

		$traduzido = array_search($text, $traduzidos);

		return $traduzido == "" ? $text : $traduzido;
	}
}