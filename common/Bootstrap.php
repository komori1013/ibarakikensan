<?php
namespace ibarakikensan\common;

require_once dirname(__FILE__).'/../vendor/autoload.php';

class Bootstrap {
  const DB_HOST = "localhost";
  const DB_USER = "ibarakikensan_user";
  const DB_PASS = "ibarakikensan_pass";
  const DB_NAME = "ibarakikensan_db";
  const DB_PORT = 13306;
  const DB_TYPE = "mysql";

  const APP_DIR = "c:/xampp/htdocs/";

  const APP_URL = "http://localhost/ibarakikensan/";

  const ENTRY_URL = "http://localhost/ibarakikensan/";

  const VIEW_DIR = "c:/xampp/htdocs/ibarakikensan/view/";
  const CACHE_DIR = false;

  public static function loadClass($class) {
    $path = str_replace('\\', "/", self::APP_DIR. $class. ".php");
    require_once $path;
  }
}

spl_autoload_register([
  "ibarakikensan\common\Bootstrap", 
  "loadClass"
]);