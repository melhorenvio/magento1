<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Float extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Returns html part of the setting
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $html = '<input type="text" class="input-text" id="'.$this->getElement()->getHtmlId().'" name="'.$this->getElement()->getName().'" value="'.$this->getElement()->getValue().'" />';
        return $html;
    }

    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/' . $key);
    }

    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
    }

    protected function addMessageError()
    {
        return Mage::getSingleton('core/session')->addError("Você digitou o campo lucro incorretamente.");
    }
}