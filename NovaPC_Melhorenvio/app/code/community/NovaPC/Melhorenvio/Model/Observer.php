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

	public function validateDataSavedInSystemXml(Varien_Event_Observer $event)
	{
		$section = Mage::app()->getRequest()->getParams();
		if($section['section'] === 'melhorenvio') {
			$empresaSystem = $section['groups']['empresa']['fields'];

			$dataMelhorEnvio = Mage::helper('melhorenvio')->getUserDataFromMelhorEnvio();

			$cpfMagento = $empresaSystem['cpf']['value'];
			$cpfMelhorEnvio = $dataMelhorEnvio->document;
			$cnpjMagento = $empresaSystem['cnpj']['value'];
			$cnpjMelhorEnvio = $dataMelhorEnvio->company_document;

			$session = Mage::getSingleton('adminhtml/session');
			
			if($this->cleanCpfCnpj($cnpjMagento) != $cnpjMelhorEnvio) {
				$session->addWarning('O CNPJ está diferente do cadastrado na Melhor Envio.');
			}
		}
	}

	protected function cleanCpfCnpj($cpfcnpj)
	{
		return preg_replace('/[.\-,\/]/', '', $cpfcnpj);
	}
}

?>