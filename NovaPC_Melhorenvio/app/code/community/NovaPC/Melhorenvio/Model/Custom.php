<?php

class NovaPC_Melhorenvio_Model_Custom extends Mage_Core_Model_Config_Data
{
    public function _afterSave()
    {
        $lucro = $this->getValue();
        if(!is_null($lucro)) {
            $lucro = str_replace(',', '', $lucro);
            $lucro = str_replace('.', '', $lucro);
            
            if(!preg_match('/^[0-9]+$/', $lucro)) {
                Mage::throwException("Digite o campo \"Porcentagem de lucro\" corretamente.");
            }
        }

        return parent::_afterSave();
    }
}