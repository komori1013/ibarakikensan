<?php
namespace ibarakikensan\controller\user\auth;

require_once dirname(__FILE__)."/../../../common/Bootstrap.php";

use ibarakikensan\common\Bootstrap;
use ibarakikensan\model\PDODatabase;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$db = new PDODatabase(
    Bootstrap::DB_HOST,
    Bootstrap::DB_USER,
    Bootstrap::DB_PASS,
    Bootstrap::DB_NAME,
    Bootstrap::DB_PORT,
    Bootstrap::DB_TYPE
);

// フォームから送信されたメールアドレスを取得
$email = $_POST["email"] ?? null;

if ($email) {
    $sql = "SELECT * FROM member WHERE email = ? AND delete_flg = 0";
    $arrVal = [$email];
    $member = $db->setQuery($sql, $arrVal);
    if (count($member) > 0) {
        // トークンと有効期限を設定
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+450 minutes'));
        
        // メンバーのreset_tokenとtoken_expireを更新
        $updateSql = "UPDATE member SET reset_token = ?, token_expire = ? WHERE email = ?";
        $db->setQuery($updateSql, [$token, $expires, $email]);

        $resetUrl = "http://localhost/ibarakikensan/controller/user/auth/passwordReset.php?token=" . $token;

        // リセットURLを表示
        echo "パスワード再設定のリンクは以下の通りです:<br>";
        echo "<a href='" . $resetUrl . "'>パスワード再設定</a>";
    } else {
        echo "一致するユーザーがいませんでした";
    }
} else {
    echo "メールアドレスが入力されていません";
}
?>
