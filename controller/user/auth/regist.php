<?php
namespace ibarakikensan\controller\user;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Date;

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$dataArr = [
  "member_account" => "",
  "member_password" => "",
  "family_name" => "",
  "first_name" => "",
  "family_name_kana" => "",
  "first_name_kana" => "",
  "year" => "",
  "month" => "",
  "day" => "",
  "zip1" => "",
  "zip2" => "",
  "address" => "",
  "email" => "",
  "tel1" => "",
  "tel2" => "",
  "tel3" => "",
];

$errArr =[];
foreach ($dataArr as $key => $value){
  $errArr[$key] = "";
}
list($yearArr, $monthArr, $dayArr) = Date::getDate();
$context = [];
$context["yearArr"] = $yearArr;
$context["monthArr"] = $monthArr;
$context["dayArr"] = $dayArr;
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;

$template = $twig->loadTemplate("user/auth/regist.twig");
$template->display($context);