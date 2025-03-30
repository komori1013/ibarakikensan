<?php
//http://localhost/ibarakikensan/common/Bootstrap.php
namespace ibarakikensan\common;

require_once dirname(__FILE__).'/../vendor/autoload.php';
// dirname(__FILE__) = C:\xampp\htdocs\ibarakikensan\common

class Bootstrap {
  //データベースの接続情報
  const DB_HOST = "localhost";
  const DB_USER = "ibarakikensan_user";
  const DB_PASS = "ibarakikensan_pass";
  const DB_NAME = "ibarakikensan_db";
  const DB_PORT = 13306;
  const DB_TYPE = "mysql";

  //アプリケーションのルートディレクトリ
  const APP_DIR = "c:/xampp/htdocs/";

  //アプリケーションのURL
  const APP_URL = "http://localhost/ibarakikensan/";

  //エントリーポイント（仮）
  const ENTRY_URL = "http://localhost/ibarakikensan/list.php/";

  //ビューのディレクトリ
  const VIEW_DIR = "c:/xampp/htdocs/ibarakikensan/view/";
  const CACHE_DIR = false;

  //クラスの自動読み込み
  public static function loadClass($class) {
    $path = str_replace('\\', "/", self::APP_DIR. $class. ".php");
    require_once $path;
  }
}

spl_autoload_register([
  "ibarakikensan\common\Bootstrap", 
  "loadClass"
]);