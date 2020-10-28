<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Chavenfe extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $fieldDisabled = ($row->getDeclararConteudo() == 1 || $row->getStatusMe() == "Gerado");

        // $html = parent::render($row);
        $html = '<input type="text" name="chave_nfe" value="' . $row->getChave_nfe() . '" class="input-text "' . ($fieldDisabled ? ' disabled' : '') .  '>';
        $html .= '<img src="'.Mage::getBaseUrl('media').'melhorenvio/tick5.png" id="chavenfe'. $row->getIncrementId() .'"' . ($fieldDisabled ? ' class="chavenfe-disabled"' : '') . '>';
        return $html;
    }
}

?>