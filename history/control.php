<?php
require_once("setting.php");

header( "Content-Type: text/html; charset=utf-8" );

// 固有の処理
require_once("model.php");

$table_data = "";

// **************************
// 一覧作成
// **************************
build_table_view();

// **************************
// 画面定義
// **************************
require_once("view.php");

//debug_print();
?>
