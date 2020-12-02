<?php
class NovaPC_Melhorenvio_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected $_token = NULL;
	protected $_url_init = NULL;

	public function __construct(){
		$this->_url_init = $this->getEnvironment();
		$this->_token = Mage::getStoreConfig('melhorenvio/general/token_melhorenvio');
	}

	public function getEnvironment(){
		return (Mage::getStoreConfig('melhorenvio/general/ambiente') == 1 ? "https://www.melhorenvio.com.br/" : "https://sandbox.melhorenvio.com.br/");
	}

	public function array_msort($array, $cols)
	{
	    $colarr = array();
	    foreach ($cols as $col => $order) {
	        $colarr[$col] = array();
	        foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
	    }
	    $eval = 'array_multisort(';
	    foreach ($cols as $col => $order) {
	        $eval .= '$colarr[\''.$col.'\'],'.$order.',';
	    }
	    $eval = substr($eval,0,-1).');';
	    eval($eval);
	    $ret = array();
	    foreach ($colarr as $col => $arr) {
	        foreach ($arr as $k => $v) {
	            $k = substr($k,1);
	            if (!isset($ret[$k])) $ret[$k] = $array[$k];
	            $ret[$k][$col] = $array[$k][$col];
	        }
	    }
	    return $ret;
	}

	public function getDeliveryTime($frete)
	{
		if(
			Mage::getStoreConfig('carriers/melhorenvio/adicional_prazo') != null &&
			(int) Mage::getStoreConfig('carriers/melhorenvio/adicional_prazo') > 0
		) {
			return $frete->delivery_time + (int) Mage::getStoreConfig('carriers/melhorenvio/adicional_prazo');
		}
		
		return $frete->delivery_time;
	}

	public function getInitialByName($value)
	{
		$estadosBrasileiros = array(
			'AC'=>'Acre',
			'AL'=>'Alagoas',
			'AP'=>'Amapá',
			'AM'=>'Amazonas',
			'BA'=>'Bahia',
			'CE'=>'Ceará',
			'DF'=>'Distrito Federal',
			'ES'=>'Espírito Santo',
			'GO'=>'Goiás',
			'MA'=>'Maranhão',
			'MT'=>'Mato Grosso',
			'MS'=>'Mato Grosso do Sul',
			'MG'=>'Minas Gerais',
			'PA'=>'Pará',
			'PB'=>'Paraíba',
			'PR'=>'Paraná',
			'PE'=>'Pernambuco',
			'PI'=>'Piauí',
			'RJ'=>'Rio de Janeiro',
			'RN'=>'Rio Grande do Norte',
			'RS'=>'Rio Grande do Sul',
			'RO'=>'Rondônia',
			'RR'=>'Roraima',
			'SC'=>'Santa Catarina',
			'SP'=>'São Paulo',
			'SE'=>'Sergipe',
			'TO'=>'Tocantins'
		);

		return array_search($value, $estadosBrasileiros);
	}

	public function validateRegion($regionOrigin)
	{
		if(strlen($regionOrigin) > 2) {
			$region = $this->getInitialByName($regionOrigin);
		}

		if(gettype($regionOrigin) == 'integer') {
			$region = Mage::getModel('directory/region')->load($regionOrigin)->getName();
		}

		return $region;
	}

	function formatCnpjCpf($value)
	{
		$cnpj_cpf = preg_replace("/\D/", '', $value);
		
		if (strlen($cnpj_cpf) === 11) {
			return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
		} 
		
		return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
	}

	public function isCNPJ($valor){
        $valor = preg_replace('/\D/', '', $valor);
        return (strlen($valor) == 14);
    }

	public function getServicos(){
	    $url = $this->_url_init."api/v2/me/shipment/services";
	    return $this->webServiceRequest($url);
	}

	public function filterCarriers($servicos_id, $filter_private){
		$servicos = $this->getServicos();
		$servicos_id = explode(",", $servicos_id);

		$servicos_return = array();

		foreach($servicos as $servico){
			if(in_array($servico->id, $servicos_id)){
				if($filter_private){
					if(strtolower($servico->company->name) != "correios"){
						array_push($servicos_return, $servico->id);
					}
				}else{
					if(strtolower($servico->company->name) == "correios"){
						array_push($servicos_return, $servico->id);
					}
				}
			}
		}

		return implode(",", $servicos_return);
	}

	public function getServiceById($id)
	{
		$url = $this->_url_init."api/v2/me/shipment/services/{$id}";
		$retorno = $this->webServiceRequest($url);
		return array("name" => $retorno->company->name, "company_id" => $retorno->company->id);
	}

	public function getAgencies($id)
	{
		$url = $this->_url_init."api/v2/me/shipment/agencies?company={$id}&pretty";

		return $this->webServiceRequest($url);
	}

	public function getUserDataFromMelhorEnvio()
	{
		$url = $this->_url_init."api/v2/me";
		return $this->webServiceRequest($url);
	}

	public function calcularFrete($servicos, $assegurar_valor, $toCep = null, $fromCep = null, $quote = null){
		foreach ($quote->getAllItems() as $key => $item) {
            $produto = Mage::getModel('catalog/product')->load($item->getProduct()->getId());
			
            if ($item instanceof Mage_Sales_Model_Quote_Item)
            {
            	$qty = $item->getQty ();
            }
            elseif ($item instanceof Mage_Sales_Model_Order_Item)
            {
            	$qty = $item->getShipped () ? $item->getShipped () : $item->getQtyInvoiced ();
            	if ($qty == 0) {
            	    $qty = $item->getQtyOrdered();
            	}
            }

            $products[$key] = array(
                "id" => $item->getProduct()->getId(),
                "weight" => $produto->getData('weight'),
                "height" => $produto->getData('altura'),
                "length" => $produto->getData('largura'),
                "width" => $produto->getData('comprimento'),
                "quantity" => $qty,
                "insurance_value" => ($assegurar_valor ? $item->getPrice() : 0),
            );
		}

        $params = array(
            "from" => array(
                "postal_code" => $fromCep,
                "address" => "",
                "number" => "",
            ),
            "to" => array(
                "postal_code" => $toCep,
                "address" => "",
                "number" => "",
            ),
            "products" => $products,
            "options" => array(
                "receipt" => (Mage::getStoreConfig('carriers/melhorenvio/aviso_de_recebimento') == 0) ? false : true,
                "own_hand" => (Mage::getStoreConfig('carriers/melhorenvio/mao_propria') == 1) ? true : false,
                "collect" => false,
            ),
            "services" => $servicos,
		);

        $url = $this->_url_init."api/v2/me/shipment/calculate";
		$ret = $this->webServiceRequest($url, $params, "POST");
		
        return $ret;
	}

	public function getCalculoFrete($toCep = null, $fromCep = null, $quote = null){
		$servicos =  Mage::getStoreConfig('carriers/melhorenvio/servicos');

		if(Mage::getStoreConfig('carriers/melhorenvio/declaracao') == 0){
			$servicos_privados = $this->filterCarriers($servicos, true);
			$servicos_nao_privados = $this->filterCarriers($servicos, false);
	
			$calculo1 = ($servicos_privados == "" ? array() : $this->calcularFrete($servicos_privados, true, $toCep, $fromCep, $quote));
			$calculo2 = ($servicos_nao_privados == "" ? array() : $this->calcularFrete($servicos_nao_privados, false, $toCep, $fromCep, $quote));
			
			if(!is_array($calculo1)) $calculo1 = array($calculo1);
			if(!is_array($calculo2)) $calculo2 = array($calculo2);

			$calculo = array_merge($calculo1, $calculo2);
		}else{
			$calculo = ($servicos == "" ? array() : $this->calcularFrete($servicos, true, $toCep, $fromCep, $quote));
			if(!is_array($calculo)) $calculo = array($calculo);
		}

		return $calculo;
    }


    public function limparCarrinho(){
        $url = $this->_url_init."api/v2/me/cart"; 
        $ret = $this->webServiceRequest($url);
        foreach($ret->data as $item){
        	$url2 = $this->_url_init."api/v2/me/cart/";
        	$url2.=$item->id;

        	$ret2 = $this->webServiceRequest($url2, null, "DELETE");
        }

        return true;
    }

	public function webServiceRequest($url = null, $params = null, $method = "GET"){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => $method,
          CURLOPT_POSTFIELDS => json_encode($params),
          CURLOPT_HTTPHEADER => array(
            "User-Agent: Magento 1/" . Mage::getVersion(),
            "Accept: application/json",
            'content-type: application/json',
            "Authorization: Bearer ".$this->_token,
            "Connection: keep-alive",
            "accept-encoding: gzip, deflate",
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          return array("error" => $err);
        }else {
          return json_decode($response);
        }
    }

	public function validateAddress($cep, $address, $extras)
	{
		if(Mage::getStoreConfig('carriers/melhorenvio/validate_address')) {
			$curl = curl_init();

			curl_setopt_array($curl, array(
			CURLOPT_URL => "http://viacep.com.br/ws/".preg_replace('/[.-]/', '', $cep)."/json/unicode/",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "GET",
			));
			
			$response = curl_exec($curl);

			$responseStatusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			curl_close($curl);
			$response = json_decode($response);

			if($response->erro != true && $responseStatusCode == 200){
				if(!empty($response->logradouro) && !empty($response->bairro) && !empty($response->localidade) && !empty($response->uf)){
					return [$response->logradouro, $response->bairro, $response->localidade, $response->uf];
				}
			}
		}
		
		return [$address[0], $address[3], $extras['city'], $this->validateRegion($extras['region'])];
	}

	public function isEtiquetaValidFromOrder($orderId)
	{
		$order = Mage::getModel('sales/order')->load($orderId);
		$orderIncrementId = $order->getIncrementId();

		$pedido = Mage::getModel('melhorenvio/orders')->load($orderIncrementId, 'increment_id');


		$tz = 'America/Sao_Paulo';
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz));
		$dt->setTimestamp($timestamp);

		if(!is_null($pedido->getData("status")) && $pedido->getData("status") != "Pendente") {

			$melhorenvio_id = explode(',', $pedido->getData('melhorenvio_id'));

			foreach($melhorenvio_id as $me_id) {
				try {
					$canBeCanceled = $this->webServiceRequest(
						$this->_url_init."api/v2/me/shipment/cancellable", 
						array("orders" => array($me_id)), 
						"POST"
					);

					if($canBeCanceled->{$me_id}->cancellable) {
						$cancelandoEtiqueta = $this->webServiceRequest(
							$this->_url_init."api/v2/me/shipment/cancel",
							array(array("id" => $me_id, "reason_id" => "2", "description" => "Pedido cancelado no magento " . $dt->format('d-m-Y_H-i-s'))),
							"POST"
						);
					}
				} catch(Exception $e) {
					Mage::getSingleton('admin/session')->addError('Erro ao cancelar pedido('.$orderIncrementId.'). Erro: '.$e->getMessage());
					Mage::log('Erro ao cancelar pedido('.$orderIncrementId.'). Erro: '.$e->getMessage(), null, 'melhorenvioErrorToCancelLabel.log');
				}
			}

			$pedido->setData("status", "Cancelado")->save();

			Mage::log($canBeCanceled . " - " . $dt->format('d-m-Y_H-i-s'), null, 'cancelamentoEtiqueta.log');
		} else {
			$pedido->setData("status", "Cancelado")->save();
		}
	}

	public function syncOrders($order = null){
		$collection = Mage::getModel('melhorenvio/orders')->getCollection();

		if($order == null){
			$collection = $collection->getOrdersByStatus("Erro");
		}else{
			$collection->addFieldToFilter("increment_id", $order->getData('increment_id'))
			->addFieldToFilter("status", "Erro")->getFirstItem();
		}

		if($order != null && $collection->count() == 0){
			return $order->getData("me_status");
		}

		
		foreach ($collection as $order){
			$url = $this->_url_init."api/v2/me/shipment/preview";
			
			$params = array(
				"orders" => explode(",",$order->getData('melhorenvio_id')),
			);

			$ret = $this->webServiceRequest($url, $params, "POST");
			if(isset($ret->url) && $ret->url != null){
				$order->setData('status', 'Gerado');
				$order->setData('url_etiqueta', $ret->url);
				$order->save();
			}

			if($collection->count() == 1){
				return $order->getData("status");
			}
		}
	}

	public function isPrivateCarrier($transportadora){
		$transportadora = explode(" - ", $transportadora);
        $transportadora = $transportadora[(count($transportadora) - 1)];
        $transportadora = explode(" ", $transportadora)[0];

        // $transportadora = explode(' ', $row->getShippingDescription());
        if(strtolower($transportadora) == 'correios') {
            return false;
        }

        return true;
	}

	public function WithdrawDeliveryTime($text){
        $metodoDeEnvio = explode("- ", $text);
        $transportadora = end($metodoDeEnvio);
		$transportadora = explode("(", $transportadora);

        if(count($transportadora) >= 2){
            $transportadora = $transportadora[count($transportadora) - 2];
            $metodoDeEnvio[count($metodoDeEnvio) - 1] = $transportadora;
        }

        return implode("- ", $metodoDeEnvio);
    }
}