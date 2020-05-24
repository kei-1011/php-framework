# php-framework

## ファイル構成

```

application/
  bootstrap.php
  controllers/
  core/
    Application.php
    ClassLoader.php
    Controller.php
    DbManager.php
    DbRepository.php
    HttpNotFoundException.php
    Request.php
    Response.php
    Router.php
    Session.php
    UnauthorizedActionException.php
    View.php
  models/
  views/
  web//
    .htaccess
    .index.php

```


## フレームワークを使用した開発の流れ

### Applicationクラス
- ルートディレクトリの指定
- アクションに合わせたルーティング定義
- 接続するデータベースの指定
- ログインアクションの指定

### index.php
- Applicationの呼び出しと実行

### Controllerクラス
- 子クラスの作成
- 再生する画面に合わせてアクション定義、処理の実装
- ログインが必要なアクションの指定

### DbRepositoryクラス
- データベース 上のテーブルごとに子クラスの作成
- データベースアクセス処理の実装

### ビューファイル
- アクションに合わせたHTMLの記述
- レイアウトファイルの記述
