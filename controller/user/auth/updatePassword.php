<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;

$db = new PDODatabase(
    Bootstrap::DB_HOST,
    Bootstrap::DB_USER,
    Bootstrap::DB_PASS,
    Bootstrap::DB_NAME,
    Bootstrap::DB_PORT,
    Bootstrap::DB_TYPE
);

// フォームから受け取ったトークンと新しいパスワード
$token = $_POST['token'] ?? null;
$newPassword = $_POST['new_password'] ?? null;

if ($token && $newPassword) {
    // トークンが有効か確認
    $sql = "SELECT * FROM member WHERE reset_token = ? AND token_expire > NOW()";
    $arrVal = [$token];
    $member = $db->setQuery($sql, $arrVal);

    if (count($member) > 0) {
        // パスワードをハッシュ化

        // パスワードとトークンを更新
        $updateSql = "UPDATE member SET member_password = ?, reset_token = NULL, token_expire = NULL WHERE reset_token = ?";
        $db->setQuery($updateSql, [$newPassword, $token]);

        echo "パスワードが再設定されました。ログインページに戻ってください。<br>";
        echo '<a href="http://localhost/ibarakikensan/controller/user/auth/login.php">ログインページへ戻る</a>';
    } else {
        echo "無効または期限切れのトークンです。";
    }
} else {
    echo "トークンまたはパスワードが未入力です。";
}
?>