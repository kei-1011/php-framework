<?php
class Router {
  protected $routes;
  public function __construct($definitions) {
    $this->routes = $this->compileRoutes($definitions);
  }

  /*受け取ったルーティング定義配列中の動的パラメータ指定を正規表現で扱える形に変換する
  */
  public function compileRoutes($definitions) {
    $routes = array();

    foreach($definitions as $url => $params) {
      $tokens = explode('/',ltrim($url,'/'));

      foreach($tokens as $i => $token) {
        if(0 === strpos($token,':')) {
          $name = substr($token,1);
          $token = '(?P<' . $name . '>[^/]+)';
        }
        $tokens[$i] = $token;
      }
      $pattern = '/' . implode('/',$tokens);
      $routes[$pattern] = $params;
    }
    return $routes;

  }
}
