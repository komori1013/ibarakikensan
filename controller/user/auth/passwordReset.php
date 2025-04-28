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

// URLパラメータからトークンを取得
$token = $_GET['token'] ?? null;


if ($token) {
    // トークンが有効か確認
    $sql = "SELECT * FROM member WHERE reset_token = ? AND token_expire > NOW()";
    $arrVal = [$token];
    $member = $db->setQuery($sql, $arrVal);

    if (count($member) > 0) {
        // トークンが有効ならパスワードリセットフォームを表示
        echo '<form action="updatePassword.php" method="POST">';
        echo '<input type="hidden" name="token" value="' . $token . '">';
        echo '<label for="new_password">新しいパスワード:</label><br>';
        echo '<input type="password" id="new_password" name="new_password" required>';
        echo '<input type="submit" value="パスワードをリセット">';
        echo '</form>';
    } else {
        echo "無効なリンクです。再度お試しください。";
    }
} else {
    echo "トークンが指定されていません。";
}
?>
