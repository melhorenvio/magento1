<?php

class NovaPC_Melhorenvio_Model_Mysql4_Orders_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct(){
        $this->_init('melhorenvio/orders');
    }

    protected function _getClearSelect(){
        return $this->_buildClearSelect();
    }

    protected function _buildClearSelect($select = null){
        if (empty($select)) {
            $select = clone $this->getSelect();
        }

        $select->reset(Zend_Db_Select::ORDER);
        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $select->reset(Zend_Db_Select::LIMIT_OFFSET);
        $select->reset(Zend_Db_Select::COLUMNS);

        return $select;
    }

    public function getCustomOrders(){
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->getSelect()->joinLeft(array('me' => 'melhorenvio_orders'),'main_table.increment_id = me.increment_id', "")
        ->columns(['me.id', 'main_table.increment_id','me.melhorenvio_id','main_table.created_at', 'COALESCE(me.status, "Pendente") as status_me', 'me.nmr_nf', 'me.url_etiqueta', 'me.chave_nfe', 'me.declarar_conteudo', 'me.assegurar_valor', 'me.order_id','main_table.base_shipping_amount', 'main_table.shipping_description', 'main_table.entity_id as sales_order_id'])
        ->where("main_table.shipping_method LIKE 'melhorenvio_%'");
        
       return $orderCollection; 
    }

    public function getCustomOrdersByIncrementId($incrementId){
        $orderCollection = Mage::getModel('sales/order')->getCollection();
        $orderCollection->getSelect()->joinLeft(array('me' => 'melhorenvio_orders'),'main_table.increment_id = me.increment_id', "")
        ->columns(['me.increment_id as me_increment_id','me.status as me_status','me.*','main_table.*'])
        ->where("main_table.increment_id ='{$incrementId}'");
        return $orderCollection; 
    }

    public function loadByIncrementId($incrementId){
        $matches = $this->getResourceCollection()
            ->addFieldToFilter('increment_id', $incrementId);

        foreach ($matches as $match) {
            return $this->load($match->getId());
        }   

        return $this->setData('increment_id', $incrementId);
    }


    public function getOrdersByStatus($status = null){
        $orderCollection = Mage::getModel('melhorenvio/orders')->getCollection();

        if($status == null){
            $orderCollection->getSelect()
            ->columns('*')
            ->where("status NOT IN ('Pendente', 'Cancelado')");

        }else{
            $orderCollection->getSelect()
            ->columns('*')
            ->where("status = '{$status}'");            
        }
        
       return $orderCollection; 
    }

}