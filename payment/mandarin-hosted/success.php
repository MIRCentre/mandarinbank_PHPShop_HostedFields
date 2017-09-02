<?php
/**
* @author github.com/vuchastyi
* @version 1.0
* @package PHPShopPayment
*/

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));
if($_REQUEST['ik_inv_st'] == 'success'){
	$order_metod="Interkassa";
	$success_function=false;
	$my_crc = "NoN";
	$crc = "NoN";
	$inv_id = $_REQUEST['ik_pm_no'];
}
