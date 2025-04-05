<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

if(isset($_GET["zip1"]) === true && isset($_GET["zip2"]) === true){
  $zip1 = $_GET["zip1"];
  $zip2 = $_GET["zip2"];
  $zip = $zip1 . $zip2;
  $query= "SELECT pref, city, town FROM postcode WHERE zip = ? LIMIT 1";
  $arrVal = [$zip];
  $res = $db->setQuery($query, $arrVal);
  echo ($res !== "" && count($res) !== 0) ? $res[0]["pref"].$res[0]["city"].$res[0]["town"] : ""; 
} else {
  echo "no";
}