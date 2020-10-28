<?php

class NovaPC_Melhorenvio_Model_System_Config_Source_Agency
{
	public function toOptionArray()
	{
		$transportadoras = Mage::getStoreConfig('carriers/melhorenvio/servicos');
		$transportadoras = explode(',', $transportadoras);
		$retorno = array();

		foreach($transportadoras as $id_transp) {
			if((int)$id_transp == 3 || (int)$id_transp == 4) {
				$service = Mage::helper('melhorenvio')->getServiceById($id_transp);
				$agencias = Mage::helper('melhorenvio')->getAgencies($service['company_id']);
				foreach($agencias as $key => $agencia) {
					$retorno[$key] = array("value" => $agencia->id, "label" => $service['name'] . " - " . $agencia->name);
				}
			}
		}

		return Mage::helper('melhorenvio')->array_msort($retorno,array('label'=>SORT_ASC));
	}
}

?>