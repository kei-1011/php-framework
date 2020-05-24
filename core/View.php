<?php

/*
ビューファイルの読み込み、ビューファイルに渡す変数の制御

アウトプットバッファリングという仕組みを使って、出力を文字列として読み込む。
また、requireでファイルを読み込むと、requireを実行した側でアクセス可能な変数に対し読み込まれた側のファイルでもアクセうすることができる。

この仕組みを使って、ビューファイルでも変数を参照できるようにする。

アクション内で利用している変数全てを読み込むようにすると、ビューファイル内でどのような値を出力されているのかがわかりにくく、管理がしにくい。
ビューファイルに必要な変数のみを明示的に指定できる仕組みを作る。
*/


class View {

  protected $base_dir;
  protected $defaults;
  protected $layout_variables = array();

  public function __construct($base_dir, $defaults = array()) {

    $this->base_dir = $base_dir;
    $this->defaults = $defaults;

  }

  public function setLayoutVar($name, $value) {
    $this->layout_variables[$name] = $value;
  }

  public function render($_path, $_variables = array(), $_layout = false) {

    $_file = $this->base_dir . '/' . $_path . '.php';

    extract(array_merge($this->defaults, $_variables));

    ob_start();
    ob_implicit_flush(0);

    require $_file;

    $content = ob_get_clean();

    if($_layout) {

      $content = $this->render($_layout,
      array_merge($this->layout_variables, array(
        '_content' => $content,
      )
      ));
    }

    return $content;
  }

  public function escape($string) {
    return htmlspecialchars($string, ENT_QUOTES,'UTF-8');
  }

  }
