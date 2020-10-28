<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('ordersGrid');
        $this->setDefaultSort('increment_id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setDefaultLimit(200);
        $this->setVarNameFilter('orders_filter');
    }

    protected function _getStore()
    {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }        
    
    protected function _prepareCollection() 
    {
        Mage::helper('melhorenvio')->syncOrders();
        $collection = Mage::getModel('melhorenvio/orders')->getCollection()->getCustomOrders();
        $this->setCollection($collection);
        $this->getColumn('massaction')->setUseIndex(true);         
        parent::_prepareCollection();
        
        return $this;
    }

    protected function _IncrementIdFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;

        $filterValue = "%" . $column->getFilter()->getValue() . "%";

        $this->getCollection()->addFieldToFilter('main_table.increment_id', array('like' => $filterValue));
        return;
    }

    protected function _StatusEtiquetaFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;
        
        $filterValue = "%" . $column->getFilter()->getValue() . "%";

        if(preg_match("/" . strtolower($column->getFilter()->getValue()) . "/", "pendente")){
            $this->getCollection()->addFieldToFilter(array('me.status', 'me.status'), array(
                array('null' => true),
                array('like' => $filterValue)
            ));
        }else{
            $this->getCollection()->addFieldToFilter('me.status', array('like' => $filterValue));
        }

        return;
    }

    protected function _EtiquetaFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;

        $filterValue = "%" . $column->getFilter()->getValue() . "%";

        $this->getCollection()->addFieldToFilter('me.order_id', array('like' => $filterValue));
        return;
    }

    protected function _ChaveNFEFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;

        $filterValue = "%" . $column->getFilter()->getValue() . "%";

        $this->getCollection()->addFieldToFilter('me.chave_nfe', array('like' => $filterValue));
        return;
    }

    protected function _DeclararConteudoFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;

        $filterValue = strtolower($column->getFilter()->getValue());
        
        if(preg_match("/" . $filterValue . "/", "sim")){
            $filterValue = 1;
        }else if(preg_match("/" . $filterValue . "/", "não")){
            $filterValue = 0;
        }else{
            $filterValue = -1;
        }
        
        if($filterValue != -1){
            $query = "me.declarar_conteudo = " . $filterValue;

            if($filterValue == 0){
                $query .= " OR me.declarar_conteudo IS NULL";
            }
        
            $this->getCollection()->getSelect()->where($query);
        }else{
            $this->getCollection()->getSelect()->where("1 = 2");
        }

        return;
    }

    protected function _AssegurarValorFilter($collection, $column) {
        if(is_null($column->getFilter()->getValue())) return $this;

        $filterValue = strtolower($column->getFilter()->getValue());
        
        if(preg_match("/" . $filterValue . "/", "sim")){
            $filterValue = 1;
        }else if(preg_match("/" . $filterValue . "/", "não")){
            $filterValue = 0;
        }else{
            $filterValue = -1;
        }
        
        if($filterValue != -1){
            $query = "me.assegurar_valor = " . $filterValue;

            if($filterValue == 0){
                $query = "(" . $query . " OR me.assegurar_valor IS NULL) AND SUBSTRING_INDEX(main_table.shipping_description, '- ', -1) LIKE 'Correios %'";
            }
        
            $this->getCollection()->getSelect()->where($query);
        }else{
            $this->getCollection()->getSelect()->where("1 = 2");
        }

        return;
    }

    protected function _prepareColumns() 
    {
        $this->addColumn(
            'increment_id',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Id Pedido'),
                'index' => 'increment_id',
                'width' => '50px',
                'type'  => 'text',
                'filter_condition_callback' => array($this, '_IncrementIdFilter')
            )
        );

        $this->addColumn(
            'created_at',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Data'),
                'index' => 'created_at',
                'width' => 50,
                'type'  => 'date',
            )
        );

        $this->addColumn(
            'status_me',
            array(
                'header'=> Mage::helper('melhorenvio')->__('Status Etiqueta'),
                'index' => 'status_me',
                'width' => '50px',
                'type'  => 'text',
                'filter_condition_callback' => array($this, "_StatusEtiquetaFilter")
            )
        );

        $this->addColumn(
            'order_id',
             array(
              'renderer' => 'NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Etiqueta',
              'header' => Mage::helper('melhorenvio')->__('Etiqueta'),
              'index' => 'order_id',
              'filter_condition_callback' => array($this, "_EtiquetaFilter")
            )
        );

        $this->addColumn(
            'shipping_description',
             array(
                'renderer' => 'NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Transportadora',
                'header'=> Mage::helper('melhorenvio')->__('Transportadora'),
                'index' => 'shipping_description',
                'width' => '50px',
                'type'  => 'text',
            )
        );

        $store = Mage::app()->getStore();
        $this->addColumn(
            'base_shipping_amount',
             array(
                'header'=> Mage::helper('melhorenvio')->__('Valor do Frete'),
                'index' => 'base_shipping_amount',
                'width' => '50px',
                'type'  => 'price',
                'currency_code' => $store->getBaseCurrency()->getCode(),
            )
        );

        $this->addColumn(
            'chave_nfe',
             array(
              'renderer' => 'NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_Chavenfe',
              'width'    => '400px',
              'header' => Mage::helper('melhorenvio')->__('Chave NFe'),
              'index' => 'chave_nfe',
              'filter_condition_callback' => array($this, '_ChaveNFEFilter')
            )
        );

        $this->addColumn(
            'declarar_conteudo',
            array(
                'renderer' => 'NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_DeclararConteudo',
                'width' => '50px',
                'header' => Mage::helper('melhorenvio')->__('Declarar Conteúdo?'),
                'index' => 'declarar_conteudo',
                'filter_condition_callback' => array($this, '_DeclararConteudoFilter')
            )
        );

        $this->addColumn(
            'assegurar_valor',
            array(
                'renderer' => 'NovaPC_Melhorenvio_Block_Adminhtml_Orders_Renderer_AssegurarValor',
                'width' => '50px',
                'header' => Mage::helper('melhorenvio')->__('Assegurar Valor?'),
                'index' => 'declarar_conteudo',
                'filter_condition_callback' => array($this, '_AssegurarValorFilter')

            )
        );

        $this->addColumn(
            'gerar',
            array(
                'header'   => Mage::helper('melhorenvio')->__('Action'),
                'width'    => 150,
                'type'     => 'action',
                'getter'  => 'getIncrementId',
                'actions'  => array(
                    array(
                        'caption' => Mage::helper('melhorenvio')->__('Pagar etiqueta'),
                        'field'     => 'increment_id',
                        'onclick' => 'javascript: return confirm("Por favor confirme a geração!")',
                        'url'  => array('base'=> '*/*/gerar'),
                    ),
                ),
                'filter' => false,
                
            )
        );

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('sales_order_id');
        $this->getMassactionBlock()->setFormFieldName('melhorenvio_id');

        $this->getMassactionBlock()->addItem(
            'sync',
            array(
                'label'    => Mage::helper('melhorenvio')->__('Sincronizar'),
                'url'      => $this->getUrl('*/*/syncOrders'),
                'confirm'  => Mage::helper('melhorenvio')->__('Esta ação irá atualizar as etiquetas!')
            )
        );

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

}