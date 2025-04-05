<?php
namespace ibarakikensan\controller\administer\item;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\model\administer\Item;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/auth/login.php");
  exit();
}

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$item = new Item($db);

if (isset($_POST["start_date"]) && isset($_POST["end_date"]) && $_POST["start_date"] !== '' && $_POST["end_date"] !== '') {
  $start_date = $_POST["start_date"];
  $end_date = $_POST["end_date"];
} else {
  $start_date = null;
  $end_date = null;
}

$productArr = $item->getInOutData($start_date, $end_date);
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";
$context["productArr"] = $productArr;
$template = $twig->loadTemplate("administer/item/inOutData.twig");
$template->display($context);