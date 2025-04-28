<?php
// http://localhost/ibarakikensan/controller/user/auth/mypage.php
namespace ibarakikensan\controller\user;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$ses = new Session($db);

$table = "member";
$column = "member_id, member_account, member_password, family_name, first_name, family_name_kana, first_name_kana, year, month, day, zip1, zip2, address, email, tel1, tel2, tel3";
$Where = "member_account = ? and delete_flg = ?";
$arrVal = [$_SESSION["customer_no"], 0];

$memberData = $db->select($table, $column, $Where, $arrVal);

$context["memberData"] = $memberData[0];
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";
$template = $twig->loadTemplate("user/auth/mypage.twig");
$template->display($context);