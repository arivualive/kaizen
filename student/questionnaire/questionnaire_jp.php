<?php
require_once "../../config.php";
require_once( '../../student/class.modal_create.php' );
//login_check('/student/auth/');

$curl = new Curl($url);
$modal = new modalCreate();

//アンケートIDの取得処理
if (filter_input(INPUT_GET, "id")) {
    $questionnaire_id = filter_input(INPUT_GET, "id");
}
if (filter_input(INPUT_GET, "bid")) {
    $bid = filter_input(INPUT_GET, "bid");
}

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$student_id = $_SESSION['auth']['student_id'];
$school_id = $_SESSION['auth']['school_id'];
$questionnaire_model = new GetStudentQuestionnaireAnswer($student_id, $school_id, $questionnaire_id, $curl);

//アンケートデータを取得
$title_data = $questionnaire_model->getQuestionnaire();
//アンケートの各問データを取得
$query_data = $questionnaire_model->getQuestionnaireQuery();
for( $i = 0 ; $i < count($query_data) ; $i++ ){
    if($query_data[$i]['query_type'] == 0 || $query_data[$i]['query_type'] == 1) {
        $reply = $questionnaire_model->getQuestionnaireQueryChoices($query_data[$i]['query_id']);
        $query_data[$i] = array_merge($query_data[$i],$reply);
        // 2019/6/03 count関数対策
        $query_data[$i]['length'] = 0;
        if(is_countable($reply)){
          $query_data[$i]['length'] = count($reply);
        }
        //$query_data[$i]['length'] = count($reply);
    } else if($query_data[$i]['query_type'] == 3) {
        $reply = $questionnaire_model->getQuestionnaireQueryLength($query_data[$i]['query_id']);
        $query_data[$i] = array_merge($query_data[$i],$reply);
        // 2019/6/03 count関数対策
        $query_data[$i]['length'] = 0;
        if(is_countable($reply)){
          $query_data[$i]['length'] = count($reply);
        }
        //$query_data[$i]['length'] = count($reply);
    }
}

if (filter_input(INPUT_POST, "sendFlag") == true) {
    //debug($_POST);
    $data['questionnaire_id'] = $questionnaire_id;
    $query_send_data = array(array(), array(), array());
    $value_array = explode(",", filter_input(INPUT_POST, "queryData"));
    $key_array = explode(",", filter_input(INPUT_POST, "queryId"));
    $type_array = explode(",", filter_input(INPUT_POST, "queryType"));

    $multiple_choice_key = 0;
    //debug($value_array);
    //debug($key_array);
    //debug($type_array);

    for( $i = 0, $j = 0 ; $i < count($value_array) ; $i++ ) {
        $value_array[$i] = str_replace("(comma)", ",", $value_array[$i]);
        if($type_array[$i] == 1){
            if(isset($query_send_data[0][$j]) != true) {
                $query_send_data[0][$j] = array();
            }
            array_push($query_send_data[0][$j], $value_array[$i]);
        } else {
            array_push($query_send_data[0], $value_array[$i]);
        }
        if (isset($key_array[$i + 1])) {
            $next_value = $key_array[$i + 1];
        } else {
            $next_value = -1;
        }

        if( $key_array[$i] != $next_value) {
            array_push($query_send_data[1], $key_array[$i]);
            array_push($query_send_data[2], $type_array[$i]);
            $j++;
        }
    }
    //debug($query_send_data);
    //debug($data);

    if(count($query_send_data[0]) > 0) {
        $data['answer_id'] = $questionnaire_model->setQuestionnaireAnswer($data);
        for( $i = 0 ; $i < count($query_send_data[0]) ; $i++ ){
            $data['query_data'] = $query_send_data[0][$i];
            $data['query_id'] = $query_send_data[1][$i];
            $data['query_type'] = $query_send_data[2][$i];
            $data['answer_query_id'] = $questionnaire_model->setQuestionnaireAnswerQuery($data);
            //debug($data);

            //debug($data['answer_id']);
            //debug($data['query_id']);
            //debug($data['query_type']);

            if($data['query_type'] == 0) {
                $questionnaire_model->setQuestionnaireAnswerQuerySingleChoice($data);
            } else if($data['query_type'] == 1) {
                $questionnaire_model->setQuestionnaireAnswerQueryMultipleChoice($data);
            } else if($data['query_type'] == 2) {
                $data['query_data'] = htmlspecialchars($data['query_data']);
                $questionnaire_model->setQuestionnaireAnswerQueryWord($data);
            } else if($data['query_type'] == 3) {
                $questionnaire_model->setQuestionnaireAnswerQueryLength($data);
            }
        }
    }
}

// ここからフォルダ関連コンテンツ関連
// 外部jSファイルへ安全にデータを渡す
function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

$folder_flg = "";
$display_data = $modal->data_create( $_SESSION[ 'auth' ], $title_data );

if ( $display_data[ 'flg' ] == 'false' || $display_data == 'false' ) {
  $folder_flg = 'false';
} else {
  $folder_flg = 'true';
  $display_data[ 'bid' ] = $bid;
  $modal_display = $modal->modal_display( $display_data );
}

//$student_data = new GetStudentQuestionnaireAnswer($student_id, $school_id, $curl);
//$test = $student_data->getStudentDataId();
//debug($test);
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
	<link rel="stylesheet" type="text/css" href="../css/rangeslider.css"><!-- スライドバー用 -->
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/sentence.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/rangeslider.js"></script><!-- スライドバー用 -->
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
                            <p class="erea-username"><?php echo $_SESSION['auth']['student_name']; ?></p> <!-- ここにユーザーの名前が入ります -->
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
                        <a href="#"><span>ヘルプ</span></a>
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
                            <a href="../message/message_list.php?p=1"><span>メッセージ</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <!-- ページタイトル -->
        <div class="sentence-top clearfix">
            <p class="text">アンケート回答</p>
            <ul class="btns">
                <li><a href="../contentslist.php?bid=<?php echo $bid; ?>">講義一覧へ戻る</a></li>
            </ul>
        </div>

        <!-- メインボックス -->
        <div class="sentence-box">

            <?php if(filter_input(INPUT_POST, "sendFlag") == false) { ?>
                <!-- head -->
                <div class="sentence-head questionnaire">
                    <div class="sentence-title">
                        <p><?php echo $title_data['title']; ?></p>
                    </div>
                </div>

                <!-- body -->
                <div class="sentence-body" id="questionnaire_start" >
                    <div class="sentence-start">
                        <!-- <p><?php echo $title_data['description'] ?></p> -->
                        <p>アンケート回答を開始します。</p>
                    </div>

                    <!-- 前へ　次へ -->
                    <div class="sentence-btns">
                        <ul>
                            <li><button class='start'>アンケートに回答する</button></li>
                        </ul>
                    </div>
                </div>

                <?php $i = 0; ?>
                <?php foreach ((array) $query_data as $item): ?>
                    <!-- body -->
                    <div class="sentence-body" id="questionnaire_<?php echo $i; ?>">

                        <!-- 問題タイトル -->
                        <div class="question">
                            <div class="head clearfix">
                                <!-- 問題番号 -->
                                <p class="number">Q<?php echo $i + 1; ?></p>
                                <?php
                                    if($item['flg_query_must'] == 0) {
                                        echo "<p class='any' value='0'>任意解答</p>";
                                    } else if($item['flg_query_must'] == 1) {
                                        echo "<p class='any' value='1'>必須解答</p>";
                                    } else {};
                                ?>
                            </div>
                            <div class="title">
                                <!-- テキスト -->
                                <p class="text" style="white-space:pre-wrap;"><?php echo $item["query"]; ?></p>
                            </div>

                            <!-- 解答 -->
                            <div class="answer">
                                <?php
                                    if($item['query_type'] == 1) {
                                        echo "<form class='query_check'>";
                                        for( $j = 0 ; $j < $item['length'] ; $j++ ) {
                                            echo "<label><li><input type='checkbox' class='input-query-check' name='" . $item['query_id'] . "' value=" . $item[$j]['choices_id'] . ">" . $item[$j]['text'] . "</li></label><br>";
                                        }
                                        echo "</form>";
                                    } else if($item['query_type'] == 0) {
                                        echo "<form class='query_radio'>";
                                        for( $j = 0 ; $j < $item['length'] ; $j++ ) {
                                            echo "<label><li><input type='radio' class='input-query-radio' name='" . $item['query_id'] . "' value=" . $item[$j]['choices_id'] . ">" . $item[$j]['text'] . "</li></label><br>";
                                        }
                                        echo "</form>";
                                    } else if($item['query_type'] == 2) {
                                        echo "
                                            <form class='query_text'>
                                                <textarea class='input-query-text' name='" . $item['query_id'] . "'></textarea>
                                            </form>
                                            <p>※2000文字以内</p>
                                        ";
                                    } else if($item['query_type'] == 3) {
                                        echo "
                                            <form class='query_slidebar'>
                                                <p class='value-min'>" . $item[0]['min_label'] . "</p>
                                                <p class='value-max'>" . $item[0]['max_label'] . "</p>
                                                <input type='range' class='input-query-slidebar' name='" . $item['query_id'] . "' min='" . $item[0]['min_limit'] . "' max='" . $item[0]['max_limit'] . "' step='" . $item[0]['step'] . "' value='" . $item[0]['max_limit'] . "' data-rangeslider/>
                                                <output class='input-query-value'></output>
                                            </form>
                                        ";
                                    } else {};
                                ?>
                            </div>

                            <!-- 前へ　次へ -->
                            <div class="sentence-btns">
                                <ul>
                                    <?php
                                        if($i != 0) {
                                            echo "<li class='back'><button>前へ</button></li>";
                                        }
                                        if($i != count($query_data) - 1) {
                                            echo "<li class='next'><button>次へ</button></li>";
                                        }
                                        if($i == count($query_data) - 1) {
                                            echo "
                                                <li class='submit'><button>アンケートを送信する</button></li>
                                                <p id='cantion'>必須項目のアンケート項目が回答されていません</p>
                                            ";
                                        }
                                    ?>
                                </ul>
                            </div>

                        </div>
                    </div>
                    <?php $i++; ?>
                <?php endforeach ?>
            <?php } else { ?>
                <!-- head -->
                <div class="sentence-head questionnaire">
                    <div class="sentence-title">
                        <p><?php echo $title_data['title']; ?></p>
                    </div>
                </div>

                <!-- body -->
                <div class="sentence-body" id="questionnaire_start" >
                    <div class="sentence-start">
                        <!-- <p><?php echo $title_data['description'] ?></p> -->
                        <p>アンケートを送信しました。
                    </div>
                    <!-- modal(関連付けコンテンツ) -->
                    <?php echo $modal_display; ?>
                    <!-- 前へ　次へ -->
                    <div class="sentence-btns">
                        <ul>
                            <li class='list'><button id="return">講義一覧へ戻る</button></li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<form action=<?php echo $_SERVER['REQUEST_URI']; ?> method="POST" name='send_form' id='send_form'>
<!--<form action='' method='post' name='send_form' id='send_form'>-->
    <input form='send_form' type='hidden' name='sendFlag'>
    <input form='send_form' type='hidden' name='queryData'>
    <input form='send_form' type='hidden' name='queryId'>
    <input form='send_form' type='hidden' name='queryType'>
    <input type="hidden" id="bid" value="<?php echo $bid?>" >
</form>

<script src="js/questionnaire.js"></script>

</body>
</html>
