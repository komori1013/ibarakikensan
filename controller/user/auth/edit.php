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

if (isset($_POST["edit"]) === true) {
  $mode = "edit";
}

if (isset($_POST["withdrawal"]) === true) {
  $mode = "withdrawal";
}

if (isset($_POST["complete"]) === true) {
  $mode = "complete";
}

switch ($mode) {

  case "edit";
    unset($_POST["edit"]);
    $dataArr = $_POST;
    $errArr = $validation->errorCheck($dataArr);
    $err_check = $validation->getErrorFlag();
    $template = ($err_check === true) ? "user/auth/edit.twig" : "user/auth/edit.twig";
  break;

  case "withdrawal";
    unset($_POST["withdrawal"]);
    $dataArr = $_POST;
    if (isset($_POST["member_id"]) === true) {
    $table = "member";
    $insData = [ "delete_flg" => 1 ]; // 更新したいカラムと値
    $where = "member_id = ?";         // WHERE 条件
    $arrVal = [ $_POST["member_id"] ]; // プレースホルダに渡す値
  
    // $db->update() を呼び出す
    $res = $db->update($table, $insData, $where, $arrVal);
  
    if ($res) {
      unset($_SESSION["customer_no"]);
      $template = "/user/auth/withdrawalComplete.twig";
      $context = [];
    } else {
      echo "更新に失敗しました";
    }
  }

  break;

    case "complete";
    $dataArr = $_POST;
    unset($dataArr["complete"]);
    $errArr = $validation->errorCheck($dataArr);
    $err_check = $validation->getErrorFlag();
    if($err_check === true){
    $table = "member";
    $insData = [
      "zip1" => $dataArr["zip1"],
      "zip2" => $dataArr["zip2"],
      "address" => $dataArr["address"],
      "email" => $dataArr["email"],
      "tel1" => $dataArr["tel1"],
      "tel2" => $dataArr["tel2"],
      "tel3" => $dataArr["tel3"],
    ];
    $where = "member_id = ? AND delete_flg = ?";
    $arrWhereVal = [$dataArr["member_id"], 0]; 
  
    $res = $db-> update($table, $insData, $where, $arrWhereVal);
      if ($res === true) {
        $template = "user/auth/registcomplete.twig";
        foreach($dataArr as $key => $value) {
          $errArr[$key] = "";
        }
      } else {
        echo "更新に失敗しました";
      };
    }else{
      $template = "user/auth/edit.twig";
    }

  break;
  }

list($yearArr, $monthArr, $dayArr) = Date::getDate();
$context["yearArr"] = $yearArr;
$context["monthArr"] = $monthArr;
$dayArr["dayArr"] = $dayArr;
$context["dataArr"] = $dataArr;
if(isset($errArr))$context["errArr"] = $errArr;
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";
$template = $twig->loadTemplate($template);
$template->display($context);