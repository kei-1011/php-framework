<?php
/*
フレームワークの中心
アプリケーションの処理を行う。

クラスのオブジェクト管理
ルーティングの定義
コントローラの実行
レスポンスの送信

*/

abstract class Application {

  protected $debug = false;
  protected $request;
  protected $response;
  protected $session;
  protected $db_manager;
  protected $login_action;

  public function __construct($debug = false) {

    $this->setDebugMode($debug);
    $this->initialize();
    $this->configure();
  }

  // デバッグモードに応じてエラー表示処理を変える
  protected function setDebugMode($debug) {
    if($debug) {
      $this->debug = true;
      ini_set('display_errors',1);
      error_reporting(-1);
    } else {
      $this->debug = false;
      ini_set('display_errors',0);
    }
  }

  // クラスの初期化処理
  protected function initialize() {
    $this->request = new Request();
    $this->response = new Response();
    $this->session = new Session();
    $this->db_manager = new DbManager();
    $this->router = new Router($this->registerRoutes());
  }

  protected function configure();

  // 以下情報取得用のメソッド
  abstract public function getRootDir();

  abstract public function registerRoutes();

  public function isDebugMode() {
    return $this->debug;
  }

  public function getRequest() {
    return $this->request;
  }

  public function getResponse() {
    return $this->response;
  }

  public function getSession() {
    return $this->session;
  }

  public function getDbManager() {
    return $this->db_manager;
  }

  public function getControllerDir() {
    return $this->getRootDir() / '/controllers';
  }

  public function getViewDir() {
    return $this->getRootDir() / '/views';
  }

  public function getModelDir() {
    return $this->getRootDir() / '/models';
  }

  public function getWebDir() {
    return $this->getRootDir() / '/web';
  }


  // コントローラ呼び出しと実行
  /*
  Routerクラスのresolve()メソッドを呼び出して、ルーティングパラメータを取得、コントローラ名とアクション名を指定する。
  これらの値を元に、runAction()メソッドを呼び出してアクションを実行する。
  */
  public function run() {
    try {
      $params = $this->router->resolve($this->request->getPathInfo());
      if($params === false) {
        // 例外処理
        throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
      }
      $controller = $params['controller'];
      $action = $params['action'];
      $this->runAction($controller,$action,$params);

    } catch (HttpNotFoundException $e) {
      // 例外が発生した場合、40エラー画面を表示
      $this->render404page($e);

    } catch (UnauthorizedActionException $e) {
      list($controller, $action) = $this->login_action;
      $this->runAction($controller, $action);
    }
  }

  /*　実際にアクションを実行する
  コントローラのクラス名はコントローラ名にControllerをつけるルールとし、
  ルーティングにはコントローラ名の先頭を小文字で指定しているので、unfirstで先頭を大文字にする。
  */
  public function runAction($controller_name, $action, $params = array()) {

    $controller_class = ucfirst($controller_name) . 'Controller';

    $controller = $this->findController($controller_class);
    if($controller === false) {

      // 例外処理
      throw new HttpNotFoundException($controller_class . 'controller class is not found.');
    }

    $content = $controller->run($action, $params);

    $this->response->setContent($content);
  }

  /*
  コントローラクラスが読み込まれていない場合、クラスファイルの読み込みを行う。
  */
  protected function findController($controller_class) {
    if(!class_exists($controller_class)) {
      $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';

      if(!is_readable($controller_file)) {
        return false;
      } else {
        require_once $controller_file;

        if(!class_exists($controller_class)) {
          return false;
        }
      }
    }

    return new $controller_class($this);
  }


  // 404ページのレンダリング
  protected function render404page($e) {

    $this->response->setStatusCode(404, 'Not Found');
    $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found';
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    $this->response->setContent(<<<EOF
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>404 not found</title>
    </head>
    <body>
      {$message}
    </body>
    </html>
  EOF
    );
  }


}
