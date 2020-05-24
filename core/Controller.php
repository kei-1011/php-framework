<?php

/*
アプリケーションごとに子クラスを作成してアクションを定義する
そのため、当クラスは抽象クラスとして定義する
*/

abstract class Controller {

  protected $controller_name;
  protected $action_name;
  protected $application;
  protected $request;
  protected $response;
  protected $session;
  protected $db_manager;

  public function __construct() {
    // コントローラ名を取得
    $this->controller_name = strtolower(substr(get_class($this), 0, -10));

    $this->application = $application;
    $this->request     = $application->getRequest();
    $this->response    = $application->getResponse();
    $this->session     = $application->getSession();
    $this->db_manager  = $application->getDbManager();
  }


  // アクションの実行
  public function run($action, $params = array()) {

    $this->action_name = $action;

    $action_method = $action . 'Action';        // アクション名の定義「アクション名＋Action」
    if(!method_exists($this, $action_method)) { //メソッドが存在するかチェック

      //存在しない場合は404へ
      $this->foward404();
    }

    //アクションの実行
    $content = $this->$action_method($params);

    return $content;
  }

  /*
  viewファイルの読み込み処理をラッピング
  */
  public function render($variables = array(), $template = null, $layout = 'layout') {

    // デフォルト値の設定
    $defaults = array(
      'request'   => $this->request,
      'base_url'  => $this->request->getBaseUrl(),
      'session'   => $this->session,
    );

    // viewクラスのインスタンス化
    // viewsディレクトリへのパスはApplicationクラスのgetViewDir()メソッドで取得できる
    $view = new View($this->application->getViewDir(), $defaults);

    // テンプレート名の指定　（第２引数）
    if(is_null($template)) {
      $template = $this->action_name; // nullの場合はアクション名をファイル名として返す
    }

    // コントローラ名をテンプレート名の先頭に付与
    $path = $this->controller_name . '/' . $template;

    // viewクラスのrenderメソッドを実行し、ビューファイルを読み込む
    return $view->render($path, $variables, $layout);
  }

  // HttpNotFoundExceptionを通知、404エラー画面に遷移する
  protected function forward404() {

    throw new HttpNotFoundException('Fowarded 40 page from' . $this->controller_name . '/' . $this->action_name);

  }

  /*
  URLを引数として受け取り、Responseオブジェクトにリダイレクトする
  */
  protected function redirect($url) {

    if(!preg_match('#https?://#', $url)) {
      $protocol = $this->request->isSsl() ? 'https://' : 'http://';
      $host = $this->request->getHost();
      $base_url = $this->request->getBaseUrl();

      $url = $protocol . $host . $base_url . $url;
    }

    // ステータスコードを302に設定し、リダイレクトさせる
    $this->response->setStatusCode(302,'Found');
    $this->response->setHttpHeader('Location', $url);
  }

  //トークン生成
  protected function generateCsrfToken($form_name) {
    $key = 'csrf_tokens/' . $form_name;
    $tokens = $this->session->get($key, array());

    // 最大10個のトークンを保持できる。
    // 10個保持している場合は、array_shiftで古いものから削除する。
    if(count($tokens) >= 10) {
      array_shift($tokens); //  配列の先頭から一つ取り出す
    }

    // 文字列のハッシュ化
    $token = sha1($form_name . session_id() . microtime());
    $tokens[] = $token;

    // セッションに格納してトークンを返す
    $this->session->set($key, $tokens);

    return $token;
  }

  // リクエストされてきたトークンとセッションに格納されたトークンを比較した結果を返す
  protected function checkCsrfToken($form_name, $token) {
    $key = 'csrf_tokens/' . $form_name;
    $tokens = $this->session->get($key, array());

    if(false !== ($pos = array_search($token, $tokens, true))) {  // tokensの中身を調べる(sessionにトークンが格納されているか)

      unset($tokens[$pos]); //  変数を破棄
      $this->session->set($key, $tokens);

      return true;
    }

    return false;
  }

}
