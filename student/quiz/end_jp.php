<?php
require_once "../../config.php";
require_once( '../../student/class.modal_create.php' );


$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$answer_id = filter_input(INPUT_GET, 'an', FILTER_SANITIZE_SPECIAL_CHARS);
$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

$Curl = new Curl($url);
$modal = new modalCreate();

# 小テスト問題
$Quiz = new Quiz($quiz_id, $Curl);
$quiz_data = $Quiz->getQuiz();

# Save
if (isset($_SESSION['student']['start_time']) && ! empty($_SESSION['student']['start_time'])) {
    $Answer = new QuizAnswer($quiz_id, $Curl);
    $Answer->setAnswerId($answer_id);
    $Answer->setQuizLimitTime($quiz_data['limit_time']);
    $Answer->updateQuizAnswer();
}

# 画面遷移
/*
$action = filter_input(INPUT_POST, 'action');

if ($action == 'result') {
    $_SESSION['student']['start_time'] = '';
    #unset($_SESSION['student']['start_time']);

    $Quiz->redirect("result.php?id=$quiz_id&an=$answer_id&bid=$bid");
}
*/


// ここからフォルダ関連コンテンツ関連
// 外部jSファイルへ安全にデータを渡す
function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}


$folder_flg = "";
$display_data = $modal->data_create( $_SESSION[ 'auth' ], $quiz_data );

$url = $_SERVER["HTTP_HOST"]."/student/quiz/";

if ( $display_data[ 'flg' ] == 'false' || $display_data == 'false' ) {

  $folder_flg = 'false';
  $action = filter_input(INPUT_POST, 'action');
  if ($action == 'result') {
      $_SESSION['student']['start_time'] = '';
      #unset($_SESSION['student']['start_time']);
      $Quiz->redirect("result.php?id=$quiz_id&an=$answer_id&bid=$bid");
  }

} else {

  $folder_flg = 'true';
  $display_data[ 'rq_result' ] = '<li><button data-result=result.php?id='.$quiz_id.'&an='.$answer_id .
    '&bid='. $bid .' class="score" id="quiz_result">テスト結果を見る</button></li>';
  $display_data[ 'bid' ] = $bid;

  $modal_display = $modal->modal_display( $display_data );
}
// フォルダ関連コンテンツ関連ここまで

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 受講者</title>
    <meta name="Author" content=""/>
    <!-- viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- favicon -->
    <link rel="shortcut icon" href="images/favicon.ico">
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
    <link rel="stylesheet" type="text/css" href="../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/quiz.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>
</head>
<body id="folder" data-folderflg='<?php echo $folder_flg; ?>' data-folder='<?php echo json_safe_encode( $display_data ); ?>'>

<div id="wrap">
    <!-- header -->
    <div id="header-bar">
        <div id="header">
            <!-- left -->
            <div class="header-left">
                <!-- h1 -->
                <div class="h1">
                    <a href="#">
                        <h1><img src="../images/logo.jpg" alt="ThinkBoard LMS"></h1>
                    </a>
                </div>
                <!-- sub menu -->
                <div class="header-submenu">
                    <div class="btn-userinfomation dropdown">
                        <a href="#" id="dropdownMenu-userinfo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <p class="erea-icon"><span class="icon-user-student"></span></p>
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="../account.php">パスワード変更</a>
                            </li>
                            <li class="loguot">
                                <a href="../auth/logout.php">ログアウト</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="../help/TBLMS_Student.pdf" target="_blank"><span>ヘルプ</span></a>
                    </div>
                </div>
            </div>
            <!-- right -->
            <div class="header-right">
                <nav class="nav-mainmenu">
                    <ul>
                        <li>
                            <a href="../info.php"><span>TOP</span></a>
                        </li>
                        <li class="active">
                            <a href="../contentslist.php"><span>講義受講</span></a>
                        </li>
                        <li>
                            <a href="../message/message_list.php"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="quiz-answer">

            <!-- ページタイトル -->
            <div class="quiz-top clearfix">
                <p class="text">テスト</p>
                <ul class="btns">
                    <li><a href="../contentslist.php?bid=<?php echo $bid; ?>">講義一覧へ戻る</a></li>
                </ul>
            </div>

            <!-- メインボックス -->
            <div class="quiz-box">

                <!-- head -->
                <div class="quiz-head">
                    <div class="quiz-title">
                        <p><?php echo $quiz_data['title']; ?> | <span class=""><?php echo $quiz_data['description']; ?></span></p>
                    </div>
<!--
                    <div class="timelimit">
                        <div class="in">
                            <p class="text">制限時間</p>
                            <p class="time">00:00:00</p>
                        </div>
                    </div>
-->
                </div>

                <!-- body -->
                <div class="quiz-body">

                    <!-- 問題リスト -->
                    <!--
                    <div class="question-itemgroup navbar-expand-lg">
                        <a class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span>全問題</span>
                        </a>
                        <form action="answer.php" method="post">
                        <ul class="collapse navbar-collapse clearfix" id="navbarSupportedContent">
                            <?php #for($i = 0; $max_query > $i; $i++): ?>
                                <?php #$num = $i + 1; ?>
                               <li<?php #echo ($p == $i) ? ' class=""' : ''; ?>><button type="submit" name="current_page" value="<?php #echo $i; ?>"><span>問題</span><?php #echo $num; ?></button></li>
                            <?php #endfor; ?>
                        </ul>
                        </form>
                    </div>
 -->
                    <!-- 問題内容 -->
                    <div class="question-main">
                        <!-- スタート画面 -->
                        <div class="question-end">
                        <p><?php echo $quiz_data['finished_message']; ?></p>
                        </div>
                        <!-- 前へ　次へ -->
                        <div class="answer-btns">
                        <!--<form action="" method="post">-->
                        <form action=<?php echo $_SERVER['REQUEST_URI'] ?> method="POST">
                            <ul>
                            <li><button id="quiz_result" type="submit" name="action" value="result">テスト結果を見る</button></li>
<!--                                <li><button type="button" onclick="location.href='index.php'">一覧へ戻る</button></li>
-->
                            </ul>
                        </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <?php echo $modal_display; ?>
</div>

<script type="text/javascript">

  $( function () {

    var $modalData = $( '#folder' ).data( 'folder' );
    var modalFlg = $( '#folder' ).data( 'folderflg' );
    var nextURL;

    if ( modalFlg == true ) {

      var options = {
        "backdrop":"static"
      };

      $( '#contents-continuity' ).modal( options );

      $( '.ok' ).on( 'click', function () {
        location.href = $modalData.url;
      });

      $( '.score' ).on( 'click', function () {
        var resultData = $( '.score' ).data( 'result' );
        location.href = resultData;
      });

      $( '.cancel' ).on( 'click', function () {
        location.href = '../../student/contentslist.php?bid='+$modalData.bid;
      });

    }

  });
</script>

</body>
</html>
