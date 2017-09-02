<?php
/**
* @author github.com/vuchastyi
* @version 0.0.1
* @package PHPShopPayment
*/

$R = $_POST;

function WriteLog($MY_LMI_HASH) {
  $handle = fopen("../paymentlog.log", "a+");

  foreach ($R as $k => $v)@$post.=$k . "=" . $v . "\r\n";

  $str = "
  MandarinPay Payment Start ------------------
  date=" . date("F j, Y, g:i a") . "
  $post
  MY_LMI_HASH=$MY_LMI_HASH
  REQUEST_URI=" . $_SERVER['REQUEST_URI'] . "
  IP=" . $_SERVER['REMOTE_ADDR'] . "
  MandarinPay Payment End --------------------
  ";
  fwrite($handle, $str);
  fclose($handle);
}

function UpdateNumOrder($uid) {
  $last_num = substr($uid, -2);
  $total = strlen($uid);
  $ferst_num = substr($uid, 0, ($total - 2));
  return $ferst_num . "-" . $last_num;
}

// Parse config.ini
$SysValue = parse_ini_file("../../phpshop/inc/config.ini", 1);
while (list($section, $array) = each($SysValue))
    while (list($key, $value) = each($array))
        $SysValue['other'][chr(73) . chr(110) . chr(105) . ucfirst(strtolower($section)) . ucfirst(strtolower($key))] = $value;

//get secret key and merchant id
$merchant_id = $SysValue['mandarin-hosted']['MANDARIN_HOSTED_MID'];
$secret_key = $SysValue['mandarin-hosted']['MANDARIN_HOSTED_SECRET'];


//check IP
if(count($R) && isset($R['merchantId'])){
  //get request data
  $data = $R;
  $request_sign = $data['sign'];
  unset($data['sign']);
  ksort($data);
  array_push($data, $secret_key);
  $signString = implode('-', $data);
  $sign = hash("sha256",$signString);


  if($sign !== $request_sign || $data['merchantId'] !== $merchant_id){
    WriteLog($sign."bad sign\n");
    exit("bad sign\n");
  }

  $order_id = $data['orderId'];

  // Connect to MySQL
  $link_db=mysqli_connect($SysValue['connect']['host'], $SysValue['connect']['user_db'], $SysValue['connect']['pass_db']);
  mysqli_select_db($link_db,$SysValue['connect']['dbase']);

  $new_uid = UpdateNumOrder($order_id);

  // If isset order
  $sql = "select uid from " . $SysValue['base']['table_name1'] . " where uid='$new_uid'";
  $result = mysqli_query($link_db,$sql);
  $row = mysqli_fetch_array($result);
  $uid = $row['uid'];

  if ($uid === $new_uid){
    // write payment to database
    $sql = "INSERT INTO " . $SysValue['base']['table_name33'] . " VALUES ('$new_uid','mandarin-hosted','" . $R['price'] . "','" . time() . "')";
    $result = mysqli_query($link_db,$sql);

    // change order state to paid
    $sql = "UPDATE " . $SysValue['base']['table_name1'] . " SET statusi=4 WHERE uid='$new_uid'";
    $result = mysqli_query($link_db,$sql);

    exit("OK");
  }else{
    WriteLog($sign);
    exit("bad order num\n");
  }
}

exit('No data specified');
