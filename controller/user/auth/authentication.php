<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();
$validation = new Validation();
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

if (isset($_POST["login"]) === true) {
  $dataArr = $_POST;
  $errArr = $validation->accountPasswordCheck($dataArr);
  $err_check = $validation->getErrorFlag();

  if ($err_check === false) {
    $template = "login.twig";
  } else {
    $table = "member";
    $col = "member_account, member_password";
    $where = "member_account = ? AND member_password = ?";
    $arrVal = [$dataArr["account"], $dataArr["password"]];
    $res = $db->select($table, $col, $where, $arrVal);
  
    if($res !== false && count($res) !== 0) {
      $_SESSION["customer_no"] = $res[0]["member_account"];
      $template = "user/auth/logincomplete.twig";
    } else {
      $template = "user/auth/login.twig";
      $errArr["dbAccountPassword"] = "会員IDまたはパスワードに誤りがあります";
    }
  }
}

$context = [];
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";

$template = $twig->loadTemplate($template);
$template->display($context);


