<?php
namespace ibarakikensan\controller\cart;

require_once dirname(__FILE__). "/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;

$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR."/user");
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);

$template = $twig->loadTemplate("complete.twig");
$template->display([]);