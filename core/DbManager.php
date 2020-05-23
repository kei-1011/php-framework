<?php
class DbManager {

  /*  DbManagerクラスの使い方 ------
  $db_manager = new DbManager();
  $db_manager->connect('master',array(
      'dsn'       =>  mysql:dbname;host=localhost,
      'user'      =>  'root',
      'password'  =>  'root',
  ));
  $db_manager->getConnection('master');
  $db_manager->getConnection();　→　masterが返ってくる

  */

  // PDOクラスのインスタンスを配列で保持
  protected $connections = array();

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
    }

    return $this->connections[$name];
  }
}
