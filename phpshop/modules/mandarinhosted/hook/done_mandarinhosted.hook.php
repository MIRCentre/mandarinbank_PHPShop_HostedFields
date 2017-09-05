<?php

function send_to_order_mod_mandarinhosted_hook($obj, $value, $rout){
  if ($rout == 'MIDDLE' and $value['order_metod'] == 10027){
    // Настройки модуля
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopMandarinHostedArray = new PHPShopMandarinHostedArray();
    $option = $PHPShopMandarinHostedArray->getArray();

    // Номер счета
    $mrh_ouid = explode("-", $value['ouid']);
    $orderId = $mrh_ouid[0] . $mrh_ouid[1];

    // Сумма покупки
    $price = number_format($obj->get('total'), 2, '.', '');
    $merchant_id = $option['merchant_key'];
    $secret_key = $option['merchant_skey'];
    $email = $value['mail_new'];

    function gen_auth($merchantId, $secret){
    	$reqid = time() ."_". microtime(true) ."_". rand();
    	$hash = hash("sha256", $merchantId ."-". $reqid ."-". $secret);
    	return $merchantId ."-".$hash ."-". $reqid;
    }
    function siteURL(){
    	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    	$domainName = $_SERVER['HTTP_HOST'].'/';
    	return $protocol.$domainName;
    }

    $xauth = gen_auth($merchant_id,$secret_key);

    $content = array(
      'payment'=>array(
        'orderId'=>$orderId,
        'action'=>'pay',
        'price'=>$price,
        'orderActualTill'=>date('Y-m-d H:i:s')
      ),
      'customerInfo'=>array(
        'email'=>$email
      ),
      'urls'=>array(
        'callback'=> siteURL() .'phpshop/modules/mandarinhosted/payment/result.php'
      )
    );
    $content = json_encode($content);

    $url = 'https://secure.mandarinpay.com/api/transactions';
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Auth: '.$xauth,'Content-type: application/json') );
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
    $json_response = curl_exec($curl);
    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    if ( $status !== 200 ) {
      die("Error: call to URL $url failed with status $status, response $json_response");
    }
    curl_close($curl);
    $json = json_decode($json_response);
    $operationId = $json->jsOperationId;

    $obj->set('operationId',$operationId);
    $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['mandarinhosted']['mandarin_payment_form'], true);

    $obj->set('orderMesage', $form);
  }
}

$addHandler = array('send_to_order' => 'send_to_order_mod_mandarinhosted_hook');
