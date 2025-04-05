<?php
namespace ibarakikensan\model\administer;

class Item
{
  private $db;
  public $inOutArr = [
    "0" => "IN",
    "1" => "OUT"
  ];
  public $reasonArr = [
    "0" => "通常",
    "1" => "在庫調整"
  ];
  public $ctgArr = [
    "0" => "食品",
    "1" => "農産物",
    "2" => "飲料"
  ];

  public function __construct($db)
  {
    $this->db = $db;
  }

  public function processInventory($dataArr,$customer_no)
  {
    $product = $dataArr['product'];
    $price = (int)$dataArr['price'];
    $image = $dataArr['image'];
    $detail = $dataArr['detail'];
    $quantity = (int)$dataArr['quantity'];
    $reason = $this->reasonArr[$dataArr['reason']] ?? "不明";
    $inOut = $this->inOutArr[$dataArr['inOut']] ?? "不明";
    $category = $dataArr['category'];
    $customer_no = $customer_no;

    $productData = $this->db->select("products", "product_id, stock_quantity", "product_name = ?", [$product]);

    $currentStock = !empty($productData) ? (int)$productData[0]['stock_quantity'] : 0;
    $product_id = !empty($productData) ? $productData[0]['product_id'] : null;

    if($dataArr['inOut'] == "0") {
      $newStock = $currentStock + $quantity;
      
      if ($product_id) {
        $updateQuery = "UPDATE products SET stock_quantity = ? WHERE product_id = ?";
        $this->db->setQuery($updateQuery, [$newStock,$product_id]);
      }else{
        $insertQuery = "INSERT INTO products (product_name, stock_quantity, ctg_id, price, detail, image) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->setQuery($insertQuery, [$product, $newStock, $category, $price, $detail, $image]);
      }
      
      $historyQuery = "INSERT INTO inventory_movements (product_name, change_type, quantity, reason, customer_no) VALUES (?, ?, ?, ?, ?)";
      $this->db->setQuery($historyQuery, [$product, $inOut, $quantity, $reason, $customer_no]);
    
    }else{ 
        $newStock = $currentStock - $quantity;
        $updateQuery = "UPDATE products SET stock_quantity = ? WHERE product_name = ? AND ctg_id = ?";
        $this->db->setQuery($updateQuery, [$newStock, $product, $category]);

        $historyQuery = "INSERT INTO inventory_movements (product_name, change_type, quantity, reason, customer_no) VALUES (?, ?, ?, ?, ?)";
        $this->db->setQuery($historyQuery, [$product, $inOut, $quantity, $reason, $customer_no]);
    }
  }

  public function getItemList()
  {
    $table = "products p left join category c on p.ctg_id = c.ctg_id";
    $col = "p.product_name, p.stock_quantity, c.category_name, p.price, p.image,p.detail";
    $where = "";  
    $arrVal = [];
    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0)? $res: false;
  }

  

  public function getInOutData($start_date = null, $end_date = null)
{
    // エイリアス i を追加！
    $table = "inventory_movements i";
    $col = "i.created_at, i.customer_no, i.change_type, i.reason, i.product_name, i.quantity";
    $where = "";
    $arrVal = [];

    if (!is_null($start_date) && !is_null($end_date)) {
        $start_date .= ' 00:00:00';
        $end_date .= ' 23:59:59';
        $where = "i.created_at BETWEEN ? AND ?";
        array_push($arrVal, $start_date, $end_date);
    }

    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
}

  public function gettallyData($start_date = null, $end_date = null)
{
    $table = "inventory_movements i LEFT JOIN products p ON i.product_name = p.product_name";
    $col = "i.created_at, i.customer_no, i.change_type, i.reason, i.product_name, i.quantity, p.price";

    $where = "i.change_type = ?";
    $arrVal = ["OUT"];

    // 日付が指定されていればWHERE句に追加
    if (!is_null($start_date) && !is_null($end_date)) {
        $start_date .= ' 00:00:00';
        $end_date .= ' 23:59:59';
        $where = "i.created_at BETWEEN ? AND ? AND " . $where;
        array_unshift($arrVal, $start_date, $end_date); // 先頭に日付を追加
    }

    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0) ? $res : false;
}


}