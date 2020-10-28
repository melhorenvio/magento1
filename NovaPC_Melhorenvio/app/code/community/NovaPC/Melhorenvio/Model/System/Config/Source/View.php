<?php

class NovaPC_Melhorenvio_Model_System_Config_Source_View
{
	public function toOptionArray()
    {
        $servicos = array();
        $retorno = Mage::helper('melhorenvio')->getServicos();
        foreach($retorno as $key => $ret){
            $servicos[$key] = array("value" => $ret->id, "label" => $ret->company->name." - ".$ret->name);
        }

        return $servicos;
    }
    
}