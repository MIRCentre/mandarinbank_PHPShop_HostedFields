<?php
/**
* @author github.com/vuchastyi
* @version 0.0.1
* @package PHPShopPayment
*/

if(empty($GLOBALS['SysValue']))exit(header("Location: /"));

if(!empty($_REQUEST['payment']) && $_REQUEST['payment'] === 'mandarin_hosted') {
	$order_metod = "mandarin-hosted";
	$success_function = false;
	$my_crc = "NoN";
	$crc = "NoN";
	$inv_id = $_GET['orderId'];
}
