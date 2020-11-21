<?php

$title = ""; //タイトルの変数
$text = ""; //記事の内容

$FILE = "article.txt"; //保存するファイル名
$id = uniqid(); //ユニークなIDを自動作成
$DATA = []; //一回分の投稿の情報を入れる
$BOARD = []; //すべての投稿の情報を入れる
$error_msg = [];

//DBを読み込み$BOARDに格納
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'Laravel_News';
$db_port = 3306;

$mysqli = new mysqli(
  $db_host,
  $db_user,
  $db_password,
  $db_db
);

$mysqli->set_charset('utf8');

$sql = 'SELECT * FROM article';
$res = $mysqli->query($sql);

if ($result = $mysqli->query($sql)) {
    // 連想配列を取得
    while ($row = mysqli_fetch_array($result)){
        $BOARD[] = [$row['id'], $row['title'], $row['article_text']];
    }
}

if(mb_strlen($_POST["title"]) >= 30) {
    $error_msg[] = "タイトルは30文字以下です。";
}


if($_SERVER["REQUEST_METHOD"] == "POST"){
    //リクエストパラメーターが空でなければ
    if (!empty($_POST["body"]) && !empty($_POST["title"])) {
        //投稿ボタンが押された場合

        //$textに送信されたテキストを代入
        $title = $_POST["title"];
        $text = $_POST["body"];

        //保存の処理
        //新規データ
        $DATA = [$id, $title, $text];
        $BOARD[] = $DATA;

        //全体配列をDBに保存する
        $sql = "INSERT INTO article (id, title, article_text) VALUES ('$id','$title','$text')";
        $res = $mysqli->query($sql);
        $mysqli->close();


          //header()で指定したページにリダイレクト
        //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
        header('Location: ' . $_SERVER['SCRIPT_NAME']);
        //プログラム終了
        exit;
    }
    else {
        if(empty($_POST["title"])) $error_msg[] = "タイトルは必須です。";
        if(empty($_POST["body"])) $error_msg[] = "記事は必須です。";
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./css/post.css">
        <title>Laravel News - index</title>
    </head>
    <body>
        <nav class="main-header">
            <div class="nav-bar">
                <a href="index.php" class="nav-link">Laravel News</a>
            </div>
        </nav>
        <section class="form-post">
            <h2 class="content-header">さぁ、最新のニュースをシェアしましょう</h2>

            <!-- エラーメッセージ -->
            <?php foreach($error_msg as $error) :?>
            <p><?php echo $error; ?></p>
            <?php endforeach?>

            <!-- フォーム -->
            <form id="formPost" method="POST" class="form" onsubmit="return submitCheckFunction()">
                <div class="input-title">
                <label for="title">タイトル：</label>
                <input type="text" name="title">
                </div>
                <div class="input-body">
                <label for="body">記事：</label>
                <textarea name="body" cols="50" rows="10" id="body"></textarea>
                </div>
                <div class="input-submit">
                <input type="submit" class="btn-submit" value="投稿">
                </div>
            </form>
        </section>

        <hr>
        
        <!-- 記事 -->
        <section class="posts">
            <?php foreach ((array)$BOARD as $ARTICLE) : ?>
                <div class="post">
                <h3 class="post-title">
                <?php echo $ARTICLE[1]; ?>
                </h3>
                <p class="post-body">
                <?php echo $ARTICLE[2]; ?>
                </p>
                <a href="/article.php?id=<?php echo $ARTICLE[0]; ?>">記事全文・コメントを見る</a>
                </div>
                <hr>
            <?php endforeach; ?>

        </section>
        <script type="text/javascript" src="./js/index.js"></script>           
    </body>
</html>