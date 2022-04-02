<?php
// ***************************
// SQL 取得
// ***************************
function sql_get( $file_path ) {

    return file_get_contents( $file_path );

}
// ***************************
// SQL 取得
// ***************************
function document_get( $file_path ) {

    $view_text = file_get_contents( $file_path );

    // 展開処理
    $view_text = str_replace('"', '\\"', $view_text );
    // 上の処理は、以下の処理内で $view_text 内に " が存在するとまずいからです。
    eval("\$view_text = \"$view_text\";");

    // 変数が展開された文字列を戻す
    return $view_text;

}
// ***************************
// 履歴表示
// ***************************
function build_table_view(){

    global $server, $user, $dbname, $password, $table_data;

    // ***************************
    // DB 接続
    // ***************************
    $mysqli = @ new mysqli($server, $user, $password, $dbname);
    if ($mysqli->connect_error) {
        print "接続エラーです : ({$mysqli->connect_errno}) ({$mysqli->connect_error})";
        exit();
    }
    // ***************************
    // クライアントの文字セット
    // ***************************
    $mysqli->set_charset("utf8"); 

    $query = sql_get("../sql/select-history.sql");

    $stmt = $mysqli->prepare($query);
    if ( !$stmt ) {
        // データなし( エラー処理省略 )
        return;
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ( $GLOBALS['row'] = $result->fetch_array(MYSQLI_BOTH) ) {
        $table_data .= document_get("TABLE_DATA");
    }

}

// **************************
// デバック
// **************************
function debug_print(){
    print "<pre style='margin:20px;'>";
    print_r( $_GET );
    print_r( $_POST );
    print_r( $_SESSION );
    print "</pre>";
}
