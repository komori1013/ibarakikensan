<?php
//http://localhost/ibarakikensan/controller/administer/authentication.php
namespace ibarakikensan\controller\administer;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;

$validation = new Validation();

$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR."/administer");
$twig = new \Twig_Environment($loader, [
  "cache" => Bootstrap::CACHE_DIR
]);

if (isset($_POST["confirm"]) === true) {
$dataArr = $_POST;
$errArr = $validation->accountPasswordCheck($dataArr);
$err_check = $validation->getErrorFlag();

if ($err_check === false) {
  $template = "login.twig";
}

echo "<pre>";
var_dump($dataArr);
var_dump($errArr);
echo "</pre>";

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;

$template = $twig->loadTemplate($template);
$template->display($context);

}

