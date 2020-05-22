<?php
/*
ClassLoaderをオートロードに登録する

この段階では、ClassLoaderは読み込まれていないため、明示的にrequireを使って読み込む

ClassLoaderクラスのregisterDirメソッドを呼び出して、coreとmodelsディレクトリをオートロードの対象にし、registerメソッドでオートロードに登録する

*/
require 'core/ClassLoader.php';

$loader = new ClassLoader();
$loader->registerDir(Dirname(__FILE__).'/core');
$loader->registerDir(Dirname(__FILE__).'/models');
$loader->register();
