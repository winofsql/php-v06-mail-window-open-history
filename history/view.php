<!DOCTYPE html>
<html>

<head>
<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
<meta charset="utf-8">
<title>メール送信の送信履歴</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.0.1/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<style>
<?php require_once("client.css") ?>
</style>
<script>
<?php require_once("client.js") ?>
</script>

</head>

<body>
<h3 class="alert alert-primary"><a href="control.php">送信履歴</a></h3>
<div id="content" class="table-responsive">

    <table class="table table-hover table-striped">
        <tbody id="tbl">
            <th>宛先</th>
            <th>タイトル</th>
            <th>本文</th>
            <?= $table_data ?>
        </tbody>
    </table>

</div>
</body>
</html>
