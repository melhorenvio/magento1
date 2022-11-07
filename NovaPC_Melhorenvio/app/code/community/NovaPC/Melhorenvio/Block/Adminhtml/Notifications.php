<?php

class NovaPC_Melhorenvio_Block_Adminhtml_Notifications extends Mage_Adminhtml_Block_Template{

    public function _toHtml($className = "notification-global")
    {  
        if( Mage::helper("melhorenvio")->isEnabledMelhorEnvio() ){
            
            $date_expire = Mage::helper("melhorenvio")->getDate();
            
            //data que simula um token valido por 8 dias + 1h
            //$date_expire = strtotime("+8 Days") + 60 * 60;

            $today = time();
    
            if( isset($date_expire) ){
    
                $date_validate = strtotime("-10 Days", $date_expire);
    
                if($today > $date_expire){
                    $ms = [
                        "message" => "Seu token de integração com o Melhor Envio está expirado. 
                        <a href='https://ajuda.melhorenvio.com.br/pt-BR/articles/4586948-integracao-magento-1-insercao-do-token' target='_blank'>
                        Clique aqui para ver como atualizá-lo.
                        </a>",
                        "type" => "addError"
                    ];
                }else if($today >= $date_validate){
    
                    //Create a date object out of today's date:
                    $date1 = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $today));
                    $date2 = date_create_from_format('Y-m-d H:i:s', date('Y-m-d H:i:s', $date_expire));
    
                    //Create a comparison of the two dates and store it in an array:
                    $diff = (array) date_diff($date1, $date2);
                    $ms = [
                        "message" => "Seu token do Melhor Envio expira em " . $diff['days'] . " dia(s) - ". $diff['h'] . "h " . $diff['i'] . "m " . $diff['s'] ."s",
                        "type" => "addNotice"
                    ];
                }
            }
            
            if(isset($ms)){
                Mage::getSingleton('core/session')->{$ms['type']}($ms['message']);
            }
        }
        return parent::_toHtml();
    }
}