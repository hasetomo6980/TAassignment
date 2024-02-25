<!--index.phpで投稿ボタンを押下した際の処理-->
<?php
//変数宣言
$id = null;
$name = $_POST["name"];
$title = $_POST["title"];
$contents = $_POST["contents"];
date_default_timezone_set('Asia/Tokyo');
$created_at = date("Y-m-d H:i:s");

try {
  //DB接続情報を設定します。
  $pdo = new PDO(
    "mysql:port=3309;dbname=taassignment;host=localhost",
    "root",
    "",
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`")
  );

  //SQLを実行。
  $regist = $pdo->prepare("INSERT INTO post(id, name,title, contents, created_at) VALUES (:id,:name,:title,:contents,:created_at)");
  $regist->bindParam(":id", $id);
  $regist->bindParam(":name", $name);
  $regist->bindParam(":title", $title);
  $regist->bindParam(":contents", $contents);
  $regist->bindParam(":created_at", $created_at);
  $regist->execute();

  $test1 = "成功";
  header('Location: index.php?post=' .$test1);
  exit();
} catch (Exception) {
  $test1 = "失敗";
  header('Location: index.php?post=' .$test1);
  exit();
}
?>