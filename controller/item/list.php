<?php
//http://localhost/ibarakikensan/controller/item/list.php
namespace ibarakikensan\controller\item;

require_once dirname(__FILE__)."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\model\user\Item;
use ibarakikensan\common\Session;

$ses = new Session();
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);
$itm = new Item($db);
$loader =new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '';

if(isset($_GET['searchKeyword']) === true){
  $searchKeyword = $_GET['searchKeyword'];
  $dataArr = $itm->sortItemList($searchKeyword);
}else{
  $dataArr = $itm->getItemList($ctg_id);
}
$cateArr = $itm->getCategoryList();
$context = [];
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
isset($_SESSION["customer_no"])? $context["customer_no"] = $_SESSION["customer_no"]:"";

$template = $twig->loadTemplate('/user/item/list.twig');
$template->display($context);
