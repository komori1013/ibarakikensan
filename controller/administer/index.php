<?php
//http://localhost/ibarakikensan/controller/administer/index.php
namespace ibarakikensan\controller\administer;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/auth/login.php");
  exit();
}

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$context = [];
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";

$template = $twig->loadTemplate("/administer/index.twig");
$template->display($context);
