<?php

class NovaPC_Melhorenvio_Model_System_Config_Source_Assegurarvalor
{
    public function toOptionArray()
    {
        return array(
            array("value" => 1, "label" => "Sempre assegurar"),
            array("value" => 0, "label" => "Assegurar se necessário")
        );
    }
}