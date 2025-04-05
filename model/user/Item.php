<?php
namespace ibarakikensan\model\user;

class Item 
{
  public $cateArr = [];
  public $db = null;
  public function __construct($db)
  {
    $this->db = $db;
  }

  public function getCategoryList()
  {
    $table = "category";
    $col = "ctg_id, category_name";
    $res = $this->db->select($table, $col);
    return $res;
  }

  public function getItemList($ctg_id)
  {
    $table = "products";
    $col = "product_id, product_name, price, image";
    $where = ($ctg_id !== "")? "ctg_id = ?": "";
    $arrVal = ($ctg_id !== "")? [$ctg_id]: [];
    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0)? $res: false;
  }

  public function sortItemList($searchKeyword)
  {
    $table = "products";
    $col = "product_id,product_name, price, image";
    $where = "product_name LIKE ?";  
    $arrVal = ["%{$searchKeyword}%"];
    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0)? $res: false;
  }

  public function getItemDetailData($product_id)
  {
    $table = "products";
    $col = "product_id,product_name, price, image, detail";
    $where = ($product_id !== "")? "product_id = ?": "";
    $arrVal = ($product_id !== "")? [$product_id]: [];
    $res = $this->db->select($table, $col, $where, $arrVal);
    return ($res !== false && count($res) !== 0)? $res: false;
  }
  

}
