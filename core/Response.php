<?php
/*
レスポンスを表す HTTPヘッダとHTMLなどのコンテンツを返す

HTTPヘッダの情報は全てResponseクラスで扱うようにする
*/

class Response {

  protected $content;
  protected $status_code = 200;
  protected $status_text = 'OK';
  protected $http_headers = array();


  /*
  プロパティに設定された値を元にレスポンスの送信を行う。
  */
  public function send() {
    // ステータスコードの指定　HTTP/1.1→httpプロトコルのバージョン
    header('HTTP/1.1 ' . $this->status_code . ' ' . $this->status_text);

    // http_headersプロパティにhttpレスポンスヘッダの指定があれば、header関数で送信
    foreach ($this->http_headers as $name => $value) {
      header($name . ': ' . $value);
    }

    // 出力
    echo $this->content;
  }

  /*
  HTMLなどの実際にクライアントに返す内容を格納。
  */
  public function setContent($content) {
    $this->content  = $content;
  }

  /*
  ステータスコードを格納  ex) 404, 500
  全てのステータスコードとテキストを保持するべきだが、分量が多くなるため、今回はテキストを指定するようにする。
  */
  public function setStatusCode($status_code,$status_text = '') {
    $this->status_code = $status_code;
    $this->status_text = $status_text;
  }

  /*
  HTTPヘッダを格納するプロパティ
  ヘッダの部分をキーに、ヘッダの内容を値にして連想配列型式で格納
  */
  public function setHttpHeader($name,$value) {
    $this->http_headers[$name] = $value;
  }

}
