<?php
namespace ibarakikensan\controller\user\item;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Date;
use ibarakikensan\common\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$validation = new Validation($db);
$ses = new Session($db);

if (isset($_POST["confirm"]) === true) {
  $mode = "confirm";
}

if (isset($_POST["back"]) === true) {
  $mode = "back";
}

if (isset($_POST["complete"]) === true) {
  $mode = "complete";
}

switch ($mode) {

  case "confirm";
    unset($_POST["confirm"]);
    $dataArr = $_POST;
    $errArr = $validation->errorCheck($dataArr);
    $err_check = $validation->getErrorFlag();
    $template = ($err_check === true) ? "user/auth/confirm.twig" : "user/auth/regist.twig";
  break;

  case "back";
    $dataArr = $_POST;
    unset($dataArr["back"]);
    foreach ($dataArr as $key => $value) {
      $errArr[$key] = "";
    }
    $template = "user/auth/regist.twig";
  break;

  case "complete";
    $dataArr = $_POST;
    unset($dataArr["complete"]); 
    $res = $db->insert("member", $dataArr);
    if ($res === true) {
      $_SESSION["customer_no"] = $dataArr["member_account"];
      $template = "user/auth/registcomplete.twig";
      foreach($dataArr as $key => $value) {
        $errArr[$key] = "";
      }
    }else{
      $template = "user/auth/regist.twig";
      foreach($dataArr as $key => $value) {
        $errArr[$key] = "";
      }
    }
  break;

}


list($yearArr, $monthArr, $dayArr) = Date::getDate();
$context["yearArr"] = $yearArr;
$context["monthArr"] = $monthArr;
$context["dayArr"] = $dayArr;
$context["dataArr"] = $dataArr;
$context["errArr"] = $errArr;
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";
$template = $twig->loadTemplate($template);
$template->display($context);