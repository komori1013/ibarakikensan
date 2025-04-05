<?php
namespace ibarakikensan\controller\administer\item;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\administer\Item;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/auth/login.php");
  exit();
}

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$item = new Item($db);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$dataArr = [
  "product" => "",
  "price" => "",
  "image" => "",
  "inout" => "",
  "reason" => "",
  "category" => "",
  "quantity" => "",
  "detail" => ""
];

$errArr =[];
foreach ($dataArr as $key => $value){
  $errArr[$key] = "";
}

list($inOutArr, $reasonArr, $ctgArr) = [$item->inOutArr, $item->reasonArr, $item->ctgArr];

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
$context["inOutArr"] = $inOutArr;
$context["reasonArr"] = $reasonArr;
$context["ctgArr"] = $ctgArr;
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";
$template = $twig->loadTemplate("administer/item/regist.twig");
$template->display($context);
?>
