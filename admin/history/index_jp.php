<?php
error_reporting(~E_NOTICE);
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-20")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄
    
    header('Location: ../auth/index.php');
    exit();
}

$curl = new Curl($url);

$path = '../../library/category/';
$csvpath = $path. 'csv/';
$backpath = './';
$_POST[ 'csvfile' ] = $csvpath . 'contents.csv';

//ツリー情報CSVファイル名がない場合
if(empty($_POST['csvfile'])) { $_POST['csvfile'] = $csvpath . 'users.csv'; }

$lines = @file( $_POST[ 'csvfile' ], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

if ( !$lines ) { $lines = array(); }
mb_convert_variables( 'UTF-8', 'SJIS-win', $lines );
unset( $lines[0], $lines[1] );

//カテゴリー計算用ファイルを読込み
require_once(dirname(__FILE__) . '/' . $path . 'catecalc.php');

$send_data = [];
$send_data[ 'enable' ] = 1;

// コンテンツグループ（ 旧 科目 ）を取得
$data = array(
    'repository' => 'SubjectSectionRepository'
    , 'method' => 'findSubjectGenreAll'
    , 'params' => $send_data
);

$subject_data = $curl->send( $data );

function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

//debug_r( $subject_data );

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS 管理者</title>
    <!-- favicon -->
	<link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <!-- js -->
    <script type="text/javascript" src="../../js/jquery-3.1.1.js"></script>
    <!--<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>-->
    <script type="text/javascript" src="js/histoy_index.js"></script>
    <link rel="stylesheet" href="../../css/history.css">

    <script type="text/javascript" src="../../js/jquery-ui-1.12.1.custom.min.js"></script>
    <script type="text/javascript" src="../../js/tabulator350/dist/js/tabulator.min.js"></script>
    <link rel="stylesheet" href="../../js/tabulator350/dist/css/tabulator_site.min.css">
    <link rel="stylesheet" href="../../js/tabulator350/dist/css/custom.css">

    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
    <script src="../js/script.js"></script>
    <script type="text/javascript" src="../../js/papaparse.min.js"></script>
</head>
<body>

<div id="wrap">

    <!-- ▼navgation -->
    <div id="nav-fixed">
        <!-- ▼h1 -->
        <div class="brand">
            <a href="../info.php">
                <h1>
                    <div class="img_h1"><img src="../images/logo.jpg" alt="ThinkBoard LMS"></div>
                    <p class="authority">管理者用</p>
                </h1>
            </a>
        </div>
        <!-- ▼scrol erea -->
        <div id="scrollerea">
            <nav id="mainnav">
                <ul id="accordion" class="accordion">
                    <li>
                        <a href="../info.php"><span class="icon-main-home"></span>HOME</a>
                    </li>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-1000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-user-add"></span>受講者所属・ID設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/users.php">所属グループ設定</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="../user/student.php">受講者ID設定</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>コンテンツ設定</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php">コンテンツ登録・編集</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="../access/contents-control.php"><span class="icon-movie-set"></span>受講対象設定</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li class="open">
                        <a class="togglebtn"><span class="icon-graph"></span>受講状況</a>
                        <ul class="togglemenu open">
                            <li><a href="../history/index.php" class="active">受講者から確認</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="../message/message_list.php"><span class="icon-main-message"></span>メッセージ</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="../help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>ヘルプ</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="../user/admin.php"><span class="icon-user-add"></span>管理者ID・権限設定</a>
                    </li>
                    <?php }; ?>
                </ul>
            </nav>
        </div>
    </div>
    <!-- ▲navgation -->

    <!-- ▼header -->
    <div id="header">
        <!-- ▼topicpath -->
        <div id="topicpath">
            <ol>
                <li><a href="../info.php">HOME</a></li>
                <li>受講状況</li>
                <li class="active"><a>受講者から確認</a></li>
            </ol>
        </div>
        <!-- ▼user information -->
        <div id="userinfo" class="button-dropdown">
            <a class="link" href="javascript:void(0)">
                <div class="erea-image"></div>
                <div class="erea-name">
                    <p class="authority">学校管理者</p>
                    <p class="username"><?php echo $_SESSION[ 'auth' ][ 'admin_name' ];?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="#"><span class="icon-lock"></span>パスワード変更</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>受講者から確認</h2>
        </div>
        <!-- ▲h2 -->

        <!-- ▼main system -->
        <div>
          <select id="genre" name="junleSelect" data-schoolID=<?php echo $_SESSION[ 'auth' ][ 'school_id' ]; ?>>
            <option value="dummy">--選択してください--</option>
            <?php foreach ( (array)$lines as $key => $line ):
              $item = explode( ',', $line );

              echo '<option value="' . $item[1] . '">';
              echo ($item[0] == 1) ? '▼' : '　　';
              echo $item[3] . '</option>';

              //if ( $item[0] == 1 ) { echo '<option value=' . $item[1] . '>' . $item[3] . '</option>'; } //第2カテゴリー追加前の処理
            ?>

            <?php endforeach; ?>
            <!--<?php //foreach ( (array)$course_select as $key => $genre_name ): ?>
              <option value="<?php //echo $key; ?>">
                <?php //echo $genre_name; ?>
              </option>
            <?php //endforeach; ?>-->
          </select>
          <!--<button class="outputcsv_btn" id="download-csv" disabled >CSV出力</button>-->
          <button class="attribute_btn" id="attribute" value="OFF">所属表示</button>
          &nbsp;
          <a href="#single-csv" id="single-csv" hidden></a>
          <button id="single" onclick="document.getElementById('single-csv').click()">CSV出力</button>
          <a href="#multi-csv" id="multi-csv" hidden></a>
          <button id="multi" onclick="document.getElementById('multi-csv').click()">詳細CSV出力</button>
          <span id="update-time" style="float:right"></span>
          <!--<button class="" id="attribute2" value="OFF">attribute</button>-->
          <form id="post-form" method="post">
            <input type="hidden" name="csv_name" value="student_data">
            <input id="key_data" type="hidden" name="export_csv">
          </form>
          <div id="divhTable">
            <div class="table-to-export" data-sheet-name="受講履歴" id=""></div>
          </div>

          <p class="student_history">
          <br>
          <br>
          <span id="name"></span>
          <span id="category"></span>&emsp;
          <select id="categorySelect" data-schoolID2=<?php echo $_SESSION[ 'auth' ][ 'school_id' ]; ?> style="width:150px;">
            <option value="0">総合</option>
            <option value="1">動画</option>
            <option value="2">テスト</option>
            <option value="3">アンケート</option>
            <option value="4">レポート</option>
          </select>
          </p>
          <p class="student_history" id="inputNumber"><label>受講者No.
          <input type="number" name="firstNumber" id="firstNumber" min="0" class="csv_number"> ～ <input type="number" class="csv_number" id="secoundNumber" name="secoundNumber" min="1" ></label>
          <button class="outputcsv_btn" id="download-csv2" disabled >CSV出力</button>
          <span id="errmsg" style="color:#F00"></span>
          </p>

        </div>
        <!-- ▲main system -->
        <!--<div id="example-table"></div>-->

    </div>
    <!-- ▲main -->
</div>
</body>
</html>
