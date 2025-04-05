<?php
//http://localhost/ibarakikensan/controller/administer/auth/login.php
namespace ibarakikensan\controller\administer\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR
]);

if (isset($_SESSION['admin_account'])) {
    header("Location: http://localhost/ibarakikensan/controller/administer/index.php");
    exit();
}

$dataArr = [
  "account" => "",
  "password" => ""
];

$errArr = [];
foreach($dataArr as $key => $val){
  $errArr[$key] = "";
};

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";

$template = $twig->loadTemplate("administer/auth/login.twig");
$template->display($context);
