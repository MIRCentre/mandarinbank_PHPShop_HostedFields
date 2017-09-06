<?php

function userorderpaymentlink_mod_mandarin_hook($obj, $PHPShopOrderFunction) {
  include_once(dirname(__FILE__) . '/mod_option.hook.php');
  $PHPShopMandarinHostedArray = new PHPShopMandarinHostedArray();
  $option = $PHPShopMandarinHostedArray->getArray();

  if(!$PHPShopOrderFunction->getParam('statusi') && $PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10027){
    $return = ', Заказ не оплачен';
  }elseif($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10027){
    $return = ', Заказ обрабатывается менеджером';
  }

  return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_mandarin_hook');
