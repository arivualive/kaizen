<?php
require_once "../../../config.php";
//login_check('/student/auth/');

$curl = new Curl($url);

//アンケートIDの取得処理
if (filter_input(INPUT_GET, "id")) {
    $questionnaire_id = filter_input(INPUT_GET, "id");
}
if (filter_input(INPUT_GET, "list_id")) {
    $list_id = filter_input(INPUT_GET, "list_id");
}

//Modelに渡す各値を取得
//年度(grade)・コース(course)・ユニット(classroom)の各値は、学生IDと学校IDから取得する
$student_id = $_SESSION['auth']['student_id'];
$school_id = $_SESSION['auth']['school_id'];
$questionnaire_model = new GetStudentQuestionnaireAnswer($student_id, $school_id, $questionnaire_id, $curl);

//アンケートデータを取得
$title_data = $questionnaire_model->getQuestionnaire();
//debug($title_data);
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
//debug($query_data);

if (filter_input(INPUT_POST, "sendFlag") == true) {
    $data['questionnaire_id'] = $questionnaire_id;
    $query_send_data = array(array(), array(), array()
    );
    $value_array = explode(",", filter_input(INPUT_POST, "queryData"));
    $key_array = explode(",", filter_input(INPUT_POST, "queryId"));
    $type_array = explode(",", filter_input(INPUT_POST, "queryType"));

    $multiple_choice_key = 0;

    for( $i = 0, $j = 0 ; $i < count($value_array) ; $i++ ) {
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

            if($data['query_type'] == 0) {
                $questionnaire_model->setQuestionnaireAnswerQuerySingleChoice($data);
            } else if($data['query_type'] == 1) {
                $questionnaire_model->setQuestionnaireAnswerQueryMultipleChoice($data);
            } else if($data['query_type'] == 2) {
                $questionnaire_model->setQuestionnaireAnswerQueryWord($data);
            } else if($data['query_type'] == 3) {
                $questionnaire_model->setQuestionnaireAnswerQueryLength($data);
            }

        }
    }
}

//$student_data = new GetStudentQuestionnaireAnswer($student_id, $school_id, $curl);
//$test = $student_data->getStudentDataId();
//debug($test);
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
	<link rel="stylesheet" type="text/css" href="../css/rangeslider.css"><!-- スライドバー用 -->
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/sentence.css">
    <!-- js -->
    <script src="../../../js/jquery-3.1.1.js"></script>
    <script src="../../../js/popper.min.js"></script>
    <script src="../../js/bootstrap.js"></script>
    <script src="../../js/rangeslider.js"></script><!-- スライドバー用 -->
    <script src="../../js/script.js"></script>
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
                        <a href="#"><span>help</span></a>
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
                            <a href="../message/message_list.php?p=1"><span>message</span></a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    <div id="container-maincontents" class="container-maincontents clearfix">
        <!-- ページタイトル -->
        <div class="sentence-top clearfix">
            <p class="text">Questionnaire response</p>
            <ul class="btns">
                <li><a href="../contentslist.php?id=<?php echo $list_id?>" >Return to the lecture list</a></li>
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
                        <p>Start the questionnaire.</p>
                    </div>

                    <!-- 前へ　次へ -->
                    <div class="sentence-btns">
                        <ul>
                            <li><button class='start'>Start a questionnaire</button></li>
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
                                        echo "<p class='any' value='0'>Optional answer</p>";
                                    } else if($item['flg_query_must'] == 1) {
                                        echo "<p class='any' value='1'>Required answer</p>";
                                    } else {};
                                ?>
                            </div>
                            <div class="title">
                                <!-- テキスト -->
                                <p class="text"><?php echo $item["query"]; ?></p>
                            </div>

                            <!-- 解答 -->
                            <div class="answer">
                                <?php
                                    if($item['query_type'] == 0) {
                                        echo "<form class='query_check'>";
                                        for( $j = 0 ; $j < $item['length'] ; $j++ ) {
                                            echo "<label><li><input type='checkbox' class='input-query-check' name='" . $item['query_id'] . "' value=" . $item[$j]['choices_id'] . ">" . $item[$j]['text'] . "</li></label><br>";
                                        }
                                        echo "</form>";
                                    } else if($item['query_type'] == 1) {
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
                                            <p>※ 2000 characters or less</p>
                                        ";
                                    } else if($item['query_type'] == 3) {
                                        echo "
                                            <form class='query_slidebar'>
                                                <p class='value-min'>" . $item[0]['min_limit'] . "</p>
                                                <p class='value-max'>" . $item[0]['max_limit'] . "</p>
                                                <input type='range' class='input-query-slidebar' name='" . $item['query_id'] . "' min='" . $item[0]['min_limit'] . "' max='" . $item[0]['max_limit'] . "' step='" . $item[0]['step'] . "' data-rangeslider/>
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
                                            echo "<li class='back'><button>Forward</button></li>";
                                        }
                                        if($i != count($query_data) - 1) {
                                            echo "<li class='next'><button>Next</button></li>";
                                        }
                                        if($i == count($query_data) - 1) {
                                            echo "
                                                <li class='submit'><button>Send a questionnaire</button></li>
                                                <p id='cantion'>Questionnaire items for required items have not been answered</p>
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
                        <p>I have sent a questionnaire.
                    </div>

                    <!-- 前へ　次へ -->
                    <div class="sentence-btns">
                        <ul>
                            <li class='list'><button>Return to the lecture list</button></li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<!--<form action='' method='post' name='send_form' id='send_form'>-->
<form action=<?php echo $_SERVER['REQUEST_URI']; ?> method="POST">
    <input form='send_form' type='hidden' name='sendFlag'>
    <input form='send_form' type='hidden' name='queryData'>
    <input form='send_form' type='hidden' name='queryId'>
    <input form='send_form' type='hidden' name='queryType'>
</form>

<script src="js/questionnaire.js"></script>

</body>
</html>
