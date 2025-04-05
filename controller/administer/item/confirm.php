<?php
namespace ibarakikensan\controller\administer\item;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Validation;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\model\administer\Item;
use ibarakikensan\common\Session;

$ses = new Session();

if (isset($_SESSION['admin_account']) === false) {
  header("Location: http://localhost/ibarakikensan/controller/administer/auth/login.php");
  exit();
}

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$item = new Item($db);
$validation = new Validation();
$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$customer_no = isset($_SESSION["admin_account"]) ? $_SESSION["admin_account"] = $_SESSION["admin_account"]:"";

if($_POST["confirm"] === "登録確認"){
  unset($_POST["confirm"]);
  $dataArr = $_POST;
  $image_path = $validation->itemImageCheck();
  $dataArr["image"] = $image_path;
  $errArr = $validation->registItemCheck($dataArr);
  $err_check = $validation->getErrorFlag();
  
  if($err_check === false || $image_path === ""){
    $template = "administer/item/regist.twig";
  
  }elseif($err_check === true && $image_path !== "") {
    if($dataArr["inOut"] === "0"){
      $table = "products";
      $col = "product_name";
      $where = "product_name = ?";
      $arrVal = [$dataArr["product"]];
      $res = $db->select($table, $col, $where, $arrVal);
    
      if($res !== false && count($res) !== 0){
        $message = "以下の内容で商品を追加しますか？";
      }else{
        $message = "以下の内容で新規登録しますか？";
      }
      $template = "administer/item/confirm.twig";

    }elseif($dataArr["inOut"] === "1"){
      $table = "products";
      $col = "product_name, stock_quantity"; 
      $where = "product_name = ? AND stock_quantity >= ?";
      $arrVal = [$dataArr["product"], $dataArr["quantity"]];
      $res = $db->select($table, $col, $where, $arrVal);
      
      if($res !== false && count($res) !== 0){
        $message = "以下の内容で商品を出庫しますか？";
        $template = "administer/item/confirm.twig";
      }else{
        $message = "在庫がありません";
        $template = "administer/item/regist.twig";
      }
    }
  }
}elseif($_POST["confirm"] === "戻る"){
  unset($_POST["confirm"]);
  $dataArr = $_POST;
  $template = "administer/item/regist.twig";

}elseif($_POST["confirm"] === "登録完了"){
  unset($_POST["confirm"]);
  $dataArr = $_POST;
  $template = "administer/item/complete.twig";
  $item->processInventory($dataArr, $customer_no);
}

list($inOutArr, $reasonArr, $ctgArr) = [$item->inOutArr, $item->reasonArr, $item->ctgArr];

$context = [];
isset($message)? $context["message"]  = $message  : "";
$context["inOutArr"] = $inOutArr;
$context["reasonArr"] = $reasonArr;
$context["ctgArr"] = $ctgArr;
$context["dataArr"] = $dataArr;
isset($errArr) ? $context["errArr"] = $errArr : "";
isset($_SESSION["admin_account"])? $context["admin_account"] = $_SESSION["admin_account"]:"";

$template = $twig->loadTemplate($template);
$template->display($context);