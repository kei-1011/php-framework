<?php
class DbManager {


  protected $connections = array();   // PDOクラスのインスタンスを配列で保持
  protected $repository_connection_map = array(); // テーブルごとのRepositoryk流明日と接続名の対応を格納
  protected $repositories = array();  // Repositoryクラスのインスタンスを管理
  /*
  db接続を行うメソッド
  $name 接続を特定するための名前 ($connectionsプロパティのキー)
  $params PDOクラスのコンストラクタに渡す
  */
  public function connect($name, $params) {

    $params = array_merge(array(
      'dsn'       =>  null,
      'user'      =>  '',
      'password'  =>  '',
      'options'   =>  array(),
    ), $params);

    // PDOのインスタンスを作成
    $con = new PDO(
      $params['dsn'],
      $params['user'],
      $params['password'],
      $params['options']
    );

    // エラーが起きた時に例外を発生させる
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $this->connections[$name] = $con;
  }

  /*
  connectメソッドで接続したコネクションを取得する
  名前の指定がされなかった場合、currentを使って取得
  指定がなければ最初に作成したPDOクラスのインスタンスが返る
  */
  public function getConnection($name = null) {

    if(is_null($name)) {
      return current($this->connections);
    } else {
      return $this->connections[$name];
    }
  }

  public function setRepositoryConnectionMap($repository_name, $name) {

    $this->repository_connection_map[$repository_name] = $name;

  }

  //
  public function getConnectionForRepository($repository_name) {

    if(isset($this->repository_connection_map[$repository_name])) {

      $name = $this->repository_connection_map[$repository_name];
      $con  = $this->getConnection($name);
    } else {

      $con = $this->getConnection();
    }

    return $con;
  }

  //インスタンスを生成,格納処理
  public function get($repository_name) {
    if(!isset($this->repositories[$repository_name])) {
      $repository_class = $repository_name . 'Repository';        // クラス名を指定
      $con = $this->getConnectionForRepository($repository_name); // コネクションを取得

      $repository = new $repository_class($con);  // インスタンスを作成

      $this->repositories[$repository_name] = $repository;
    }

    return $this->repositories[$repository_name];
  }


  // データベースとの接続を開放する処理 __destruct　→　インスタンスが破棄された時に自動的に呼び出される
  public function __destruct() {

    foreach($this->repositories as $repository) {
      unset($repository);
    }

    foreach($this->connections as $con) {
      unset($con);
    }
  }

}
