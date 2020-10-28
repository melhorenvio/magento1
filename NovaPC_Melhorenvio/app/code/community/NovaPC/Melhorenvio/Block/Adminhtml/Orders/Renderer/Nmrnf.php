<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Nmrnf extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $html = parent::render($row);
        $html .= '<img src="'.Mage::getBaseUrl('media').'melhorenvio/tick.png" id="addName'. $row->getIncrementId() .'">';
        return $html;

    }
}

?>