<?php
//http://localhost/ibarakikensan/controller/administer/login.php
namespace ibarakikensan\controller\administer;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;

session_start();

if (isset($_SESSION['session_no'])) {
    header("Location: http://localhost/ibarakikensan/controller/administer/regist.php");
    exit();
}

$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR."/administer");
$twig = new \Twig_Environment($loader, [
  "cache" => Bootstrap::CACHE_DIR
]);

$dataArr = [
  "account" => "",
  "password" => ""
];

$errArr = [];
foreach($dataArr as $key => $val){
  $errArr[$key] = "";
};

echo "<pre>";
var_dump($dataArr);
var_dump($errArr);
echo "</pre>";

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;

$template = $twig->loadTemplate("login.twig");
$template->display($context);
