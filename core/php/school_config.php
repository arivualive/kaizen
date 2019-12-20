<?php
//config.phpで使用するフラグ
$view_flag = 1;
require '../config.php';

//ログインチェック
if($_SESSION['auth']['organizer_id'] == ''){
    //$base = (empty($_SERVER["HTTPS"]) ? "https://" : "https://") . $_SERVER["HTTP_HOST"];
    header('Location: ../index.php');
    exit();
} else {
    print_r('Login User Name : ' . $_SESSION['auth']['organizer_name'] . '<br/>');
    print_r('Login User ID   : ' . $_SESSION['auth']['organizer_id'] . '<br/>');
    print_r('<a href="index.php">一覧に戻る</a><br/>');
}

//学校ID
if(isset($_GET['id'])){
    $data['school_id'] = $_GET['id'];
} else {
    $data['school_id'] = 0;
}

$curl = new Curl($url);
$schoolInfo = new SchoolConfig($curl);

//保存ボタン押下
if(isset($_POST['insert_flag'])) {
    $data['school_name'] = $_POST['school_name'];
    $data['call_sign'] = $_POST['call_sign'];
    $data['max_id_of_admin'] = $_POST['max_id_of_admin'];
    $data['max_id_of_teacher'] = $_POST['max_id_of_teacher'];
    $data['max_id_of_student'] = $_POST['max_id_of_student'];
    $data['max_school_contents_total_giga_byte'] = $_POST['max_school_contents_total_giga_byte'];
    $data['enable'] = $_POST['enable'];
    $data['display_order'] = $_POST['display_order'];
    if($data['school_id'] != 0) {
        $schoolInfo->changeSchool($data, 'update');
    } else {
        $schoolInfo->changeSchool($data, 'insert');
    }
}

//------ FTPの設定をsatellite毎にどのように設定・読み込むかが面倒な為、FTP絡みの仕様が決まってから実装 ------//
//保存ボタン押下
/*
if(isset($_POST['config_change_flag'])) {
    $data['file_path'] = $_POST['file_path'];
    $data['access_student'] = $_POST['access_student'];
    $data['teacher_list'] = $_POST['teacher_list'];
    $data['student_list'] = $_POST['student_list'];
    $data['access_contents'] = $_POST['access_contents'];
    $data['contents_list'] = $_POST['contents_list'];
    $data['contents_control'] = $_POST['contents_control'];
    $data['history'] = $_POST['history'];
    $data['viewing_wise'] = $_POST['viewing_wise'];
    $data['message'] = $_POST['message'];
    $data['help'] = $_POST['help'];
    print_r($data);

    $file_detail = "<?php\n";

    if($data['access_student']) {
        $file_detail .= "    \$page_config['access_student'] = 1;     //所属・ID設定 -> 所属グループ設定\n";
    } else {
        $file_detail .= "    \$page_config['access_student'] = 0;     //所属・ID設定 -> 所属グループ設定\n";
    }

    if($data['teacher_list']) {
        $file_detail .= "    \$page_config['teacher_list'] = 1;       //所属・ID設定 -> 教員ID設定 [未実装]\n";
    } else {
        $file_detail .= "    \$page_config['teacher_list'] = 0;       //所属・ID設定 -> 教員ID設定 [未実装]\n";
    }

    if($data['student_list']) {
        $file_detail .= "    \$page_config['student_list'] = 1;       //所属・ID設定 -> 受講者ID設定\n";
    } else {
        $file_detail .= "    \$page_config['student_list'] = 0;       //所属・ID設定 -> 受講者ID設定\n";
    }

    $file_detail .= "\n";

    if($data['access_contents']) {
        $file_detail .= "    \$page_config['access_contents'] = 1;    //コンテンツ設定 -> コンテンツグループ設定\n";
    } else {
        $file_detail .= "    \$page_config['access_contents'] = 0;    //コンテンツ設定 -> コンテンツグループ設定\n";
    }

    if($data['contents_list']) {
        $file_detail .= "    \$page_config['contents_list'] = 1;      //コンテンツ設定 -> コンテンツ登録・編集\n";
    } else {
        $file_detail .= "    \$page_config['contents_list'] = 0;      //コンテンツ設定 -> コンテンツ登録・編集\n";
    }

    $file_detail .= "\n";

    if($data['contents_control']) {
        $file_detail .= "    \$page_config['contents_control'] = 1;   //受講対象設定\n";
    } else {
        $file_detail .= "    \$page_config['contents_control'] = 0;   //受講対象設定\n";
    }

    $file_detail .= "\n";

    if($data['history']) {
        $file_detail .= "    \$page_config['history'] = 1;            //受講状況 -> 受講者から確認\n";
    } else {
        $file_detail .= "    \$page_config['history'] = 0;            //受講状況 -> 受講者から確認\n";
    }

    if($data['viewing_wise']) {
        $file_detail .= "    \$page_config['viewing_wise'] = 1;       //受講状況 -> 動画授業から確認\n";
    } else {
        $file_detail .= "    \$page_config['viewing_wise'] = 0;       //受講状況 -> 動画授業から確認\n";
    }

    $file_detail .= "\n";

    if($data['message']) {
        $file_detail .= "    \$page_config['message'] = 1;            //メッセージ\n";
    } else {
        $file_detail .= "    \$page_config['message'] = 0;            //メッセージ\n";
    }

    $file_detail .= "\n";

    if($data['help']) {
        $file_detail .= "    \$page_config['help'] = 1;               //ヘルプ\n";
    } else {
        $file_detail .= "    \$page_config['help'] = 0;               //ヘルプ\n";
    }
}
*/

//List Data(学校データ-一覧)
$list_data = $schoolInfo->selectSchool('', 'list');

//Person Data(学校データ-詳細)
if($data['school_id'] != 0) {
    $person_data = $schoolInfo->selectSchool($data, 'person');
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>ThinkBoard LMS SuperAdmin</title>
    <!-- viewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- js -->
    <!--<script src="https://code.jquery.com/jquery-3.1.1.js"></script>-->
    <script src="../script/jquery-3.1.1.js"></script>
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>-->
    <script src="../script/popper.min.js"></script>
    <style>
        body {
            display: block;
            width: 100%;
            text-align:center;
        }
        div.schoollist {
            float: left;
            width: 33.33%;
        }
        table.schoollist {
            margin-left:auto;
            margin-right:auto;
        }
        table.schoollist td {
            width: 220px;
        }
        div.schooldata {
            float: left;
            width: 33.33%;
            text-align:left;
        }
        div.schooldata .text{
            width: 300px;
        }
        div.schooldata .save{
            margin-left: 300px;
        }
        .js-link {
            color: #0000FF;
            text-decoration: underline;
        }
        div.schoolconfig {
            float: left;
            width: 33.33%;
            text-align:left;
        }
        div.schoolconfig .strings{
            margin-left: 160px;
        }
        div.schoolconfig .save{
            margin-left: 200px;
        }
    </style>
    <script>
        $(function(){
            $('.listvalue').find('.id, .name, .enable, .new').on('click', function () {
                $scrool_value = $('.userlist').scrollTop();
                if($(this).siblings('.id').text() != '') {
                    window.location.href = './school_config.php?id=' + $(this).siblings('.id').text();
                } else {
                    window.location.href = './school_config.php';
                }
            });
        });
        function alertMessage(message) {
            value = alert(message);
            return value
        }
    </script>
</head>
<body>
<div id="login">
    <div class="main">
        <div class="in">
            <div class="schoollist">
                <table class="schoollist">
                    <tr class='listvalue'>
                        <td>学校名(school_name)</td>
                    </tr>
                    <tr class='listvalue'>
                        <td class="name js-link">新規登録</td>
                    </tr>
                    <?php foreach ((array)$list_data as $items): ?>
                        <tr class='listvalue'>
                            <td class="id" style="display: none;"><?php echo $items['school_id']; ?></td>
                            <td class="name js-link"><?php echo $items['school_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="schooldata">
                <div class="tab-pane fade show active" id="new-regist" role="tabpanel">
                    <?php
                        if($data['school_id'] != '') {
                            echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('受講者情報を変更します。よろしいですか？')\">";
                        } else {
                            echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('受講者情報を登録します。よろしいですか？')\">";
                        }
                    ?>
                        <!-- 学校名 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                　　　　　　　　学校名：<input type="text" class="text" maxlength="250" name="school_name" value="<?php echo $person_data['school_name'] ?>">
                            <?php } else { ?>
                                　　　　　　　　学校名：<input type="text" class="text" maxlength="250" name="school_name">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['school_name']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_school_name'>学校名が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 学校識別コード -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                　　　　学校識別コード：<input type="text" class="text" maxlength="3" name="call_sign" value="<?php echo $person_data['call_sign'] ?>">
                            <?php } else { ?>
                                　　　　学校識別コード：<input type="text" class="text" maxlength="3" name="call_sign">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['call_sign']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_call_sign'>学校識別コードが入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 最大管理者数 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                　　　　　最大管理者数：<input type="text" class="text" maxlength="10" name="max_id_of_admin" value="<?php echo $person_data['max_id_of_admin'] ?>">
                            <?php } else { ?>
                                　　　　　最大管理者数：<input type="text" class="text" maxlength="10" name="max_id_of_admin">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['max_id_of_admin']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_max_id_of_admin'>最大管理者数が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 最大教員数 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                　　　　　　最大教員数：<input type="text" class="text" maxlength="10" name="max_id_of_teacher" value="<?php echo $person_data['max_id_of_teacher'] ?>">
                            <?php } else { ?>
                                　　　　　　最大教員数：<input type="text" class="text" maxlength="10" name="max_id_of_teacher">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['max_id_of_teacher']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_max_id_of_teacher'>最大教員数が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 最大学生数 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                　　　　　　最大学生数：<input type="text" class="text" maxlength="10" name="max_id_of_student" value="<?php echo $person_data['max_id_of_student'] ?>">
                            <?php } else { ?>
                                　　　　　　最大学生数：<input type="text" class="text" maxlength="10" name="max_id_of_student">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['max_id_of_student']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_max_id_of_student'>最大学生数が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 許容コンテンツデータ量 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                許容コンテンツデータ量：<input type="text" class="text" maxlength="20" name="max_school_contents_total_giga_byte" value="<?php echo $person_data['max_school_contents_total_giga_byte'] ?>">
                            <?php } else { ?>
                                許容コンテンツデータ量：<input type="text" class="text" maxlength="20" name="max_school_contents_total_giga_byte">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['max_school_contents_total_giga_byte']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_max_school_contents_total_giga_byte'>許容コンテンツデータ量が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 有効・無効 -->
                        <dl>
                            <dd>
                            <?php if($data['school_id'] != 0) { ?>
                                <?php if($person_data['enable'] == 1) { ?>
                                    　　　　　　　稼働状態：　　　<input type="radio" class="radio" name="enable" value="0">無効　　<input type="radio" class="radio" name="enable" value="1" checked="checked">有効
                                <?php } else { ?>
                                    　　　　　　　稼働状態：　　　<input type="radio" class="radio" name="enable" value="0" checked="checked">無効　　<input type="radio" class="radio" name="enable" value="1">有効
                                <?php } ?>
                            <?php } else { ?>
                                　　　　　　　稼働状態：　　　<input type="radio" class="radio" name="enable" value="0" checked="checked">無効　　<input type="radio" class="radio" name="enable" value="1">有効
                            <?php } ?>
                            </dd>
                        </dl>
                        <!-- 並び順 -->
                        <?php if($data['school_id'] != 0) { ?>
                            <input type="hidden" name="display_order" value="<?php echo $person_data['display_order'] ?>">
                        <?php } else { ?>
                            <input type="hidden" name="display_order" value="<?php/*// 2019/6/03 count関数対策*/
                              if(is_countable($list_data)){
                                echo (count($list_data) + 1);
                              }
                            /*echo (count($list_data) + 1)*/ ?>">
                        <?php } ?>
                        <!-- 保存 -->
                        <dl>
                            <button class="save">保 存</button>
                            <input type="hidden" name="insert_flag" value="1" />
                            <input type="hidden" name="primary_id" value="<?php if(isset($student_data['student_id'])) { echo $student_data['student_id']; } else { echo'0'; } ?>" />
                        </dl>
                    </form>
                </div>
            </div>
            <!-- 現状、設定を参照するのみ 設定変更を実装するには、satellite毎のFTP関連の設定をどのように設定・読み込むか決める必要あり -->
            <div class="schoolconfig">
                <div class="tab-pane fade show active" id="new-regist" role="tabpanel">
                    <!-- 有効・無効 -->
                    <dl>
                        <dd>
                            <?php
                                echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('機能設定を変更します。よろしいですか？')\">";

                                $config_directory_file = file("page_config_directory.php", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                                if(isset($config_directory_file[$data['school_id'] - 1])) {
                                    if($config_directory_file[$data['school_id'] - 1] != "") {
                                        $file_path = $config_directory_file[$data['school_id'] - 1];
                                        require $file_path;
                                    }
                                }
                                if($data['school_id'] != 0 && isset($page_config)) {
                                    echo "<span class='strings'>page_config情報</span><br/>";
                                    //所属・ID設定 -> 所属グループ設定
                                    if($page_config['access_student'] == 1) {
                                        echo "　　　所属グループ設定：　　　有効<br/>";
                                    //  echo "　　　所属グループ設定：　　　<input type='radio' class='radio' name='access_student' value='0'>無効　　<input type='radio' class='radio' name='access_student' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　所属グループ設定：　　　無効<br/>";
                                    //  echo "　　　所属グループ設定：　　　<input type='radio' class='radio' name='access_student' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='access_student' value='1'>有効<br/>";
                                    }

                                    //所属・ID設定 -> 教員ID設定 [未実装]
                                    if($page_config['teacher_list'] == 1) {
                                        echo "　　　　　教員ＩＤ設定：　　　有効<br/>";
                                    //  echo "　　　　　教員ＩＤ設定：　　　<input type='radio' class='radio' name='teacher_list' value='0'>無効　　<input type='radio' class='radio' name='teacher_list' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　　教員ＩＤ設定：　　　無効<br/>";
                                    //  echo "　　　　　教員ＩＤ設定：　　　<input type='radio' class='radio' name='teacher_list' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='teacher_list' value='1'>有効<br/>";
                                    }

                                    //所属・ID設定 -> 受講者ID設定
                                    if($page_config['student_list'] == 1) {
                                        echo "　　　　受講者ＩＤ設定：　　　有効<br/>";
                                    //  echo "　　　　受講者ＩＤ設定：　　　<input type='radio' class='radio' name='student_list' value='0'>無効　　<input type='radio' class='radio' name='student_list' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　受講者ＩＤ設定：　　　無効<br/>";
                                    //  echo "　　　　受講者ＩＤ設定：　　　<input type='radio' class='radio' name='student_list' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='student_list' value='1'>有効<br/>";
                                    }

                                    //コンテンツ設定 -> コンテンツグループ設定
                                    if($page_config['access_contents'] == 1) {
                                        echo "コンテンツグループ設定：　　　有効<br/>";
                                    //  echo "コンテンツグループ設定：　　　<input type='radio' class='radio' name='access_contents' value='0'>無効　　<input type='radio' class='radio' name='access_contents' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "コンテンツグループ設定：　　　無効<br/>";
                                    //  echo "コンテンツグループ設定：　　　<input type='radio' class='radio' name='access_contents' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='access_contents' value='1'>有効<br/>";
                                    }

                                    //コンテンツ設定 -> コンテンツ登録・編集
                                    if($page_config['contents_list'] == 1) {
                                        echo "　コンテンツ登録・編集：　　　有効<br/>";
                                    //  echo "　コンテンツ登録・編集：　　　<input type='radio' class='radio' name='contents_list' value='0'>無効　　<input type='radio' class='radio' name='contents_list' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　コンテンツ登録・編集：　　　無効<br/>";
                                    //  echo "　コンテンツ登録・編集：　　　<input type='radio' class='radio' name='contents_list' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='contents_list' value='1'>有効<br/>";
                                    }

                                    //受講対象設定
                                    if($page_config['contents_control'] == 1) {
                                        echo "　　　　　受講対象設定：　　　有効<br/>";
                                    //  echo "　　　　　受講対象設定：　　　<input type='radio' class='radio' name='contents_control' value='0'>無効　　<input type='radio' class='radio' name='contents_control' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　　受講対象設定：　　　無効<br/>";
                                    //  echo "　　　　　受講対象設定：　　　<input type='radio' class='radio' name='contents_control' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='contents_control' value='1'>有効<br/>";
                                    }

                                    //受講状況 -> 受講者から確認
                                    if($page_config['history'] == 1) {
                                        echo "　　　　受講者から確認：　　　有効<br/>";
                                    //  echo "　　　　受講者から確認：　　　<input type='radio' class='radio' name='history' value='0'>無効　　<input type='radio' class='radio' name='history' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　受講者から確認：　　　無効<br/>";
                                    //  echo "　　　　受講者から確認：　　　<input type='radio' class='radio' name='history' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='history' value='1'>有効<br/>";
                                    }

                                    //受講状況 -> 動画授業から確認
                                    if($page_config['viewing_wise'] == 1) {
                                        echo "　　　動画授業から確認：　　　有効<br/>";
                                    //  echo "　　　動画授業から確認：　　　<input type='radio' class='radio' name='viewing_wise' value='0'>無効　　<input type='radio' class='radio' name='viewing_wise' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　動画授業から確認：　　　無効<br/>";
                                    //  echo "　　　動画授業から確認：　　　<input type='radio' class='radio' name='viewing_wise' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='viewing_wise' value='1'>有効<br/>";
                                    }

                                    //メッセージ
                                    if($page_config['message'] == 1) {
                                        echo "　　　　　　メッセージ：　　　有効<br/>";
                                    //  echo "　　　　　　メッセージ：　　　<input type='radio' class='radio' name='message' value='0'>無効　　<input type='radio' class='radio' name='message' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　　　メッセージ：　　　無効<br/>";
                                    //  echo "　　　　　　メッセージ：　　　<input type='radio' class='radio' name='message' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='message' value='1'>有効<br/>";
                                    }

                                    //ヘルプ
                                    if($page_config['help'] == 1) {
                                        echo "　　　　　　　　ヘルプ：　　　有効<br/>";
                                    //  echo "　　　　　　　　ヘルプ：　　　<input type='radio' class='radio' name='help' value='0'>無効　　<input type='radio' class='radio' name='help' value='1' checked='checked'>有効<br/>";
                                    } else {
                                        echo "　　　　　　　　ヘルプ：　　　無効<br/>";
                                    //  echo "　　　　　　　　ヘルプ：　　　<input type='radio' class='radio' name='help' value='0' checked='checked'>無効　　<input type='radio' class='radio' name='help' value='1'>有効<br/>";
                                    }

                                //  echo "
                                //      <dl>
                                //          <button class='save'>保 存</button>
                                //          <input type='hidden' name='config_change_flag' value='1' />
                                //          <input type='hidden' name='file_path' value='" . $file_path . "' />
                                //      </dl>
                                //  ";
                                } else {
                                    echo "　　　　　page_config.phpが読み込めませんでした<br/>";
                                }
                                echo "</form>"
                            ?>
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
