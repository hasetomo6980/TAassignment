<?php

/**
 * HTML特殊文字をエスケープする関数
 */
function h($str)
{
	return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * 投稿削除に関する通知用関数
 */
function mes($message, $id)
{
	$mes = '投稿ID' . $id . 'の投稿削除に' . $message . 'しました。';
//OKを押した時にリダイレクトするように
	echo "<script>if(!alert('$mes')){
		location.href='/index.php';
	}</script>";
}

/**
 * DB接続情報を設定
 */
$pdo = new PDO(
	"mysql:port=3309;dbname=taassignment;host=localhost",
	"root",
	"",
	array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET `utf8`")
);

//ページ番号定義
$page = null;
// ページ番号を1以上の整数になるように補正
if (!empty($_GET['page'])) {
	$page = max(1, $_GET['page']);
} else {
	$page = 1;
}
// 1ページの最大表示数
$disp_max = 10;

//SQLを実行
$regist = $pdo->prepare(
	implode(
		' ',
		array(
			'SELECT',
			'*',
			'FROM post',
			'ORDER BY `id` DESC',
			'LIMIT ?, ?',
		)
	)
);
// 値をバインド
$regist->bindValue(1, ($page - 1) * $disp_max, PDO::PARAM_INT);
$regist->bindValue(2, $disp_max, PDO::PARAM_INT);
// 読み出しを実行
$regist->execute();
// ページ番号にあった分だけ取り出す
$articles = $regist->fetchAll(PDO::FETCH_ASSOC);
// 現在のページの件数をセット
$current_count = count($articles);
// 総件数をセット
$whole_count = (int) $pdo->query('SELECT count(*) from post')->fetchColumn();
// 総ページ数をセット
$page_count = ceil($whole_count / $disp_max);
//SQL実行終わり

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<title>掲示板サンプル</title>
	<link href="css/index.css" rel="stylesheet">
</head>

<body>
	<?php
	//delete.phpからの遷移か確認
	if (!empty($_GET['delete']) && !empty($_GET['DelId'])) {
		mes($_GET['delete'], $_GET['DelId']);
	}
	?>
	<div id="thread">
		<h1><a href="index.php">掲示板サンプル</a></h1>
		<section>
			<h2>新規投稿</h2>
			<form action="post.php" method="post">
				名前　　：<input type="text" name="name" value="" class="post post-name"><br>
				タイトル：<input type="text" name="title" value="" class="post post-title"><br>
				投稿内容：<textarea type="text" name="contents" value="" class="post post-contents"></textarea><br>
				<button type="submit" class="btn">投稿</button>
				<?php
				//post.phpからの遷移か確認
				if (!empty($_GET['post'])) {
					echo "投稿", $_GET['post'];
				}
				?>
			</form>
		</section>

		<section>
			<h2>投稿内容一覧</h2>

			<?php if (!empty($articles)): ?>
				<div id="articles">
					<?php foreach ($articles as $article): ?>
						<div id="article">
							<div class="id-name-time">
								<?= h($article['id']) ?>&nbsp;名前:
								<?= h($article['name']) ?>&nbsp;
								<?= h($article['created_at']) ?>
							</div>
							<div class="title">
								<?= h($article['title']) ?>
							</div>
							<div class="contents">
								<?= h($article['contents']) ?>
							</div>
							<button class="delete-btn btn" type="button">
								<a href="delete.php?id=<?= h($article['id']) ?> ">投稿削除</a>
							</button>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</section>
		<div class="thread-footer">
			<div class="navmenu">
				<!--ページング処理-->
				<?php if ($page > 1): ?>
					<a href="?page=<?= $page - 1 ?>">前</a> |
				<?php endif; ?>
				<a href="?">最新</a>
				<?php if (!empty($page_count) and $page < $page_count): ?>
					| <a href="?page=<?= $page + 1 ?>">次</a>
				<?php endif; ?>
			</div>
			<p class="page">
				<?php
				if (empty($current_count)) {
					echo 'まだ書き込みはありません';
				} else {
					printf(
						'%d件中%d件目～%d件目(%dページ中%dページ目)を表示中',
						$whole_count,
						($tmp = ($page - 1) * $disp_max) + 1,
						$tmp + $current_count,
						$page_count,
						$page
					);
				}
				?>
			</p>
		</div>
	</div>

</body>

</html>