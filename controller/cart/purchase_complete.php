<?php
namespace ibarakikensan\controller\cart;
require_once dirname(__FILE__) ."/../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use ibarakikensan\common\Session;

$ses = new Session();
$loader = new \Twig_Loader_Filesystem(Bootstrap::VIEW_DIR);
$twig = new \Twig_Environment($loader, ["cache" => Bootstrap::CACHE_DIR]);
$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_PORT, Bootstrap::DB_TYPE);

$dataArr = $_POST;
$creditErrArr = [];
$productErrArr = [];

$templateFile = "cart/purchase_confirm.twig"; // ← 最初から確認ページ！

$hasError = false;

// クレジットカード情報のバリデーション
$cardNumber = $dataArr['card_number'] ?? ''; // クレジットカード番号
$expirationDate = $dataArr['expiration_date'] ?? ''; // 有効期限（例: MM/YY）
$security_code = $dataArr['security_code'] ?? ''; // セキュリティコード（CVV）

// クレジットカード情報が空でないかを確認
if (empty($cardNumber)) {
    $creditErrArr[] = "クレジットカード番号は必須です";
    $hasError = true;
} elseif (!preg_match('/^\d{12}$/', $cardNumber)) { // 16桁の数字か確認
    $creditErrArr[] = "クレジットカード番号は12桁の数字で入力してください";
    $hasError = true;
}

if (empty($expirationDate)) {
    $creditErrArr[] = "有効期限は必須です";
    $hasError = true;
} elseif (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expirationDate)) { // MM/YY形式を確認
    $creditErrArr[] = "有効期限はMM/YY形式で入力してください";
    $hasError = true;
}

if (empty($security_code)) {
    $creditErrArr[] = "セキュリティコードは必須です";
    $hasError = true;
} elseif (!preg_match('/^\d{3}$/', $security_code)) { // 3桁の数字か確認
    $creditErrArr[] = "セキュリティコードは3桁の数字で入力してください";
    $hasError = true;
}

unset($dataArr["card_number"], $dataArr["expiration_date"], $dataArr["security_code"], $dataArr["submit"]);

// 商品の在庫チェック
foreach($dataArr as $key => $item) {
    $product = $item['product_id'];
    $productName = $item['product_name'];
    $crt_id = $item['crt_id'];
    $quantity = intval($item['num']);
    $productData = $db->select("products", "product_id, stock_quantity", "product_id = ?", [$product]);

    if(empty($productData)) {
        $productErrArr[$key]["error"] = "商品が見つかりません";
        $productErrArr[$key]["product_name"] = $productName;
        $hasError = true;
        continue;
    }

    $currentStock = intval($productData[0]['stock_quantity']);
    $newStock = $currentStock - $quantity;

    if($newStock < 0) {
        $productErrArr[$key]["error"] = "在庫不足です";
        $productErrArr[$key]["product_name"] = $productName;
        $hasError = true;
        continue;
    }
}

// エラーがなかったら、購入処理をする
if (!$hasError) {
    foreach($dataArr as $item) {
        $product = $item['product_id'];
        $productName = $item['product_name'];
        $crt_id = $item['crt_id'];
        $quantity = intval($item['num']);
        
        $productData = $db->select("products", "product_id, stock_quantity", "product_id = ?", [$product]);
        $currentStock = intval($productData[0]['stock_quantity']);
        $newStock = $currentStock - $quantity;

        $db->setQuery("UPDATE products SET stock_quantity = ? WHERE product_id = ?", [$newStock, $product]);
        $db->setQuery("INSERT INTO inventory_movements (product_name, change_type, quantity, reason, customer_no) VALUES (?, ?, ?, ?, ?)", [
            $productName, "OUT", $quantity, "購入" , $_SESSION["customer_no"]
        ]);
        $db->setQuery("DELETE FROM cart WHERE crt_id = ?", [$crt_id]);
    }

    $templateFile = "cart/complete.twig"; // 購入完了ページにする
}

$table = "member";
$column = "family_name, first_name, zip1, zip2, address, tel1, tel2, tel3, email";
$where = "member_account = ? AND delete_flg = ?";
$arrVal = [$_SESSION['customer_no'], 0];
$customer = $db->select($table, $column, $where, $arrVal);

$context = [
    "dataArr" => $dataArr,
    "creditErrArr" => $creditErrArr,
    "productErrArr" => $productErrArr,
    "customer" => $customer[0],  // ここでcustomerを渡す
    "customer_no" => $_SESSION["customer_no"] ?? ""
];

$template = $twig->load($templateFile);
$template->display($context);
