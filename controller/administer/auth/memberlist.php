<?php
namespace ibarakikensan\controller\administer\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/login.php");
  exit();
}

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$query = "SELECT"
  ." member_id,"
  ." member_account,"
  ." member_password,"
  ." family_name,"
  ." first_name,"
  ." family_name_kana,"
  ." first_name_kana,"
  ." email,"
  ." created_at"
  ." FROM"
  ." member";

$dataArr = $db->setquery($query, $where=[]);
$context = [];
$context["dataArr"] = $dataArr;
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";
$template = $twig->loadTemplate("administer/auth/memberlist.twig");
$template->display($context);