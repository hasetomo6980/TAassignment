<!--index.phpで投稿削除ボタンを押下した際の処理-->
<?php
try {
  if (!empty($_GET['id'])) {

    //DB接続情報を設定します。
    $pdo = new PDO(
      "mysql:port=3309;dbname=taassignment;host=localhost",
      "root",
      "",
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`")
    );

    //SQLを実行。
    $posts = $pdo->prepare("SELECT * FROM post WHERE id=?");
    $posts->bindValue(1, $_GET['id'], PDO::PARAM_INT);
    $posts->execute();
    $post = $posts->fetch();
    //指定されたidがpostにあるか存在確認
    if ($post != false) {
      
      $delete = $pdo->prepare("DELETE FROM post WHERE id=?");
      $delete->bindValue(1, $_GET['id'], PDO::PARAM_INT);
      $delete->execute();

      $test2 = "成功";
      header('Location: index.php?delete=' . $test2.'&DelId='.$_GET['id']);
      exit();
    }
  }
  throw new Exception();
} catch (Exception) {
  $test2 = "失敗";
  header('Location: index.php?delete=' . $test2.'&DelId='.$_GET['id']);
  exit();
}
?>