<?php

$uniqueId = uniqid(); //ユニークなIDを自動生成

$id = $_GET['id'];
$page_data = [];


$comment_board = []; //全体配列
$text = '';
$DATA = []; //追加するデータ
$COMMENT_BOARD = []; //表示する配列

$error_message = [];

// ページの情報をココで取得
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
        $article_data[] = [$row['id'], $row['title'], $row['article_text']];
    }
}
foreach ($article_data as $index => list($ID)) {
  if ($ID == $id) {
    $page_data = $article_data[$index];
  }
}

// コメントの情報をココで取得

$sql = 'SELECT * FROM comment';
$res = $mysqli->query($sql);

if ($result = $mysqli->query($sql)) {
  // 連想配列を取得
  while ($row = mysqli_fetch_array($result)){
      $comment_data[] = [$row['comment_id'], $row['article_id'], $row['text']];
  }
}
foreach ($comment_data as $index => list($key, $comment_id)) {
  $comment_board[] = $comment_data[$index];
  if ($comment_id == $id) {
    $COMMENT_BOARD[] = $comment_data[$index];
  }
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$_POSTはHTTPリクエストで渡された値を取得する
  //リクエストパラメーターが空でなければ
  if (!empty($_POST['txt'])) {
    //投稿ボタンが押された場合
    if (mb_strlen($_POST['txt']) > 50) {
      $error_message[] = "コメント数は50文字以内でお願いします。";
    } else {

    //$textに送信されたテキストを代入
    $text = $_POST['txt'];

    //新規データ
    $DATA = [$uniqueId, $id, $text];
    //新規データを全体配列に代入する
    $comment_board[] = $DATA;

    //全体配列をファイルに保存する
    $sql = "INSERT INTO comment (comment_Id, article_Id, text) VALUES ('$uniqueId','$id','$text')";
    $res = $mysqli->query($sql);

    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了
    exit;
    }
  } else if (isset($_POST['del'])) {
    //削除ボタンが押された場合
    // DELETEのSQL作成
    $sql = "DELETE FROM comment WHERE comment_Id = '{$_POST['del']}'";

    // SQL実行
    $res = $mysqli->query($sql);
   
    $mysqli->close();
      
    

    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['REQUEST_URI']);
    //プログラム終了
    exit;
  } else if (empty($_POST['txt'])) {
    $error_message[] = "コメントは必須です。";
  }
}

?>      




<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./css/article.css">
        <title>Laravel News - article</title>
    </head>
    <body>
        <nav class="main-header">
            <div class="nav-bar">
                <a href="index.php" class="nav-link">Laravel News</a>
            </div>
        </nav>
        <section class="post-detail">
                <h3 class="post-title"><?php echo $page_data[1]; ?></h3>
                <p class="post-body"><?php echo $page_data[2]; ?></p>
        </section>

        <hr>

        <!-- エラーメッセージ -->
        <ul>
        <?php foreach ($error_message as $error) : ?>
            <li>
            <?php echo $error ?>
            </li>
        <?php endforeach; ?>
        </ul>
        <!-- コメント表示部分 -->
        <section class="comments">
        <form method="post" class="commentForm">
        <textarea name="txt" class="inputFlex commentInput"></textarea>
        <input type="submit" value="コメントを書く" name='<?php echo $id; ?>' class="commnetSubmitStyle">
      </form>
      <?php foreach ((array)$COMMENT_BOARD as $DATA) : ?>
        <div class="commentContent">
          <p>
            <?php echo $DATA[2] ?>
          </p>
          <div>
            <form method="post">
              <input type="hidden" name="del" value="<?php echo $DATA[0]; ?>">
              <input type="submit" value="コメントを消す" class="deleteComment">
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
        </section>

    </body>
</html>