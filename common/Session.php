<?php
namespace ibarakikensan\common;

class Session {
  public function __construct() {
    session_start();

    // セッションの有効期限を30分（1800秒）に設定
    $sessionTimeout = 1800; 
    
    if (isset($_SESSION["last_activity"]) && (time() - $_SESSION["last_activity"] > $sessionTimeout)) {
        session_unset(); 
        session_destroy(); 
        header("Location: http://localhost/ibarakikensan/controller/administer/login.php");
        exit();
    }
    
    $_SESSION["last_activity"] = time();
  }

}
