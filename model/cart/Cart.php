<?php
namespace ibarakikensan\model\cart;

class Cart
{
  private $db = null;

  public function __construct($db = null)
  {
    $this->db = $db;
  }

  public function insCartData($customer_no, $item_id, $quantity, $total_price): bool
  {
    $table = "cart";
    $column = "crt_id, customer_no, item_id, num, delete_flg, totalprice";
    $where = "customer_no = ? AND item_id = ? AND delete_flg = ?";
    $arrVal = [$customer_no, $item_id, 0];

    $result = $this->db->select($table, $column, $where, $arrVal);
  
    if (!empty($result)) {
      $crt_id = $result[0]["crt_id"];
      $new_num = $result[0]["num"] + $quantity;
      $new_total_price = $result[0]["totalprice"] + $total_price;
      $where = "crt_id = ?";
      $arrWhereVal = [$crt_id];
      $insData = ["num" => $new_num, "totalprice" => $new_total_price];
      return $this->db->update($table, $insData, $where, $arrWhereVal);

    } else {
      $insData = [
        "customer_no" => $customer_no,
        "item_id" => $item_id,
        "num" => $quantity,
        "totalprice" => $total_price
      ];
      return $this->db->insert($table, $insData);
    }
  }

  public function getCartData($customer_no)
  {
    $table = "cart c LEFT JOIN products i ON c.item_id = i.product_id";
    $column = "c.crt_id, i.product_id, i.product_name, i.price, c.num, c.totalprice";
    $where = "c.customer_no = ? AND c.delete_flg = ?";
    $arrVal = [$customer_no, 0];

    return $this->db->select($table, $column, $where, $arrVal);
  }

  public function delCartData($crt_id)
  {
    $table = "cart";
    $insData = ["delete_flg" => 1];
    $where = "crt_id = ?";
    $arrWhereVal = [$crt_id];

    return $this->db->update($table, $insData, $where, $arrWhereVal);
  }

  public function getItemAndSumPrice($customer_no)
  {
    $table = "cart c LEFT JOIN products i ON c.item_id = i.product_id";
    $column = "SUM(c.totalprice) AS totalPrice";
    $where = "c.customer_no = ? AND c.delete_flg = ?";
    $arrWhereVal = [$customer_no, 0];

    $res = $this->db->select($table, $column, $where, $arrWhereVal);
    $price = ($res !== false && count($res) !== 0) ? $res[0]["totalPrice"] : 0;

    $table = "cart c";
    $column = "SUM(num) AS num";
    $res = $this->db->select($table, $column, $where, $arrWhereVal);
    $num = ($res !== false && count($res) !== 0) ? $res[0]["num"] : 0;
    return [$num, $price];
  }

  public function updateCartData($crt_id, $item_id, $quantity, $price){
    $table = "cart";
    $where = "crt_id = ?";
    $new_total_price = $quantity * $price;
    $insData = ["item_id" => $item_id, "num" => $quantity, "totalprice" => $new_total_price];
    $arrWhereVal = [$crt_id];

    return $this->db->update($table, $insData, $where, $arrWhereVal);
  }

  public function exportCsv($filename, $data)
  {
    // **Shift-JISエンコーディングを明示**
    header('Content-Type: text/csv; charset=Shift_JIS');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // **出力ストリームを開く**
    $output = fopen('php://output', 'w');

    // **BOMなし（Excelが認識しやすい）**
    // fwrite($output, pack('C*', 0xEF, 0xBB, 0xBF)); // これはコメントアウト

    // **ヘッダーをShift-JISへ変換**
    $header = ['日時', '顧客番号', '種別', '理由', '商品名', '数量', '単価', '合計'];
    fputcsv($output, array_map(fn($v) => mb_convert_encoding($v, 'SJIS-WIN', 'UTF-8'), $header));

    // **データ行**
    foreach ($data as $row) {
      fputcsv($output, array_map(fn($v) => mb_convert_encoding((string)$v, 'SJIS-WIN', 'UTF-8'), [
        $row['created_at'],
        $row['customer_no'],
        $row['change_type'],
        $row['reason'],
        $row['product_name'],
        $row['quantity'],
        $row['price'],
        $row['subtotal']
      ]));
    }

    fclose($output);
    exit;
  }

}