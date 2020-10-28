<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Etiqueta extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
	    $value =  $row->getData($this->getColumn()->getIndex());
	    $html = '<a href="'.$row->getUrlEtiqueta().'" target="_blank">'.$value.'</a>';
	    return $html;

    }
}

?>