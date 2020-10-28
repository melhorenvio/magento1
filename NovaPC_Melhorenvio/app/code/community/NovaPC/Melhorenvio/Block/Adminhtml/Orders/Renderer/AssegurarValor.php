<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_AssegurarValor extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $url = $this->getUrl(
            'melhorenvio/adminhtml_orders/saveassegurarvalor',
            array(
                'model' => 'melhorenvio_model',
                'increment_id' => $row->getIncrementId()
            )
        );
        // $html = parent::render($row);
        $html = '<select name="assegurar_valor" onchange="saveModel(event, \''.$url.'\')" '.($this->isSelectDisabled($row) ? 'disabled' : '').'>';
        $html .= '<option value="1" '. ($this->validateRowOption($row) ? "selected" : "").' >Sim</option>';
        $html .= '<option value="0" '. (!$this->validateRowOption($row) ? "selected" : "").' >Não</option>';
        $html .= '</select>';
        return $html; 
    }

    protected function validateRowOption(Varien_Object $row)
    {
        $helper = Mage::helper("melhorenvio");

        // se etiqueta já foi gerada, não pode mudar o select
        $ml_id = $row->getMelhorenvioId();
        $assegurarValor = $row->getAssegurarValor();
        if(!is_null($ml_id)) {
            return $assegurarValor;
        }

        if($helper->isPrivateCarrier($row->getShippingDescription())) {
            $this->savingOnModel($row->getIncrementId());
            return true;
        }

        return $row->getAssegurarValor();
    }

    protected function isSelectDisabled(Varien_Object $row)
    {
        $helper = Mage::helper("melhorenvio");
        
        // se etiqueta já foi gerada, não pode mudar o select
        $ml_id = $row->getMelhorenvioId();
        if(!is_null($ml_id)) {
            return true;
        }

        if($helper->isPrivateCarrier($row->getShippingDescription())) {
            $this->savingOnModel($row->getIncrementId());
            return true;
        }

        if($row->getStatusMe() == 'Cancelado') {
            return true;
        }

        return false;
    }

    protected function savingOnModel($incrementId)
    {
        $order = Mage::getModel('melhorenvio/orders')->load($incrementId, 'increment_id');
        if(!is_null($order->getId()) && !$order->getData('assegurar_valor')) {
            $order->setData('assegurar_valor', true);
            try {
                $order->save();
            } catch(Exception $e) {
                Mage::log($e->getMessage(), null, 'melhorenvio_error_to_save_order.log');
            }
        } else {
            try {
                $order
                    ->setData('increment_id', $incrementId)
                    ->setData('assegurar_valor', true)
                    ->save();
            } catch(Exception $e) {
                Mage::log($e->getMessage(), null, 'melhorenvio_error_to_save_order.log');
            }
        }
    }
}