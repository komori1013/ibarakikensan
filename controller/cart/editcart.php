<?php
namespace ibarakikensan\controller\cart;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;
use ibarakikensan\model\cart\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$ses = new Session($db);
$cart = new Cart($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$customer_no = $_SESSION['customer_no'];

if($customer_no === null){
  header("Location: http://localhost/ibarakikensan/controller/user/auth/login.php");
}

if (isset($_GET['product_id']) && isset($_GET['quantity']) && isset($_GET['price']) && isset($_GET['crt_id'])) {
  $item_id = (int)$_GET['product_id'];
  $quantity = (int)$_GET['quantity'];
  $price = (int)$_GET['price'];
  $crt_id = (int)$_GET['crt_id']; 
  $res = $cart->updateCartData($crt_id, $item_id, $quantity, $price);
}

$dataArr = $cart->getCartData($customer_no);
list($sumNum, $sumPrice) = $cart->getItemAndSumPrice($customer_no);
$context = [];
$context['sumNum'] = $sumNum;
$context['sumPrice'] = $sumPrice;
$context['dataArr'] = $dataArr;
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";
$template = $twig->loadTemplate('cart/cart.twig');
$template->display($context);
?>