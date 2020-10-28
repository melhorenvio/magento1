<?php
class NovaPC_Melhorenvio_Model_Validate_Cnpjorcpf extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        $section = Mage::app()->getRequest()->getParams();
        $empresaSystem = $section['groups']['empresa']['fields'];

        $cpf = $empresaSystem['cpf']['value'];
		$cnpj = $empresaSystem['cnpj']['value'];

        if(empty($cpf) && empty($cnpj)){
            Mage::throwException("As configurações não foram salvas pois o CNPJ ou CPF deve ser informado.");
        }

        return parent::save();
    }
}