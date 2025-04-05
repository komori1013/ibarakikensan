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

  public function registItemCheck($dataArr) {
    $this->dataArr = $dataArr;
    $this->createErrorMessage();
    
    // issetで確認してからアクセス
    if (!isset($this->dataArr["inOut"]) || $this->dataArr["inOut"] === "") {
        $this->errArr["inOut"] = "入出庫を選択してください";
    }
    if (!isset($this->dataArr["reason"]) || $this->dataArr["reason"] === "") {
        $this->errArr["reason"] = "区別を選択してください";
    }
    if (!isset($this->dataArr["product"]) || $this->dataArr["product"] === "") {
        $this->errArr["product"] = "商品名を入力してください";
    }
    if (!isset($this->dataArr["quantity"]) || $this->dataArr["quantity"] === "") {
        $this->errArr["quantity"] = "個数を入力してください";
    }
    if (!isset($this->dataArr["category"]) || $this->dataArr["category"] === "") {
        $this->errArr["category"] = "カテゴリーを入力してください";
    }
    
    return $this->errArr;
}

  public function itemImageCheck()
  {
      $tmp_image = $_FILES["image"] ?? null;
  
      if (!$tmp_image || $tmp_image["error"] !== UPLOAD_ERR_OK) {
          return;
      }
  
      if (!is_uploaded_file($tmp_image["tmp_name"])) {
          $this->errArr["image"] = "不正なアップロードです";
          return;
      }
  
      $image_info = getimagesize($tmp_image["tmp_name"]);
      if ($image_info === false) {
          $this->errArr["image"] = "アップロードされたファイルは、画像ではありません";
          return;
      }
  
      if ($tmp_image["size"] > 1048576) {
          $this->errArr["image"] = "アップロードできる画像のサイズは、1MBまでです";
          return;
      }
  
      $allowed_types = ["image/jpeg"];
      if (!in_array($image_info["mime"], $allowed_types, true)) {
          $this->errArr["image"] = "アップロードできる画像の形式は、JPEG形式だけです";
          return;
      }
  
      $target_dir = __DIR__ . '/../image/';
      if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
          $this->errArr["image"] = "画像ディレクトリの作成に失敗しました";
          return;
      }
  
      $target_path = $target_dir . time() . '.jpg';
      if (!move_uploaded_file($tmp_image["tmp_name"], $target_path)) {
          $this->errArr["image"] = "画像のアップロードに失敗しました";
          return;
      }
  
      return $target_path;
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


  public function errorCheck($dataArr)
  {
    $this->dataArr = $dataArr;
    $this->createErrorMessage();
    $this->accountCheck();
    $this->passwordCheck();
    $this->familyNameCheck();
    $this->firstNameCheck();
    $this->birthCheck();
    $this->zipCheck();
    $this->addCheck();
    $this->telCheck();
    $this->mailCheck();

    return $this->errArr;
  }

  public function accountCheck() {
    if ($this->dataArr["member_account"] === "") {
      $this->errArr["member_account"] = "IDを入力してください";
    }
  }

  public function passwordCheck() {
    if ($this->dataArr["member_password"] === "") {
      $this->errArr["member_password"] = "passwordを入力してください";
    }
  }


  private function familyNameCheck()
  {
    if ($this->dataArr["family_name"] === "") {
      $this->errArr["family_name"] = "お名前(氏)を入力してください";
    }
  }

  private function firstNameCheck()
  {
    if ($this->dataArr["first_name"] === "") {
      $this->errArr["first_name"] = "お名前(名)を入力してください";
    }
  }


  private function birthCheck()
  {
    if ($this->dataArr["year"] === "") {
      $this->errArr["year"] = "生年月日の年を選択してください";
    }
    if ($this->dataArr["month"] === "") {
      $this->errArr["month"] = "生年月日の月を選択してください";
    }
    if ($this->dataArr["day"] === "") {
      $this->errArr["day"] = "生年月日の日を選択してください";
    }

    if (checkdate($this->dataArr["month"], $this->dataArr["day"], $this->dataArr["year"]) === false) {
      $this->errArr["year"] = "正しい日付を入力してください";
    }

    if(strtotime($this->dataArr["year"]. "-". $this->dataArr["month"]. "-". $this->dataArr["day"]) - strtotime("now") > 0) {
      $this->errArr["year"] = "正しい日付を入力してください";
    }
  }

  private function zipCheck()
  {
    if (preg_match("/^[0-9]{3}$/", $this->dataArr["zip1"]) === 0) {
      $this->errArr["zip1"] = "郵便番号の上は半角数字3桁で入力してください";
    }

    if (preg_match("/^[0-9]{4}$/", $this->dataArr["zip2"]) === 0) {
      $this->errArr["zip2"] = "郵便番号の下は半角数字4桁で入力してください";
    }
  }

  private function addCheck()
  {
    if ($this->dataArr["address"] === "") {
      $this->errArr["address"] = "住所を入力してください";
    }
  }

  private function mailCheck()
  {
    if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+[a-zA-Z0-9\._-]+$/", $this->dataArr["email"]) === 0) {
      $this->errArr["email"] = "メールアドレスを正しい形式で入力してください"; 
    }
  }

  private function telCheck()
  {
    if (preg_match("/^\d{1,6}$/", $this->dataArr["tel1"]) === 0 ||
        preg_match("/^\d{1,6}$/", $this->dataArr["tel2"]) === 0 ||
        preg_match("/^\d{1,6}$/", $this->dataArr["tel3"]) === 0 ||
        strlen($this->dataArr["tel1"]. $this->dataArr["tel2"]. $this->dataArr["tel3"]) >= 12) {
          $this->errArr["tel1"] = "電話番号は、半角数字で11桁以内で入力してください";
        }
  }

}