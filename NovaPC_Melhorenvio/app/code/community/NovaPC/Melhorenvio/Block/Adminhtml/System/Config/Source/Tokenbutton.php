<?php

class NovaPC_Melhorenvio_Block_Adminhtml_System_Config_Source_Tokenbutton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Abstract $element)
    {
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('success')
            ->setLabel('Buscar')
            ->setOnClick("tokenButton(this)")
            ->toHtml();

        return $html;
    }
}