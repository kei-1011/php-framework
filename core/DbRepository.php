<?php
/*
データベースへのアクセスを行う
DBのテーブルごとに子クラスを作成する。

SQLの実行時に頻繁に出てくる処理をDbRepositoryクラスに抽象化しておく。(abstract)
*/

abstract class DbRepository {

  protected $con;

  // DbManagerクラスからPDOクラスのインスタンスを受け取って内部に格納する処理
  public function __construct() {
    $this->setConnection($con);
  }
  public function setConnection($con) {
    $this->con = $con;
  }

  // よく使うSQL
  public function execute($sql, $params = array()) {
    $stmt = $this->con->prepare($sql);
    $stmt->execute($params);
    return $stmt;
  }

  //1行のみ
  public function fetch($sql, $params = array()) {
    return $this->execute($sql,$params)->fetch(PDO::FETCH_ASSOC);
  }

  // 全ての行
  public function fetchAll($sql,$params = array()) {
    return $this->execute($sql,$params)->fetchAll(PDO::FETCH_ASSOC);
  }
}
