<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_DeclararConteudo extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $url = $this->getUrl(
            'melhorenvio/adminhtml_orders/savedeclararconteudo',
            array(
                'model' => 'melhorenvio_model',
                'increment_id' => $row->getIncrementId(),
                'shipping_method' => $row->getShippingDescription()
            )
        );

        // $html = parent::render($row);
        $html = '<select id="declararconteudo' . $row->getIncrementId() . '" name="select_declarar_conteudo" onchange="saveModel(event, \''.$url.'\')" '.($this->isSelectDisabled($row) ? 'disabled' : '').'>';
        $html .= '<option value="1" '. ($this->validateRowOption($row) == true ? "selected" : "").' >Sim</option>';
        $html .= '<option value="0" '. ($this->validateRowOption($row) == false ? "selected" : "").' >Não</option>';
        $html .= '</select>';
        return $html;
    }

    protected function validateRowOption(Varien_Object $row)
    {
        $helper = Mage::helper("melhorenvio");

        if($helper->isPrivateCarrier($row->getShippingDescription())) {
            $this->savingOnModel($row);
        }

        return $row->getDeclararConteudo();
    }

    protected function isSelectDisabled(Varien_Object $row)
    {
        $helper = Mage::helper("melhorenvio");
        
        // se etiqueta já foi gerada, não pode mudar o select
        $ml_id = $row->getMelhorenvioId();
        if(!is_null($ml_id)) {
            return true;
        }

        if(!empty($row->getData("chave_nfe")) || !is_null($row->getData("chave_nfe"))){
            return true;
        }

        if($row->getStatusMe() == 'Cancelado') {
            return true;
        }
        
        return false;
    }

    protected function savingOnModel(Varien_Object $row)
    {
        $order = Mage::getModel('melhorenvio/orders')->load($row->getIncrementId(), 'increment_id');
        if(is_null($order->getIncrementId())) {
            try {
                $order->setData('increment_id', $row->getIncrementId())->save();
            } catch(Exception $e) {
                Mage::log($e->getMessage(), null, 'melhorenvio_error_to_save_order.log');
            }
        }
    }
}