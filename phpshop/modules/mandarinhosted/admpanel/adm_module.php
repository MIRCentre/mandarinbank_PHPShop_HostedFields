<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.mandarinhosted.mandarinhosted_system"));

// ���������� ������ ������
function actionBaseUpdate() {
  global $PHPShopModules, $PHPShopOrm;
  $PHPShopOrm->clean();
  $option = $PHPShopOrm->select();
  $new_version = $PHPShopModules->getUpdate($option['version']);
  $PHPShopOrm->clean();
  $action = $PHPShopOrm->update(array('version_new' => $new_version));
  return $action;
}

// ������� ����������
function actionUpdate() {
  global $PHPShopOrm;

  $PHPShopOrm->debug = false;
  $action = $PHPShopOrm->update($_POST);
  header('Location: ?path=modules&id=mandarinhosted');
  return $action;
}

function actionStart() {
  global $PHPShopGUI, $PHPShopOrm;

  // �������
  $data = $PHPShopOrm->select();

  $Tab1.=$PHPShopGUI->setField('ID ��������', $PHPShopGUI->setInputText(false, 'merchant_key_new', $data['merchant_key'], 250));
  $Tab1.=$PHPShopGUI->setField('��������� ����', $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 250));

  $info = '
<p></p>

<h4>��������� ������</h4>
       <ol>
       <li>������������������ � <a href="http://nextpay.ru/" target="_blank">NextPay</a>. ��� ������ ��� ����������� ��������� ������������ ��� ������ ������ �� ����������� ����� �������� � ���� "�������� �����" ����� "����������� ����/�� (��� ���������� ��������)" � ��������� ��������� ����� �����������.
       <li>�������� ������� � �������� �������� � ������� � ������� <kbd>��������</kbd> - <kbd>������� �������</kbd>
<li>� ���������� �������� � ���� "URL �������� ������" ������� <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules//payment/result.php</code>
        </ol>

';

  $Tab2 = $PHPShopGUI->setInfo($info);

  // ����� �����������
  $Tab3 = $PHPShopGUI->setPay();

  // ����� ����� ��������
  $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

  // ����� ������ ��������� � ����� � �����
  $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) . $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

  $PHPShopGUI->setFooter($ContentFooter);
  return true;
}
// ��������� �������
$PHPShopGUI->getAction();
// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
