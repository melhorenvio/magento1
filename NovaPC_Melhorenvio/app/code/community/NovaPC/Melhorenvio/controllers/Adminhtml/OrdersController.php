<?php
/**
 * Authors: [
 *  {
 *  name: 'Paulo Araujo' ,
 * email: 'paulofelipe_jau7654@hotmail.com'
 * },
 * 
 * {
 * name: Felipe Ap,
 * email: felipe.ap@gmail.com
 * }
 * ]
 */
class NovaPC_Melhorenvio_Adminhtml_OrdersController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction(){
        $this->loadLayout();
        $this->_setActiveMenu('melhorenvio');
        $this->renderLayout();
    
    }

    public function setNmrNfAction(){
        $params = $this->getRequest()->getParams();
        $data = Mage::getModel('melhorenvio/orders')->load($params['pro_id'], 'increment_id');
        try{
            $data->setData('nmr_nf', $params['text']);
            $data->setData('increment_id', $params['pro_id']);
            $data->save();
            $response=array('error'=>false);
        } catch (Exception $e) {
            $response=array('items'=>$e->getMessage(),'error'=>true);
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function savedeclararconteudoAction()
    {
        $params = $this->getRequest()->getParams();

        $order = Mage::getModel('melhorenvio/orders')->load($params['increment_id'], 'increment_id');
        
        if(intval($params['model']) == 1 && (!empty($order->getData("chave_nfe")) || !is_null($order->getData("chave_nfe")))){
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError("O pedido não pode conter declaração de conteúdo pois há uma Chave NFe inserida.");
            return $this->_redirect('*/*/');
        }
        
        if(is_null($order->getIncrementId()) && $order->getDeclararConteudo() != intval($params['model'])) {
            $order->setIncrementId($params['increment_id']);
            $order->setDeclararConteudo(intval($params['model']));
            $order->save();
        }

        if(!is_null($order->getIncrementId()) && $order->getDeclararConteudo() != intval($params['model'])) {
            $order->setDeclararConteudo(intval($params['model']));
            $order->save();
        }
        
        return $this->_redirect('*/*/');
    }

    public function saveassegurarvalorAction()
    {
        $params = $this->getRequest()->getParams();
        
        $order = Mage::getModel('melhorenvio/orders')->load($params['increment_id'], 'increment_id');
        if(is_null($order->getIncrementId()) && $order->getAssegurarValor() != intval($params['model'])) {
            $order->setIncrementId($params['increment_id']);
            $order->setAssegurarValor(intval($params['model']));
            $order->save();
        }

        if(!is_null($order->getIncrementId()) && $order->getAssegurarValor() != intval($params['model'])) {
            $order->setAssegurarValor(intval($params['model']));
            $order->save();
        }
        
        return $this->_redirect('*/*/');
    }


    public function setChaveNfeAction(){
        $params = $this->getRequest()->getParams();
        $data = Mage::getModel('melhorenvio/orders')->load($params['pro_id'], 'increment_id');

        if($data->getData("status") == "Gerado"){
            $response = array('items'=> 'A Chave NFe não pode ser inserida pois a etiqueta desse pedido já foi gerada','error'=>true);
        }else if($data->getData('declarar_conteudo') == 1 && !empty($params['text'])){
            $response = array('items'=> 'A Chave NFe não pode ser inserida pois o pedido está declarando o conteúdo','error'=>true);
        }else{
            try{
                $data->setData('chave_nfe', $params['text']);
                $data->setData('increment_id', $params['pro_id']);
    
                if(!empty($params['text']))
                    $data->setData('declarar_conteudo', false);
                    
                $data->save();
                $response=array('error'=>false);
            } catch (Exception $e) {
                $response=array('items'=>$e->getMessage(),'error'=>true);
            }
        }

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
    }

    public function gerarAction(){

        $params = $this->getRequest()->getParams();
        $order = Mage::getModel('melhorenvio/orders')->getCollection()->getCustomOrdersByIncrementId($params['increment_id']);

        $prices = array();
        $products = array();
    
        foreach($order as $o){
            $stts = Mage::helper('melhorenvio')->syncOrders($o);

            $chave_nfe = $o->getData('chave_nfe');
            $non_commercial = (empty($chave_nfe) || is_null($chave_nfe));

            $cnpj = Mage::helper('melhorenvio')->formatCnpjCpf(Mage::getStoreConfig("melhorenvio/empresa/cnpj"));
            $cpf = Mage::helper('melhorenvio')->formatCnpjCpf(Mage::getStoreConfig("melhorenvio/empresa/cpf"));

            $servico = $this->getServicoId($o->getData('shipping_method'));

            if((empty($cnpj) || is_null($cnpj)) && (empty($cpf) || is_null($cpf))) {
                $session = Mage::getSingleton('adminhtml/session');
                $session->addError("Não foi possível gerar a etiqueta pois o CNPJ e CPF não estão configurados no módulo, informe pelo menos um dos campos e tente novamente.");
            
                return $this->_redirect('*/*/index');
            }

            if($stts == "Gerado"){
                $msg = "Etiqueta já gerada.";
            }else if($stts == "Cancelado" && $o->getData('status') == "canceled") {
                $msg = "Etiqueta e pedido cancelado.";
            }

            if(empty($chave_nfe) && $o->getData('declarar_conteudo') == 0 && !isset($msg)){
                $msg = "É obrigatório declarar o conteúdo ou inserir a Nota Fiscal para gerar está etiqueta.";
            }

            if(isset($msg)) {
                $session = Mage::getSingleton('adminhtml/session');
                $session->addError($msg);
            
                return $this->_redirect('*/*/index');
            }

            $customerAddress = $o->getShippingAddress()->toArray();
            $fromCep = Mage::getStoreConfig("shipping/origin/postcode");

            $fretes = Mage::helper('melhorenvio')->getCalculoFrete($customerAddress['postcode'], $fromCep, $o);

            foreach ($o->getAllItems() as $item) {
                $allProducts[$item->getProductId()] = array(
                    "name" => $item->getName(),
                    "weight" => $item->getData('weight'),
                    "unitary_value" => (float)$item->getPrice()
                );
            }

            foreach ($fretes as $frete){
                if($frete->id == $servico['id']){
                    foreach ($frete->packages as $i=> $pkg){
                        $packages[$i] = array(
                            "weight" => $pkg->weight,
                            "width" => $pkg->dimensions->width,
                            "height" => $pkg->dimensions->height,
                            "length" => $pkg->dimensions->length,
                        );

                        $products[$i] = array();
                        $prices[$i] = 0;

                        foreach ($pkg->products as $product){
                            $product_info = $allProducts[$product->id];
                            $product_info["quantity"] = $product->quantity;

                            array_push($products[$i], $product_info);

                            $prices[$i] += $product_info["unitary_value"] * (int)$product->quantity;
                        }
                        
                    }
                    break;
                }
            }

            list($street, $district, $city, $state) = Mage::helper('melhorenvio')->validateAddress($customerAddress['postcode'], $o->getShippingAddress()->getStreet(), $customerAddress);
            
            $params = array(
                "service" => $servico['id'],
                "agency" => (strtolower($servico["company_label"]) == "jadlog") ? Mage::getStoreConfig('carriers/melhorenvio/agencia') : null,
                "from" => array(
                    "name" => Mage::getStoreConfig("melhorenvio/empresa/razao_social"),
                    "phone" => Mage::getStoreConfig("melhorenvio/empresa/telefone"),
                    "email" => Mage::getStoreConfig("melhorenvio/empresa/email"),
                    "address" => Mage::getStoreConfig("melhorenvio/address/rua"),
                    "complement" => Mage::getStoreConfig("melhorenvio/address/complem"),
                    "number" => Mage::getStoreConfig("melhorenvio/address/numero"),
                    "district" => Mage::getStoreConfig("melhorenvio/address/bairro"),
                    "city" => Mage::getStoreConfig("melhorenvio/address/cidade"),
                    "state_abbr" => Mage::getStoreConfig("melhorenvio/address/estado"),
                    "country_id" => "BR",
                    "postal_code" => Mage::getStoreConfig("melhorenvio/address/cep"),
                    "note" => ""
                ),
                "to" => array(
                    "name" => $o->getData('customer_firstname')." ".$o->getData('customer_lastname'),
                    "phone" => $o->getShippingAddress()->getTelephone(),
                    "email" => $o->getData('customer_email'),
                    "address" => $street,
                    "complement" => $o->getShippingAddress()->getStreet()[2],
                    "number" => $o->getShippingAddress()->getStreet()[1],
                    "district" => $district,
                    "city" => $city,
                    "state_abbr" => $state,
                    "country_id" => "BR",
                    "postal_code" => str_replace('-', '', $customerAddress['postcode']),
                    "note" => ""
                ),
                "products" => array(),
                "package" => array(),
                "options" => array(
                    "platform" => "NovaPC - Magento",
                    "tags" => array(
                        "tag" => $o->getIncrementId(),
                        "url" => Mage::getBaseUrl()."sales/order/view/order_id/{$o->getId()}/",
                    ),
                    "insurance_value" => '',
                    "receipt" => (Mage::getStoreConfig('carriers/melhorenvio/aviso_de_recebimento') == 0) ? false : true,
                    "own_hand" => (Mage::getStoreConfig('carriers/melhorenvio/mao_propria') == 0) ? false : true,
                    "collect" => false,
                    "reverse" => false,
                    "non_commercial" => $non_commercial,
                    "invoice" => (!$non_commercial ? array( 
                        "number" => substr($o->getData('chave_nfe'), 25, 9),
                        "key" => $o->getData('chave_nfe')
                    ) : ''),
                ),
                "coupon" => "",
            );

            $inscricao_estadual = Mage::getStoreConfig("melhorenvio/empresa/inscricao_estadual");
            
            if(!empty($inscricao_estadual) && !is_null($inscricao_estadual))
                $params["from"]["state_register"] = $inscricao_estadual;

            if(!empty($cnpj) && !is_null($cnpj)){
                $params["from"]["company_document"] = $cnpj;
            }else{
                $params["from"]["document"] = $cpf;
            }

            $customer_document = Mage::helper('melhorenvio')->formatCnpjCpf($o->getData('customer_taxvat'));

            if(Mage::helper('melhorenvio')->isCNPJ($customer_document)){
                $params["to"]["company_document"] = $customer_document;
            }else{
                $params["to"]["document"] = $customer_document;
            }
            
            $url = Mage::helper('melhorenvio')->getEnvironment() . "api/v2/me/cart";

            foreach($packages as $i => $pkg){
                if($o->getAssegurarValor() == 1){
                    $params['options']['insurance_value'] = $prices[$i];
                }

                if($o->getDeclararConteudo() == 1){
                    $params["products"] = $products[$i];
                }

                $params['package'] = $pkg;

                $ret = Mage::helper('melhorenvio')->webServiceRequest($url, $params, "POST");

                if(isset($ret->errors)){
                    if(isset($ret->errors->{'to.document'})) {
                        $session = Mage::getSingleton('adminhtml/session');
                        $session->addError("O CPF/CNPJ do destinatário é inválido.");
                        
                        return $this->_redirect('*/*/index');    
                    }
                    $session = Mage::getSingleton('adminhtml/session');
                    $session->addError($ret->message);
                    
                    return $this->_redirect('*/*/index');
                }

                if(isset($ret->error)){
                    $session = Mage::getSingleton('adminhtml/session');
                    $session->addError($ret->error);
                    
                    return $this->_redirect('*/*/index');
                }

                if(!isset($ret->id)){
                    $session = Mage::getSingleton('adminhtml/session');
                    $session->addError("Ocorreu um erro inesperado.");
                    
                    return $this->_redirect('*/*/index');
                }


                $ids['order_id'][] = $ret->id;
                $ids['protocol_id'][] = $ret->protocol;
 
            }

           $this->buyOrder($ids, $o->getIncrementId());  
        }
    }


    public function buyOrder($ids, $incrementId){

        $url = Mage::helper('melhorenvio')->getEnvironment() . "api/v2/me/shipment/checkout";
        $url2 = Mage::helper('melhorenvio')->getEnvironment() . "api/v2/me/shipment/preview";
        $params = array(
            "orders" => $ids['order_id'],
        );
        $data = Mage::getModel('melhorenvio/orders')->load($incrementId, 'increment_id');

        $ret = Mage::helper('melhorenvio')->webServiceRequest($url, $params);

        $ret2 = Mage::helper('melhorenvio')->webServiceRequest($url2, $params, "POST");   

        $order_id = "";
        $me_id = "";

        foreach($ids['protocol_id'] as $i => $id){
            if($i == count($ids['protocol_id'])-1){
                $order_id.=$id;
                $me_id.= $ids["order_id"][$i];
                break;
            }
            $order_id.=$id."</br>";  
            $me_id.=$ids["order_id"][$i].",";  
        }

        try{
            $data->setData('increment_id', $incrementId);
            $data->setData('order_id', $order_id);
            $data->setData('url_etiqueta', $ret2->url);
            $data->setData('status', 'Gerado');
            $data->setData('melhorenvio_id', $me_id);
            

        } catch (Exception $e) {
            $response=array('items'=>$e->getMessage(),'error'=>true);
        }
 
        if(isset($ret->error) || isset($ret->errors)){
            $url = Mage::helper('melhorenvio')->getEnvironment() . "carrinho/";
            $data->setData('status', 'Erro');
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError($ret->error.".<br>Sua etiqueta está no carrinho do Melhor Envio, <a href='{$url}' target='_blank'>clique aqui</a> para realizar o pagamento e poder imprimir sua etiqueta.");
        }

        $data->save();
        $this->_redirect('*/*/index');
    }


    public function getServicoId($nome){
        $nome = str_replace("melhorenvio_", "", $nome);
        $servicos = Mage::helper('melhorenvio')->getServicos();  
        foreach ($servicos as $servico){
            if($servico->name == $nome){
                return array("id" => $servico->id, "company_label" => $servico->company->name);
            }
        }
    }

    
    public function syncOrdersAction(){
        $itensIds = (array) $this->getRequest()->getParam('melhorenvio_id');

        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->addFieldToFilter("entity_id", array('in' => $itensIds));

        $itensIds = array();

        foreach ($collection as $order){
            array_push($itensIds, $order->getData("increment_id"));
        }

        $collection = Mage::getModel('melhorenvio/orders')->getCollection();
        $collection->addFieldToFilter("increment_id", array('in' => $itensIds));

        $session = Mage::getSingleton('adminhtml/session');
       
        if($collection->count() > 0){
            foreach ($collection as $order){
                if($order->getStatus() == 'Cancelado' || is_null($order->getMelhorenvioId())) {
                    continue;
                }
                
                $ids = explode(',', $order->getMelhorenvioId());

                foreach($ids as $id) {
                    $url = Mage::helper('melhorenvio')->getEnvironment() . "api/v2/me/orders/search?q=".$id;

                    $ret = Mage::helper('melhorenvio')->webServiceRequest($url);
                    
                    if(isset($ret[0]->error)) {
                        $session->addWarning($ret[0]->error);
                        continue;
                    }

                    if(isset($ret[0]->status) && $ret[0]->status == 'canceled') {
                        $order->setStatus("Cancelado");
                    }

                    if(isset($ret[0]->url) && $ret[0]->url != null){
                        $order->setData('status', 'Gerado');
                        $order->setData('url_etiqueta', $ret[0]->url);
                    }
                    
                }

                $order->save();

            }
        }

        $session->addSuccess("Pedidos Sincronizados com Sucesso!");
        $this->_redirect('*/*/');
    }   

    public function gridAction(){
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('melhorenvio/adminhtml_orders_grid')->toHtml()
        );
    }

    protected function _isAllowed(){
        return Mage::getSingleton('admin/session')->isAllowed('melhorenvio/orders');
    }
}