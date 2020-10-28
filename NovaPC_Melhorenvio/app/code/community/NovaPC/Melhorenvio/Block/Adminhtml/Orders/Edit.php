<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Orders_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
       parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'gestaoboxapi';
        $this->_controller = 'adminhtml_report';
        $this->_removeButton('save');
        $this->_updateButton('delete', 'label', 'Excluir Item');
        $this->_removeButton('reset');
        $this->_headerText = $this->__('Informações da Integração');
    }

}