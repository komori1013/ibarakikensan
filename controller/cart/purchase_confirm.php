<?php
namespace ibarakikensan\controller\cart;
require_once dirname(__FILE__) ."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

// カートからPOSTされたデータを受け取る
$dataArr = $_POST;
// submitを削除する！（ここが大事）
unset($dataArr["submit"]);


// データがなかったらカートに戻す
if (empty($dataArr)) {
    header("Location: http://localhost/ibarakikensan/controller/cart/cart.php");
    exit();
}


$table = "member";
$column = "family_name, first_name, zip1, zip2, address, tel1, tel2, tel3, email";
$where = "member_account = ? AND delete_flg = ?";
$arrVal = [$_SESSION['customer_no'], 0];

$customer = $db->select($table, $column, $where, $arrVal);

// Twigで確認画面を表示
$template = $twig->load("cart/purchase_confirm.twig");
$template->display([
  "dataArr" => $dataArr,  
  "customer" => $customer[0],  // ここでcustomerを渡す
  "customer_no" => $_SESSION["customer_no"] ?? ""
]);