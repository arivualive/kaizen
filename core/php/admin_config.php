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
//print_r( $_SESSION );
//学校ID
if(isset($_GET['s_id'])){
    $data['school_id'] = $_GET['s_id'];
} else {
    $data['school_id'] = 0;
}

//管理者ID
if(isset($_GET['a_id'])){
    $data['admin_id'] = $_GET['a_id'];
} else {
    $data['admin_id'] = 0;
}

$curl = new Curl($url);
$schoolInfo = new SchoolConfig($curl);
$adminInfo = new AdminConfig($curl);
/*
$test = 'return_config';
$re = $adminInfo->return_test($test);

*/
print_r( $data );
//保存ボタン押下
if(isset($_POST['insert_flag'])) {
    $data['admin_name'] = $_POST['admin_name'];
    $data['school_id'] = $_POST['school_id'];
    $data['call_sign'] = $_POST['call_sign'];
    $data['id'] = $_POST['id'];
    $data['password'] = $_POST['password'];
    $data['enable'] = $_POST['enable'];
    $data['display_order'] = $_POST['display_order'];
    $data['manage'] = $_POST['manage'];
    $data['permission'] = $_POST['permission'];
    $data['bit_subject'] = $_POST['bit_subject'];
    if($data['admin_id'] != 0) {
        $adminInfo->changeAdmin($data, 'update');
    } else {
        $adminInfo->changeAdmin($data, 'insert');
    }
}
//print_r( $data );
//print_r( $adr );

//School Data(学校データ-一覧)
$school_data = $schoolInfo->selectSchool('', 'list');

//List Data(管理者データ-一覧)
$list_data = $adminInfo->selectAdmin($data, 'list_all');

//CS Data(学校データ-識別コード)
$call_sign_data = $schoolInfo->selectSchool($data, 'call_sign')['call_sign'];

//Count Data(管理者データ-レコード数)
$count_data = $adminInfo->selectAdmin('', 'count_all');

//Person Data(管理者データ-詳細)
if($data['admin_id'] != 0) {
    $person_data = $adminInfo->selectAdmin($data, 'person');
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
        div.adminlist {
            float: left;
            width: 33.33%;
        }
        table.adminlist {
            margin-left:auto;
            margin-right:auto;
        }
        table.adminlist td {
            width: 220px;
        }
        div.schooldata {
            float: left;
            width: 33.33%;
            text-align:left;
        }
        div.schooldata .text {
            width: 300px;
        }
        div.schooldata .save {
            margin-left: 160px;
        }
        .js-link {
            color: #0000FF;
            text-decoration: underline;
        }
    </style>
    <script>
        $(function(){
            $('.schoollist').find('.name').on('click', function () {
                $scrool_value = $('.userlist').scrollTop();
                if($(this).siblings('.id').text() != '') {
                    window.location.href = './admin_config.php?s_id=' + $(this).siblings('.id').text();
                } else {
                    window.location.href = './admin_config.php';
                }
            });
            $('.adminlist').find('.name').on('click', function () {
                $scrool_value = $('.userlist').scrollTop();
                if($(this).siblings('.id').text() != '') {
                    window.location.href = './admin_config.php?s_id=' + "<?php echo $data['school_id']; ?>" + '&a_id=' + $(this).siblings('.id').text();
                } else {
                    window.location.href = './admin_config.php';
                }
            });
            $('.adminlist').find('.new').on('click', function () {
                $scrool_value = $('.userlist').scrollTop();
                window.location.href = './admin_config.php?s_id=' + "<?php echo $data['school_id']; ?>";
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
                        <td>学校名</td>
                    </tr>
                    <?php foreach ((array)$school_data as $items): ?>
                        <tr class='listvalue'>
                            <td class="id" style="display: none;"><?php echo $items['school_id']; ?></td>
                            <td class="name js-link"><?php echo $items['school_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="adminlist">
                <table class="adminlist">
                    <tr class='listvalue'>
                        <td>管理者名(admin_name)</td>
                    </tr>
                    <?php if($data['school_id'] != 0) { ?>
                        <tr class='listvalue'>
                            <td class="new js-link">新規登録</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ((array)$list_data as $items): ?>
                        <tr class='listvalue'>
                            <td class="id" style="display: none;"><?php echo $items['admin_id']; ?></td>
                            <td class="name js-link"><?php echo $items['admin_name']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <div class="schooldata">
                <div class="tab-pane fade show active" id="new-regist" role="tabpanel">
                    <?php
                        if($data['admin_id'] != '') {
                            echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('受講者情報を変更します。よろしいですか？')\">";
                        } else {
                            echo "<form action='" . $_SERVER['REQUEST_URI'] . "' method='POST' onsubmit=\"return submitCheck('受講者情報を登録します。よろしいですか？')\">";
                        }
                    ?>
                        <!-- 管理者名 -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                　管理者名：<input type="text" class="text" maxlength="250" name="admin_name" value="<?php echo $person_data['admin_name'] ?>">
                            <?php } else { ?>
                                　管理者名：<input type="text" class="text" maxlength="250" name="admin_name">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['admin_name']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_admin_name'>管理者名が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 学校ID -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                　学校ＩＤ：<input type="text" class="text" maxlength="10" name="school_id" readonly="readonly" value="<?php echo $person_data['school_id'] ?>">
                            <?php } else { ?>
                                　学校ＩＤ：<input type="text" class="text" maxlength="10" name="school_id" readonly="readonly" value="<?php echo $data['school_id'] ?>">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['school_id']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_school_id'>学校IDが入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- ID -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                管理者ＩＤ：<input type="text" class="text" maxlength="250" name="id" value="<?php echo $person_data['id'] ?>">
                            <?php } else { ?>
                                管理者ＩＤ：<input type="text" class="text" maxlength="250" name="id">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['id']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_id'>学校IDが入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- PW -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                パスワード：<input type="text" class="text" maxlength="250" name="password" value="<?php echo $person_data['password'] ?>">
                            <?php } else { ?>
                                パスワード：<input type="text" class="text" maxlength="250" name="password">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['password']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_password'>学校IDが入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- 所属 -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                所属：<input type="text" class="text" maxlength="250" name="bit_subject" value="<?php echo $person_data['bit_subject']; ?>">
                            <?php } else { ?>
                                所属：<input type="text" class="text" maxlength="250" name="bit_subject" value="<?php echo "0"; ?>">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['permission']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_bit_subject'>所属が入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- パーミッション -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                パーミッション：<input type="text" class="text" maxlength="250" name="permission" value="<?php echo $person_data['permission']; ?>">
                            <?php } else { ?>
                                パーミッション：<input type="text" class="text" maxlength="250" name="permission" value="<?php echo "FFFF"; ?>">
                            <?php } ?>
                            </dd>
                            <?php
                                if(!isset($data['permission']) && filter_input(INPUT_POST, "insert_flag")) {
                                    echo "<p class='attention' id='not_permission'>パーミッションが入力されていません</p>";
                                }
                            ?>
                        </dl>
                        <!-- マネージ機能 -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                <?php if($person_data['manage'] == 1) { ?>
                                    　マネージ機能：　　　　<input type="radio" class="radio" name="manage" value="0">なし　　　<input type="radio" class="radio" name="manage" value="1" checked="checked">あり
                                <?php } else { ?>
                                    　マネージ機能：　　　　<input type="radio" class="radio" name="manage" value="0" checked="checked">なし　　　<input type="radio" class="radio" name="manage" value="1">あり
                                <?php } ?>
                            <?php } else { ?>
                                　マネージ機能：　　　　<input type="radio" class="radio" name="manage" value="0" checked="checked">なし　　　<input type="radio" class="radio" name="manage" value="1">あり
                            <?php } ?>
                            </dd>
                        </dl>
                         <!-- 有効・無効 -->
                        <dl>
                            <dd>
                            <?php if($data['admin_id'] != 0) { ?>
                                <?php if($person_data['enable'] == 1) { ?>
                                    　稼働状態：　　　　<input type="radio" class="radio" name="enable" value="0">無効　　　<input type="radio" class="radio" name="enable" value="1" checked="checked">有効
                                <?php } else { ?>
                                    　稼働状態：　　　　<input type="radio" class="radio" name="enable" value="0" checked="checked">無効　　　<input type="radio" class="radio" name="enable" value="1">有効
                                <?php } ?>
                            <?php } else { ?>
                                　稼働状態：　　　　<input type="radio" class="radio" name="enable" value="0" checked="checked">無効　　　<input type="radio" class="radio" name="enable" value="1">有効
                            <?php } ?>
                            </dd>
                        </dl>
                        <!-- 並び順 -->
                        <?php if($data['admin_id'] != 0) { ?>
                            <input type="hidden" name="display_order" value="<?php echo $person_data['display_order'] ?>">
                        <?php } else { ?>
                            <input type="hidden" name="display_order" value="<?php echo ($count_data + 1) ?>">
                        <?php } ?>
                        <!-- 識別コード -->
                        <?php if($data['school_id'] != 0) { ?>
                            <input type="hidden" name="call_sign" value="<?php echo $call_sign_data ?>">
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
        </div>
    </div>
</div>
</body>
</html>
