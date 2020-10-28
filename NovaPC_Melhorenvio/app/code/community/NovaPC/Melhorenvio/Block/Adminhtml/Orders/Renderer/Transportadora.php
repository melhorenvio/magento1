<?php
class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Transportadora extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
		$value =  $row->getData($this->getColumn()->getIndex());
		$value = Mage::helper("melhorenvio")->WithdrawDeliveryTime($value);

		// if(count(explode("-", $value)) >= 3) {
		// 	$value = substr($value, strpos($value, "-") + 1);
		// }
		
		// if(substr($value, 0, 1) == " ") $value = substr($value, 1);
		
	    return $value;
    }
}

?>