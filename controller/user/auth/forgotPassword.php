<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\common\Session;

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$context = [];

$template = $twig->loadTemplate("user/auth/forgotPassword.twig");
$template->display($context);