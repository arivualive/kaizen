<?php
require_once "../../config.php";

$quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
$answer_id = filter_input(INPUT_GET, 'an', FILTER_SANITIZE_SPECIAL_CHARS);
$bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

#$time_left = filter_input(INPUT_GET, 't', FILTER_SANITIZE_SPECIAL_CHARS);

$Curl = new Curl($url);
$Query = new Query($quiz_id, $Curl);

# 回答の保存
if (filter_input(INPUT_POST, 'submit') == 'answer') {
#    $time_left = filter_input(INPUT_POST, "time_left");
    $selection_id = filter_input(INPUT_POST, "selection_id", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    $Save = new QuizAnswerSave($quiz_id, $Curl);
    $Save->setQueryId(filter_input(INPUT_POST, 'query_id'));
    $Save->setAnswerId($answer_id);
    $Save->setSelectionId($selection_id);
    $Save->saveAnswer();
}

# view の表示
$Quiz = new Quiz($quiz_id, $Curl);
$Selection = new QuerySelection($quiz_id, $Curl);
$Choice = new QuizAnswerChoice($quiz_id, $Curl);
$Choice->setAnswerId($answer_id);

$View = new QuizAnswerView($Quiz, $Query, $Selection);
$View->setChoice($Choice);

#date_default_timezone_set('Asia/Tokyo');
//debug($View->showData());
extract($View->showData());

// 秒に変換
$LimitTime = $quiz_data['limit_time'];

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Thinkboard LMS students</title>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
    $(function() {
    <?php
        if(isset($_POST['StartTime'])) {
            echo '	var startTime = ' . $_POST['StartTime'] . ';';
        } else {
            echo '	var startTime = Date.now();';
        }
        # echo ' //スタート時間（UTC - 協定世界時）' . "\n";
    ?>
        countDown(startTime); //countDown()にスタート時間を渡す
        $('input[name="StartTime"]').val(startTime);

        function countDown(startTime) {
            var limit = <?php echo $LimitTime ?> * 60 * 1000; //制限時間（ミリ秒形式）
            var left = limit - (Date.now() - startTime); //残り時間を算出（ミリ秒形式）

            var dayTime = 24 * 60 * 60 * 1000; //1日のミリ秒
            //var d = Math.floor(left / dayTime); //制限時間から現在までの日数
            //var h = Math.floor((left % dayTime) / (60 * 60 * 1000)); //制限時間から現在までの時間
            //var m = Math.floor((left % dayTime) / (60 * 1000)) % 60; //制限時間から現在までの分
            var h = Math.floor((left % dayTime) / (60 * 60 * 1000)); //制限時間から現在までの時間
            var m = Math.floor((left % dayTime) / (60 * 1000)); //制限時間から現在までの分
            var s = Math.floor((left % dayTime) / 1000) % 60 % 60; //制限時間から現在までの秒

            if(limit > 0) {
                if(left <= 0){ //タイムアウト処理
                    alert('Time out!');
                    location.href = 'end.php?id=<?php echo $quiz_id;?>&an=<?php echo $answer_id;?>&bid=<?php echo $bid;?>';
                } else { //制限時間内の処理
                    //$("#timeleft").text(d + '日' + h + '時間' + m + '分' + s + '秒');
                    //$("#timeleft").text(m + '分' + s + '秒'); //<div>の残り時間を変更
                    h = ('0' + h).slice(-2);
                    m = ('0' + m).slice(-2);
                    s = ('0' + s).slice(-2);
                    $("#timeleft").text(h + ':' + m + ':' + s); //<div>の残り時間を変更
                    setTimeout(countDown, 1000, startTime); //1秒ごとにcountDown()を繰り返す
                }
            }
        }
    });
    </script>

</head>
<body>

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
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenu-userinfo">
                            <li class="PW">
                                <a href="../account.php">Change Password</a>
                            </li>
                            <li class="loguot">
                                <a href="../auth/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                    <div class="btn-help">
                        <a href="../help/TBLMS_Student.pdf" target="_blank"><span>help</span></a>
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
                            <a href="../contentslist.php"><span>Taking lectures</span></a>
                        </li>
                        <li>
                            <a href="message/message_list.php"><span>message</span></a>
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
                <p class="text">quiz</p>
                <ul class="btns">
                    <li><a href="../contentslist.php?bid=<?php echo $bid; ?>">Return to the lecture list</a></li>
                </ul>
            </div>

            <!-- メインボックス -->
            <div class="quiz-box">

                <!-- head -->
                <div class="quiz-head">
                    <div class="quiz-title">
                        <p><?php echo $quiz_data['title']; ?> | <span class=""><?php echo $quiz_data['description']; ?></span></p>
                    </div>
                    <div class="timelimit">
                        <div class="in">
                            <p class="text">Time limit</p>
                            <p id="timeleft" class="time">00:00:00</p>
                        </div>
                    </div>
                </div>

                <!-- body -->
                <div class="quiz-body">

                    <!-- 問題リスト -->
                    <div class="question-itemgroup navbar-expand-lg">
                        <a class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span>All issues</span>
                        </a>
                        <ul class="collapse navbar-collapse clearfix" id="navbarSupportedContent">
                            <?php for($i = 0; $max_query > $i; $i++): ?>
                                <?php $num = $i + 1; ?>
                              <li<?php echo ($p == $i) ? ' class="active"' : ''; ?>><button><span>question</span><?php echo $num; ?></button></li>
                            <?php endfor; ?>
                        </ul>
                    </div>

                    <!-- 問題内容 -->
                    <div class="question-main">

                        <!-- 問題タイトル -->
                        <div class="question-contents">
                            <div class="question-head">
                                <!-- 問題番号 -->
                                <p class="number">question<?php echo $p+1; ?></p><p>Allocation of points <?php echo $query_data['score']; ?>point</p>
                            </div>
                            <div class="question-title">
                                <!-- 画像 -->
<?php
    // path
    $path = $query_data['quiz_id'] . '_' . $query_data['query_id'] . '.deploy';

    // 画像ファイルの取得
    $image = $query_data['image_file_name'];
    $image_file_path = $url2 . 'file/image/' . $path;
?>
<?php if (file_exists($image_file_path) || $image != ''): ?>
<p class="img"><img src="<?php echo $image_file_path; ?>" alt="image" /></img>
<?php endif; ?>

                            <!--
                                <p class="img">
                                    <img src="sample-question-image.jpg">
                                </p> -->
                                <!-- テキスト -->
                                <p class="text" style="white-space:pre-wrap;"><?php echo $query_data['query_text']; ?></p>
                            </div>
                        </div>

                        <!-- 解答 -->
                        <!--<form action="" method="post">-->
                        <form action=<?php echo $_SERVER['REQUEST_URI'] ?> method="POST">
                        <div class="answer-erea">
                            <div class="answer-head clearfix">
                            <p>answer</p>
                            <span><?php echo $type_jp; ?></span>
                            </div>
                            <div class="select clearfix">

                                <ul>
                                <?php foreach ((array) $selection_data as $item): ?>
                                    <?php if ($query_data['query_type'] == 0): ?>
                                       <li><label><input type="radio" name="selection_id[]" value="<?php echo $item['selection_id']; ?>" <?php echo (isset($item['checked']))? $item['checked']:'';?>><?php echo $item['text']; ?></label></li>
                                       <?php else: ?>
                                       <li><label><input type="checkbox" name="selection_id[]" value="<?php echo $item['selection_id']; ?>" <?php echo (isset($item['checked']))? $item['checked']:'';?>><?php echo $item['text']; ?></label></li>
                                    <?php endif; ?>
                                <?php endforeach;?>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" name="StartTime" value="" />
                        <input type="hidden" name="query_id" value="<?php echo $query_data['query_id'];?>" />
                        <input type="hidden" name="current_page" value="<?php echo ++$p;?>" />
                        <!-- 前へ　次へ -->
                        <div class="answer-btns">
                            <ul>
                                <li class="back"><button type="submit" name="submit" value="back">Forward</button></li>
                                <li class="next"><button type="submit" name="submit" value="answer">Next</button></li>
                            </ul>
                        </div>
                        </form>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
<?php echo "<pre>";print_r($type_jp); echo "</pre>";?>
<?php echo "<pre>";print_r($type); echo "</pre>";?>
</body>
</html>
