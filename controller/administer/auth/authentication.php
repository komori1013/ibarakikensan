<?php
namespace ibarakikensan\controller\administer\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();
$validation = new Validation();
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, [
  "cache" => Bootstrap::CACHE_DIR
]);

if (isset($_POST["login"]) === true) {
  $dataArr = $_POST;
  $errArr = $validation->accountPasswordCheck($dataArr);
  $err_check = $validation->getErrorFlag();

  if ($err_check === false) {
    $template = "administer/auth/login.twig";
  } else {
    $table = "administer";
    $col = "admin_account, admin_password";
    $where = "";
    $arrVal = [];
    $res = $db->select($table, $col, $where, $arrVal);
    if($res[0]["admin_account"] === $dataArr["account"] && $res[0]["admin_password"] === $dataArr["password"]){
      session_start();
      $_SESSION["admin_account"] = $res[0]["admin_account"];
      header("Location: http://localhost/ibarakikensan/controller/administer/index.php");
    } else {
      $template = "administer/auth/login.twig";
      $errArr["dbAccountPassword"] = "会員IDまたはパスワードに誤りがあります";
    }
  }
}

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";

$template = $twig->loadTemplate($template);
$template->display($context);



