<?php

class NovaPC_Melhorenvio_Model_Orders extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('melhorenvio/orders');
    }
}