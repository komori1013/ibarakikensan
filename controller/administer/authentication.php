<?php
//http://localhost/ibarakikensan/controller/administer/authentication.php
namespace ibarakikensan\controller\administer;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;
use ibarakikensan\common\PDODatabase;

$validation = new Validation();
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR."/administer");
$twig = new \Twig_Environment($loader, [
  "cache" => Bootstrap::CACHE_DIR
]);

if (isset($_POST["login"]) === true) {
$dataArr = $_POST;
$errArr = $validation->accountPasswordCheck($dataArr);
$err_check = $validation->getErrorFlag();

if ($err_check === false) {
  $template = "login.twig";
} else {
  $table = "administer";
  $col = "admin_account, admin_password";
  $where = "";
  $arrVal = [];
  $res = $db->select($table, $col, $where, $arrVal);
  var_dump($res);
  if($res[0]["admin_account"] === $dataArr["account"] && $res[0]["admin_password"] === $dataArr["password"]){
    session_start();
    $_SESSION["admin_account"] = $res[0]["admin_account"];
    header("Location: http://localhost/ibarakikensan/view/administer/list.twig");
  } else {
    $template = "login.twig";
    $errArr["dbAccountPassword"] = "会員IDまたはパスワードに誤りがあります";
  }
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

