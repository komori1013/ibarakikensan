<?php
require_once dirname(__FILE__)."/../../../common/Bootstrap.php";
use ibarakikensan\model\cart\Cart;

$data = isset($_POST['productArr']) ? json_decode($_POST['productArr'], true) : [];

if (!empty($data)) {
    $csv = new Cart();
    $csv->exportCsv("result.csv", $data);
} else {
   header("Location: http://localhost/ibarakikensan/controller/administer/index.php");
}
?>