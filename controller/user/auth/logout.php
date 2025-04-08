<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

unset($_SESSION['customer_no']);

$template = $twig->loadTemplate("user/auth/logout.twig");
$template->display([]);