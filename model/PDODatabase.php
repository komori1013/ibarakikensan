<?php
// POD(PHP Data Objects)::PHP標準のデータベース接続クラス)
// おすすめ記事: http://qiita.com/7968/items/6f089fec8dde676abb5b
namespace ibarakikensan\model;

class PDODatabase
{
  private $dbh = null;
  private $db_host = "";
  private $db_user = "";
  private $db_pass = "";
  private $db_name = "";
  private $db_port = "";
  private $db_type = "";

  //オプション
  private $order = "";
  private $limit = "";
  private $offset = "";
  private $groupby = "";


  public function __construct($db_host, $db_user, $db_pass, $db_name, $db_port, $db_type)
  {
   
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_port, $db_type);
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    $this->db_port = $db_port;
   
    //SQL関連
    $this->order="";
    $this->limit="";
    $this->offset="";
    $this->groupby="";
  }


  private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_port, $db_type)
  {
    try { // 接続エラー発生→PDOExceptionオブジェクトがスローされる→例外処理をキャッチする
      switch ($db_type) {
        case "mysql":
          $dsn = "mysql:host=". $db_host. ";port=". $db_port. ";dbname=". $db_name. ";charset=utf8";
          $dbh = new \PDO($dsn, $db_user, $db_pass);
          $dbh->query('SET NAMES utf8');
          break;

          //dsn = Data Source Name
          //dbh = Database Handle

        case "pgsql":
          $dsn = "pgsql:dbname=". $db_name. "host=". $db_host. "port=5432";
          $dbh = new \PDO($dsn, $db_user, $db_pass);
          break;
      }
    } catch (\PDOException $e) {
      var_dump($e->getMessage());
      exit();
    }
    return $dbh;
  }


  public function setQuery($query = "", $arrVal = [])
  {
    $stmt = $this->dbh->prepare($query);
    $stmt->execute($arrVal);
    if (stripos($query, 'SELECT') === 0) {
      return $stmt->fetchAll(\PDO::FETCH_ASSOC); // 結果を配列で返す
  }

  }


  public function select($table, $column = "", $where = "", $arrVal = [])
  {
    // echo "<pre>";
    // var_dump($table);
    // var_dump($column);
    // var_dump($where);
    // var_dump($arrVal);
    
    $sql = $this->getSql("select", $table, $where, $column);

    $this->sqlLogInfo($sql, $arrVal);

    $stmt = $this->dbh->prepare($sql); //where id = ?の状態でプリペアードステートメントを作成
    // stmt = statement 状態
    // echo "<pre>";
    // var_dump($stmt);
    $res = $stmt->execute($arrVal);

    if($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    //データを連想配列に格納;
    $data = [];
    while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
      array_push($data, $result);
    }
    return $data;
  }
  

  public function count($table, $where = "", $arrVal = [])
  {
    $sql = $this->getSql("count", $table, $where);

    $this->sqlLogInfo($sql, $arrVal);
    $stmt = $this->dbh->prepare($sql);

    $res = $stmt->execute($arrVal);

    if($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    return intval($result['NUM']);
  }


  public function setOrder($order="")
  {
    if ($order !== "") {
      $this->order = " ORDER BY ". $order;
    }
  }


  public function setLimitOff($limit="", $offset="")
  {
    if ($limit !== "") {
      $this->limit = " LIMIT ". $limit;
    }
    if ($offset !== "") {
      $this->offset = " OFFSET ". $offset;
    }
  }


  public function setGroupBy($groupby)
  {
    if ($groupby !== "") {
      $this->groupby = " GROUP BY ". $groupby;
    }
  }


  private function getSql($type, $table, $where = "", $column = "")
  {
    switch ($type) {
      case "select":
        $columnKey = ($column !== "") ? $column : "*";
        break;

      case "count":
        $columnKey = "COUNT(*) AS NUM";
        break;

      default:
        break;
    }
    
    $whereSQL = ($where !== "") ? " WHERE ". $where : "";
    $other = $this->groupby. " ". $this->order. " ". $this->limit. " ".$this->offset;

    //sql文の作成
    $sql = "SELECT ". $columnKey. " FROM ". $table. $whereSQL. $other;
    // var_dump($sql);
    return $sql;
  }


  public function insert($table, $insData = [])
  {
    $insDataKey = [];
    $insDataVal = [];
    $preCnt = [];

    $columns = "";
    $preSt = "";

    foreach ($insData as $col => $val) {
      $insDataKey[] = $col;
      $insDataVal[] = $val;
      $preCnt[] = "?";
    }

    $columns = implode(",", $insDataKey); //配列の要素を連結
    $preSt = implode(",", $preCnt); //配列の要素を連結

    $sql = "INSERT INTO "
      .$table
      ."("
      .$columns
      .") VALUES ("
      .$preSt
      .")";

    $this->sqlLogInfo($sql, $insDataVal);

    $stmt = $this->dbh->prepare($sql); //sql文をプリペアードステートメントにセット
    $res = $stmt->execute($insDataVal);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }

    return $res;
  }


  public function update($table, $insData = [], $where="", $arrWhereVal = [])
  {
    $arrPreSt = [];
    foreach ($insData as $col => $val) {
      $arrPreSt[] = $col. "=?";
    }
    $preSt = implode(",", $arrPreSt);

  //sql文の作成
  $sql = "UPDATE "
    .$table
    ." SET "
    .$preSt
    ." WHERE "
    .$where;
  
    $updateData = array_merge(array_values($insData), $arrWhereVal); //[[delete_flg]=> "1"]
    $this->sqlLogInfo($sql, $updateData);

    $stmt = $this->dbh->prepare($sql);
    $res = $stmt->execute($updateData);

    if ($res === false) {
      $this->catchError($stmt->errorInfo());
    }
    return $res;
  }


  public function getLastId()
  {
    return $this->dbh->lastInsertId();
  }


  private function catchError($errArr = [])
  {
    $errMsg = (!empty($errArr[2])) ? $errArr[2] : "";
    die("SQLエラーが発生しました。". $errArr[2]);
  }


  private function makeLogFile()
  {
    $logDir = dirname(__DIR__). "/logs";
    if (!file_exists($logDir)) {
      mkdir($logDir, 0777); // 0777: ファイルの読み書き権限を全てのユーザーに与える
    }
    $logPath = $logDir. "/shopping.log";
    if (!file_exists($logPath)) {
      touch($logPath);
    }
    return $logPath;
  }


  private function sqlLogInfo($str, $arrVal = [])
  {
    $logPath = $this->makeLogFile();
    $logData = sprintf("[SQL_LOG: %s]: %s [%s]\n", date("Y-m-d H:i:s"), $str, implode(",", $arrVal)); //%s: 文字列, %d: 整数
    error_log($logData, 3, $logPath);
  }
}