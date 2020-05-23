<?php

class Session {

  protected static $sessionStarted = false;
  protected static $sessionIdRegenerated = false;

  // コンストラクタが実行されたタイミングでsession_startを実行
  public function __construct() {

    if(!self::$sessionStarted) {
      session_start();

      self::$sessionStarted = true;
    }
  }

  // session 設定
  public function set($name, $value) {
    $_SESSION[$name] = $value;
  }

  // session 取得
  public function get($name, $default = null) {

    if(isset($_SESSION[$name])) {

      return $_SESSION[$name];
    }

    return $default;
  }

  // session 削除
  public function remove($name) {
    unset($_SESSION[$name]);
  }

  //session 空にする
  public function clear() {
    $_SESSION = array();
  }


  // セッションIDを新しく発行するためのsession_regenerate_id関数を実行する
  public function regenerate($destroy = true) {

    if(!self::$sessionIdRegenerated) {
      session_regenerate_id($destroy);

      self::$sessionIdRegenerated = true;
    }
  }

/*
ログイン状態を制御するメソッド

_authenticatedというキーでログインしているかどうかのフラグを格納。

*/
  public function setAuthenticated($bool) {
    $this->set('_authenticated', (bool)$bool);

    $this->regenerate();
  }

  public function isAuthenticated() {
    return $this->get('_authenticated', false);
  }
}
