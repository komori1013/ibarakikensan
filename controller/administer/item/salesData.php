<?php
namespace ibarakikensan\controller\administer;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\PDODatabase;
use ibarakikensan\model\administer\Item;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/auth/login.php");
  exit();
}

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR."/administer");
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$item = new Item($db);

$productArr = $item->getInOutData();
$context["productArr"] = $productArr;
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";

$template = $twig->loadTemplate("inOutData.twig");
$template->display($context);