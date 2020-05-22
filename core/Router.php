<?php
/*
ルーティング定義配列をコンストラクタのパラメータとして受け取り、
compileRoutes芽絵ソッドに渡して変換したものを$routesプロパティとして設定
*/
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
      // explede スラッシュごとに分割
      $tokens = explode('/',ltrim($url,'/'));

      foreach($tokens as $i => $token) {
        if(0 === strpos($token,':')) {    // :で始まる文字列があった場合、正規表現に変換する
          $name = substr($token,1);
          $token = '(?P<' . $name . '>[^/]+)';
        }
        $tokens[$i] = $token;
      }
      $pattern = '/' . implode('/',$tokens);  // 再度スラッシュでつなげる
      $routes[$pattern] = $params;
    }
    return $routes;

  }


  public function resolve($path_info) {
    // path_infoの先頭が/でない場合、先頭に/を付与
    if('/' !== substr($path_info, 0, 1)) {
      $path_info = '/' . $path_info;
    }

    //
    foreach($this->routes as $pattern => $params) {
      if(preg_match('#^' . $pattern . '$#', $path_info,$matches)) {

        // マッチしたら、　array_mergeで連結する
        $params = array_merge($params, $matches);

        return $params;
      }
    }

    return false;
  }
}
