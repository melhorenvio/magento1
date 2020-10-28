<?php

class Gestaobox_Api_Block_Adminhtml_Report_Edit_Tab_Product extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset(
            'report_form',
            array(
                'legend' => Mage::helper('gestaoboxapi')->__('Produto')
            )
        );

        $fieldset->addField(
            'product_body',
            'textarea',
            array(
                'name' => 'product_body',
                'label' => Mage::helper('gestaoboxapi')->__('Requisição'),
            )
        );


        $fieldset->addField(
            'product_error',
            'textarea',
            array(
                'name'    => 'product_error',
                'label'   => Mage::helper('gestaoboxapi')->__('Erro'),
            )
        );


        $form->addValues(Mage::registry('report_data')->getData());

        return parent::_prepareForm();
    }

}