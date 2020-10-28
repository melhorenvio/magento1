<?php
class NovaPC_Melhorenvio_Model_Validate_Address extends Mage_Core_Model_Config_Data
{
    public function save()
    {
        if($this->getValue() == '...'){
            $fieldName = ucfirst($this->getField());

            switch($fieldName){
                case "Rua":
                    $fieldName = "Endereço";
                    break;
                case "Estado":
                    $fieldName = "Estado / UF";
                    break;
            }

            Mage::throwException("As configurações não foram salvas pois o campo '" . $fieldName . "' não pode ser igual a '...'.");
        }

        return parent::save();
    }
}