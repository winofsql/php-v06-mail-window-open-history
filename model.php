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
// メール送信
// ***************************
function db_mail(){

    global $server,$user,$dbname,$password,$message;

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

    // テキストの改行を整備
    $_POST["text"] = str_replace("\r\n", "\n", $_POST["text"]);
    $_POST["text"] = str_replace("\n", "\r\n", $_POST["text"]);

    // insert 用 SQL
    $query = sql_get("sql/insert.sql");

    // ***************************
    // SQL の実行準備
    // ***************************
    $stmt = $mysqli->prepare($query);
    if ( $stmt ) {
        // 入力データの埋め込み( 5つ )
        $stmt->bind_param("sssss", $_POST["to"], $_POST["subject"], $_POST["text"], $_POST["date1"], $_POST["date2"]);
        $stmt->execute();
    }
    else {
        // テーブルが無いので作成( 初回のみ実行される一連の処理 )
        $create = sql_get("sql/create.sql");
        $mysqli->query($create);

        // 再度 insert を実行
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sssss", $_POST["to"], $_POST["subject"], $_POST["text"], $_POST["date1"], $_POST["date2"]);
        $stmt->execute();
    }

    // *******************************************
    // メール送信の準備
    // *******************************************
    $mail_address = "a@b.c.jp";
    // 差出人の名前は自由に変更可能
    $from_header = "From: " . mb_encode_mimeheader( mb_convert_encoding("差出人の名前","iso-2022-jp") );
    // メールアドレスは偽装可能。但し迷惑メールになる可能性は高い
    $from_header .= " <{$mail_address}>";

    // 日付フィールドの内容を本文へ簡易フォーマットで追加
    $_POST["text"] = $_POST["text"] . "\r\n--------------\r\n" . $_POST["date1"]  . "\r\n" . $_POST["date2"];

    $body = $_POST["text"];

    // メールを添付する処理
    if ( isset( $_FILES["file"] ) ) {
        if ( $_FILES["file"]["error"] == 0 ) {

            // 外部テキスト内埋め込み用
            $GLOBALS['uniqid'] = uniqid();

            $from_header .= "\n";
            $from_header .= "Content-Type: multipart/mixed; boundary=\"{$GLOBALS['uniqid']}\"\n";

            // 外部テキスト内埋め込み用
            $GLOBALS['mime'] = $_FILES['file']['type'];
            $GLOBALS['fname'] = $_FILES['file']['name'];

            // アップロードされたファイル
            $path = $_FILES["file"]["tmp_name"];
            $data = file_get_contents($path);
            $encode = base64_encode($data);

            // 外部テキスト内埋め込み用
            $GLOBALS['base64'] = chunk_split($encode);

            // 外部テキスト内変数埋め込み結果取得
            $body = document_get("MAIL_DATA");
        }
    }	

    // *******************************************
    // メール内容のデバッグ用出力
    // *******************************************
    file_put_contents("mail.txt", $_POST["to"] . "\n" . $_POST["subject"] . "\n" . $body . "\n" . $_POST["date1"]  . "\n" . $_POST["date2"] );

    // *******************************************
    // メール送信
    // *******************************************
    $result = mb_send_mail($_POST["to"], $_POST["subject"], $body, $from_header);

    // *******************************************
    // 新しい投稿用のクラス作成
    // *******************************************
    $json = new stdClass;
    
    $json->to = $_POST["to"];
    $json->subject = $_POST["subject"];
    $json->text = $_POST["text"];
    $json->status = $result;

    // *******************************************
    // ブラウザに返す
    // *******************************************
    print json_encode( $json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
}

// **************************
// デバック
// **************************
function debug_print(){

    print "<pre class=\"m-5\">";
    print_r( $_GET );
    print_r( $_POST );
    print_r( $_SESSION );
    print_r( $_FILES );
    print "</pre>";

}
