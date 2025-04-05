<?php
namespace ibarakikensan\controller\cart;
require_once dirname(__FILE__) . "/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

$dataArr = $_POST;
$errArr = [];

unset($dataArr["submit"]);
$templateFile = "cart/cart.twig";
$hasError = false;

if($dataArr == null){
    header ("Location: http://localhost/ibarakikensan/controller/cart/cart.php");
}

foreach($dataArr as $key => $item) {
  $product = $item['product_id'];
  $productName = $item['product_name'];
  $crt_id = $item['crt_id'];
  $quantity = intval($item['num']);
  $productData = $db->select("products", "product_id, stock_quantity", "product_id = ?", [$product]);
  
  if(empty($productData)) {
    $errArr[$key]["error"] = "商品が見つかりません";
    $errArr[$key]["product_name"] = $productName;
    $hasError = true;
    continue;
  }
  
  $currentStock = intval($productData[0]['stock_quantity']);
  $newStock = $currentStock - $quantity;

  if($newStock < 0) {
    $errArr[$key]["error"] = "在庫不足です";
    $errArr[$key]["product_name"] = $productName;
    $hasError = true;
    continue;
  }

  $db->setQuery("UPDATE products SET stock_quantity = ? WHERE product_id = ?", [$newStock, $product]);
  $db->setQuery("INSERT INTO inventory_movements (product_name, change_type, quantity, reason, customer_no) VALUES (?, ?, ?, ?, ?)", [
    $productName, "OUT", $quantity, "購入" , $_SESSION["customer_no"]]);
  $db->setQuery("DELETE FROM cart WHERE crt_id = ?", [$crt_id]);
    
}

if(!$hasError) {
  $templateFile = "cart/complete.twig";
}

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
isset($_SESSION["customer_no"]) ? $context["customer_no"] = $_SESSION["customer_no"] : "";

$template = $twig->load($templateFile);
$template->display($context);