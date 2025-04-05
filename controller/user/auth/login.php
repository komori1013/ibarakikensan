<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

if (isset($_SESSION['customer_no'])) {
  $template = ("logincomplete.twig");
}else{
  $template = ("user/auth/login.twig");
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
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";

$template = $twig->loadTemplate($template);
$template->display($context);
