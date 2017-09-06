<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.mandarinhosted.mandarinhosted_system"));

// Обновление версии модуля
function actionBaseUpdate() {
  global $PHPShopModules, $PHPShopOrm;
  $PHPShopOrm->clean();
  $option = $PHPShopOrm->select();
  $new_version = $PHPShopModules->getUpdate($option['version']);
  $PHPShopOrm->clean();
  $action = $PHPShopOrm->update(array('version_new' => $new_version));
  return $action;
}

// Функция обновления
function actionUpdate() {
  global $PHPShopOrm;

  $PHPShopOrm->debug = false;
  $action = $PHPShopOrm->update($_POST);
  header('Location: ?path=modules&id=mandarinhosted');
  return $action;
}

function actionStart() {
  global $PHPShopGUI, $PHPShopOrm;

  // Выборка
  $data = $PHPShopOrm->select();

  $Tab1.=$PHPShopGUI->setField('ID Мерчанта', $PHPShopGUI->setInputText(false, 'merchant_key_new', $data['merchant_key'], 250));
  $Tab1.=$PHPShopGUI->setField('Секретный ключ', $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 250));

  $info = '<h4>MerchantId и Secret для интеграции</h4>
<p>MerchantID - это числовой идентификатор вашего проекта (магазина) в системе Mandarinpay, используемый для идентификации.<br>
Значения MerchantID и secret можно найти в <a href="https://admin.mandarinpay.com/user/" target="_blank">Личном кабинете</a> на вкладке Продажи.</p>';

  $Tab2 = $PHPShopGUI->setInfo($info);

  // Форма регистрации
  $Tab3 = $PHPShopGUI->setPay();

  // Вывод формы закладки
  $PHPShopGUI->setTab(array("Основное", $Tab1, true), array("Инструкция", $Tab2), array("О Модуле", $Tab3));

  // Вывод кнопок сохранить и выход в футер
  $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) . $PHPShopGUI->setInput("submit", "saveID", "Применить", "right", 80, "", "but", "actionUpdate.modules.edit");

  $PHPShopGUI->setFooter($ContentFooter);
  return true;
}
// Обработка событий
$PHPShopGUI->getAction();
// Вывод формы при старте
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
