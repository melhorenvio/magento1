<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Report_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('reports_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle('Seções');
    }

    protected function _beforeToHtml()
    {
        $this->addTab(
            'product_section',
            array(
                'label' => 'Produto',
                'title' => 'Produto',
                'content' => $this->getLayout()
                    ->createBlock('gestaoboxapi/adminhtml_report_edit_tab_product')
                    ->toHtml()
            )
        );

        return parent::_beforeToHtml();
    }

}