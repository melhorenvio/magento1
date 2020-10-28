<?php

class NovaPC_Melhorenvio_Adminhtml_RastreioController extends Mage_Adminhtml_Controller_Action
{


    public function indexAction() 
    {
        $this->loadLayout();
        $this->_setActiveMenu('melhorenvio');
        $this->renderLayout();
    
    }

    /**
     * Product grid for AJAX request 
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('melhorenvio/adminhtml_rastreio_grid')->toHtml()
        );
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('melhorenvio/orders')->load($id, 'increment_id');
        Mage::register('rastreio_data', $model);
        $this->loadLayout();
        $this->_addContent(
            $this->getLayout()
            //->createBlock('melhorenvio/adminhtml_rastreio_custom')
            ->createBlock('Mage_Core_Block_Template', 'template-rastreio', array("default/template" => "melhorenvio/rastreio/custom.phtml"))
        );
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('melhorenvio/rastreio');
    }
}