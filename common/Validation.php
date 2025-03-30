<?php
namespace ibarakikensan\common;

class Validation {
  private $dataArr = [];
  private $errArr = [];

  public function __construct() {
  }

  public function accountPasswordCheck($dataArr) {
    $this->dataArr = $dataArr;
    $this->createErrorMessage();
    if ($this->dataArr["account"] === "") {
      $this->errArr["account"] = "IDを入力してください";
    }
    if ($this->dataArr["password"] === "") {
      $this->errArr["password"] = "パスワードを入力してください";
    }
    return $this->errArr;
  }

  private function createErrorMessage()
  {
    foreach ($this->dataArr as $key => $val) {
      $this->errArr[$key] = "";
    }
  }

  public function getErrorFlag() {
    $err_check = true;
    foreach ($this->errArr as $key => $val) {
      if ($val !== "") {
        $err_check = false;
      }
    }
    return $err_check;
  }

}