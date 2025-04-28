<?php
namespace ibarakikensan\controller\administer\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

if (isset($_POST["member_id"]) === true) {
    $table = "member";
    $insData = [ "delete_flg" => 1 ]; // 更新したいカラムと値
    $where = "member_id = ?";         // WHERE 条件
    $arrVal = [ $_POST["member_id"] ]; // プレースホルダに渡す値
  
    // $db->update() を呼び出す
    $res = $db->update($table, $insData, $where, $arrVal);
  
    if ($res) {
      $template = "administer/user/withdrawalComplete.twig";
      $context = [];
    } else {
      echo "更新に失敗しました";
    }
  }

  $template = $twig->loadTemplate($template);
  $template->display($context);