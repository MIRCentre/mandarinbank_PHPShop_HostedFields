<?php
/**
* @author github.com/vuchastyi
* @version 0.0.1
* @package PHPShopPayment
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

// initialize variable
$merchant_id = $SysValue['mandarin-hosted']['MANDARIN_HOSTED_MID'];
$secret_key = $SysValue['mandarin-hosted']['MANDARIN_HOSTED_SECRET'];
$price = number_format($GLOBALS['SysValue']['other']['total'], 2, '.', '');
$email = $_POST['mail_new'];
$orderIdTmp = explode("-", $_POST['ouid']);
$orderId = $orderIdTmp[0]."".$orderIdTmp[1];

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
    'callback'=> siteURL() .'payment/mandarin-hosted/result.php'
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

$disp = '<style>.hosted-field{background:#f0f0f0;height:40px;padding:5px;border:1px solid gray;border-radius:10px;position:relative}.hosted-field .glyphicon{visibility:hidden;position:absolute;right:5px;top:5px;color:green;float:right}.mandarinpay-field-state-error{background:#fff0f0;border:1px solid #900000}.mandarinpay-field-state-focused{background:#fff;border:1px solid #9acd32}.mandarinpay-field-state-valid{background:#c0ffc0!important;border:1px solid green!important}.mandarinpay-field-state-valid .glyphicon{visibility:visible}</style>

<form id="form-hosted-pay">
	<div style="margin: 10px; padding: 10px; border: 1px solid gray">
		Card Number:
		<div class="mandarinpay-field-card-number hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		Card Holder:
		<div class="mandarinpay-field-card-holder hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		Card Expiration:
		<div class="mandarinpay-field-card-expiration hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		CVV:
		<div class="mandarinpay-field-card-cvv hosted-field"><div class="glyphicon glyphicon-check"></div></div>
		<br/>
		<a href="#" onclick="return mandarinpay.hosted.process(this);" class="btn btn-default">Оплатить</a>
	</div>
</form>

<script src="https://secure.mandarinpay.com/api/hosted.js"></script>
<script>
mandarinpay.hosted.setup("#form-hosted-pay",{
  operationId: "'.$operationId.'",
  onsuccess: function(data) {
    window.location.href = "/success/?payment=mandarin_hosted&orderId='.$orderId.'";
  },
  onerror: function(data) {
    window.location.href = "/fail/";
  }
});
</script>';
