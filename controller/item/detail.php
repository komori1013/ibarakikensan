<?php
namespace ibarakikensan\controller\user\item;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\model\user\Item;
use ibarakikensan\common\Session;

$ses = new Session();
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$itm = new Item($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$product_id = (isset($_GET['product_id']) === true && preg_match('/^\d+$/', $_GET['product_id']) === 1) ? $_GET['product_id'] : '';
if ($product_id === ""){
    header("Location:" . Bootstrap::ENTRY_URL. "/controller/item/list.php");
}

$cateArr = $itm->getCategoryList();
$productData = $itm->getItemDetailData($product_id);
$context = [];
$context['cateArr'] = $cateArr;
$context['productData'] = $productData[0];
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";

$template = $twig->loadTemplate('user/item/detail.twig');
$template->display($context);