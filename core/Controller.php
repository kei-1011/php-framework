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
}
