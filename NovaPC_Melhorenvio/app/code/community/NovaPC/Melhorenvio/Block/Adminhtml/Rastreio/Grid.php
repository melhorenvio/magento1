<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Rastreio_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('rastreioGrid'); 
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(200);
        $this->setVarNameFilter('rastreio_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }        
    
    protected function _prepareCollection() 
    {
        $collection = Mage::getModel('melhorenvio/orders')->getCollection()->getCustomOrders()
            ->addFieldToFilter('me.status', array('nin' => array(NULL, 'Cancelado')));
        $this->setCollection($collection);
             
        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns() 
    {
        $this->addColumn(
            'increment_id',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Id Pedido'),
                'index' => 'increment_id',
                'type'  => 'number',
            )
        );

        $this->addColumn(
            'status_me',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Status Pedido'),
                'index' => 'status_me',
                'type'  => 'text',
            )
        );

        $this->addColumn(
            'order_id',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Id Pedido Melhor Envio'),
                'index' => 'order_id',
                'type'  => 'text',
            )
        );

        $this->addColumn(
            'chave_nfe',
             array(
              'header' => Mage::helper('melhorenvio')->__('Chave NF-e'),
              'index' => 'chave_nfe',
              'type' => 'text'
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {

        return $this;
    }

    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }
    
    public function getGridUrl() 
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id'=>$row->getIncrementId()));
    }
}