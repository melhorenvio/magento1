<?php  
    class NovaPC_Melhorenvio_Model_Carrier_Melhorenvio     
        extends Mage_Shipping_Model_Carrier_Abstract
        implements Mage_Shipping_Model_Carrier_Interface
    {  
        protected $_code = 'melhorenvio';

        protected $_from = null;
        protected $_address = null;
      
        public function collectRates(Mage_Shipping_Model_Rate_Request $request){
            $this->_init($request);

            $cep = $request->getDestPostcode();
           
            $quote = $request->getAllItems();

            $fretes = Mage::helper('melhorenvio')->getCalculoFrete($cep, $this->_from, $request);

            $result = Mage::getModel('shipping/rate_result');

            foreach($fretes as $frete){
                if(!isset($frete->error)){
                    $lucro = str_replace(',','.', Mage::getStoreConfig('carriers/melhorenvio/lucro'));
                    $lucro = str_replace('%', '', $lucro);
                    $price = $frete->price + ($frete->price*($lucro/100));
					
					if($price == 0 || $price == '0' || $price == 0.00) {
						continue;
					}
					
                    $title = $frete->company->name." ".$frete->name;
                    if(Mage::getStoreConfig('carriers/melhorenvio/exibir_prazo') == 1){
                        $title.= " (" .Mage::helper('melhorenvio')->getDeliveryTime($frete). " dias úteis) - ";
                    }
                    $method = Mage::getModel('shipping/rate_result_method');
                    $method->setCarrier($this->_code);  
                    $method->setCarrierTitle($this->getConfigData('title'));
                    $method->setMethod($frete->name);  
                    $method->setMethodTitle($title);
                    $method->setPrice($price);
                    $method->setCost($price);

                    $result->append($method);
                }
            }
            return $result;
        }

        protected function _init(Mage_Shipping_Model_Rate_Request $request){
            if (!$this->getConfigData('active')) {
                return false;
            }

            $store = Mage::app()->getStore();
            $storeId = $store->getStoreId();

            $this->_from = Mage::getStoreConfig(Mage_Shipping_Model_Config::XML_PATH_ORIGIN_POSTCODE, $storeId);
            $this->_address = Mage::getStoreConfig("shipping/origin/street_line1", $storeId);
            
        }

        public function getAllowedMethods(){
            return array(
                $this->_code => $this->getConfigData('name')
            );
        }
    }  
