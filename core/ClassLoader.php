<?php
class ClassLoader {
  protected $dirs;

  //PHPにオートロードクラスを登録する
  public function register() {
    sql_autoload_register(array($this,'loadClass'));
  }

  //ディレクトリを登録する
  public function registerDir($dir) {
    $this->dirs[] = $dir;
  }

  //オートロード時に呼び出し、クラスファイルの読み込みを行う処理
  public function loadClass($class) {
    foreach($this->dirs as $dir) {
      $fire = $dir . '/' .$class . '.php';
      if(is_readable($fire)){
        require $fire;

        return;
      }
    }
  }
}
