<?php

// Настройки модуля
PHPShopObj::loadClass("array");

class PHPShopMandarinHostedArray extends PHPShopArray {
  function __construct() {
    $this->objType=3;
    $this->objBase=$GLOBALS['SysValue']['base']['mandarinhosted']['mandarinhosted_system'];
    parent::__construct('merchant_key','merchant_skey');
  }
}
