<?php
require_once "../../config.php";
require_once "../../library/permission.php";

//debug($_SESSION);
//debug($_GET);
//debug($_POST);
//debug($_FILES);

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

if (!$isManager && !isPermissionFlagOn($permission, "1-8")) {
    $_SESSION = array(); //全てのセッション変数を削除
    setcookie(session_name(), '', time() - 3600, '/'); //クッキーを削除
    session_destroy(); //セッションを破棄
    
    header('Location: ../auth/index.php');
    exit();
}

// 初期値
$selection_default_cnt = 5; // 選択肢のデフォルト数
$error_message = array();
$disabled = '';
$selection = array();

$error_message = array();
$error_p = '';
$disabled = '';

function validation() {
    $error_message = array();

    // バリデーションチェック
    if ($_POST['query_text'] == '') {
        $error_message['query_text'] = '問題文を入力してください。';
    }

    $error = 0;
    foreach ($_POST['text'] as $value) {
        if ($value != '') {
            $error++;
        }
    }

    if ($error == 0) {
        $error_message['text'] = '選択肢を入力してください。';
    }

    if (! in_array(1, $_POST['correct'])) {
        $error_message['correct'] = '正解の選択肢を指定してください。';
    }

    return $error_message;
}

// curlのインスタンス化
$curl = new Curl($url);

// request
$quiz_id = filter_input(INPUT_GET, "id");
$p = filter_input(INPUT_GET, "p");
$bid = filter_input(INPUT_GET, "bid");

// インスタンス化
$Quiz = new Quiz($quiz_id, $curl);
$QueryObj = new Query($quiz_id, $curl);
#$SelectionObj = new QuerySelection($selection_default_cnt, $QueryObj, $curl);
$SelectionObj = new QuerySelection($quiz_id, $curl);

// tbl_quiz_query にquiz_idが無い場合はinsertする
if ($quiz_id != ''){
    $query = $QueryObj->getQuery();

    if (count($query) == 0) {
        $display_order = 0;
        $query_id = $QueryObj->insertQuery($quiz_id, $display_order);
    }
}

// 問題の取得
$query_data = $QueryObj->getQuery();

//debug($query_data);
// 2019/6/03 count関数対策
$p_max = 0;
if(is_countable($query_data)){
  $p_max = count($query_data);
}
//$p_max = count($query_data);


function save($QueryObj, $SelectionObj){
    $data['query_id'] = filter_input(INPUT_POST, "query_id");
    $data['query_text'] = filter_input(INPUT_POST, "query_text", FILTER_SANITIZE_SPECIAL_CHARS);
    $data['score'] = filter_input(INPUT_POST, "score");
    $data['description'] = filter_input(INPUT_POST, "description", FILTER_SANITIZE_SPECIAL_CHARS);


    // 更新
    $QueryObj->updateQuery($data);

    // 選択肢の保存
    $SelectionObj->saveSelection($data['query_id']);

    $correct_cnt = $SelectionObj->countCorrect($data['query_id']);

    if ($correct_cnt['count_correct'] >= 2) {
        $post_data['query_id'] = $data['query_id'];
        $post_data['query_type'] = 1;
    } else {
        $post_data['query_id'] = $data['query_id'];
        $post_data['query_type'] = 0;
    }

    $QueryObj->updateQueryType($post_data);

    // image file upload
    $QueryObj->setQueryId($data['query_id']);
    $QueryObj->imageFileUpload();

}


if (filter_input(INPUT_POST, "submit") == 'prev') {
    save($QueryObj, $SelectionObj);

    if ($p > 0) {
        $p = $p - 1;
    }

    $Quiz->redirect("query.php?id=$quiz_id&p=$p&bid=$bid");
}

if (filter_input(INPUT_POST, "submit") == 'next') {
    save($QueryObj, $SelectionObj);

    $p = $p + 1;

    if ($p_max == $p) {
        $p = $p - 1;
    }

    $Quiz->redirect("query.php?id=$quiz_id&p=$p&bid=$bid");
}

if (filter_input(INPUT_POST, "submit") == 'confirm') {
    save($QueryObj, $SelectionObj);

    // すべての問題をチェック
    $query_data = $QueryObj->getQuery();

    foreach ($query_data as $key => $value) {
        if ($value['query_text'] == '') {
            $error_p = $key;
            $error_message['query_text'] = sprintf('問%dの問題文を入力してください。', $error_p + 1);
        }

        // 選択肢の正解フラグ漏れをチェック
        $sum = 0;
        foreach ($SelectionObj->getSelection($value['query_id']) as $item) {
            $sum += (int) $item['correct_flg'];
            $correct[$key] = $sum;
        }
    }

    if (in_array(0, $correct)) {
        $error_p = array_search(0, $correct);
        $error_message['correct'] = sprintf('問%dの正解を指定してください。', $error_p + 1);
    }

    if (count($error_message) > 0) {
        // 確認ボタンを押せなくする
        $disabled = ' disabled';
    } else {
        $Quiz->redirect("confirm.php?id=$quiz_id&bid=$bid");
    }
}

// quiz_queryの保存
if (filter_input(INPUT_POST, "submit") == 'save') {
    save($QueryObj, $SelectionObj, $p);

    $error_message = validation();

    if (count($error_message) > 0) {
        // 確認ボタンを押せなくする
        $disabled = ' disabled';
    }

}

$status = filter_input(INPUT_GET, "status");

if ($error_p == '') {
    $p = filter_input(INPUT_GET, "p");
    if ($p == '' && empty($status)) die ('問題番号が不明です。');
} else {
    $p = $error_p;
}

$page = $p + 1;
$nav_title = sprintf("第%d問", $page);

// query_idの取得
$query_id = $query_data[$p]['query_id'];

/*
if ($query_data) {

    if ($p_max > $p) {
        $p = $p_max - 1;
    }
}
*/

// ナビゲーション
$nav = $QueryObj->makeQueryNav();
// 2019/6/03 count関数対策
$last = 0;
if(is_countable($nav)){
  $last = count($nav);
}
//$last = count($nav);

// 問題を追加する場合
if (filter_input(INPUT_POST, "submit") == 'add') {
    save($QueryObj, $SelectionObj, $p);

#    $query_data = $QueryObj->getQuery();
    // 2019/6/03 count関数対策
    $display_order = 0;
    if(is_countable($query_data)){
      $display_order = count($query_data) + 1;
    }
    //$display_order = count($query_data) + 1;

    // 新規のレコードを作成しquery_idを得る
    $query_id = $QueryObj->insertQuery($quiz_id, $display_order);
    //debug($query_id);

    // 5つ空の選択肢を作成しselection_idを取得
    for ($i = 0; $i < $selection_default_cnt; $i++) {
        $selection[] = $SelectionObj->insertSelectionNoData($query_id, $i);
    }

    // 追加した問いをリダイレクト表示
    header("location: query.php?id=$quiz_id&p=$last&bid=$bid");
    exit();
}


// フォローコンテンツ
$FollowObj = new QueryFollow($query_id, $Quiz, $curl);

// 削除の場合
if (filter_input(INPUT_POST, "submit") == 'delete') {
    $error_message = array();
    $disabled = '';

    $query_id = filter_input(INPUT_POST, "query_id");

    $error_message = validation();

    if (count($error_message) > 0) {
        // 確認ボタンを押せなくする
        $disabled = ' disabled';
    }

    // 無効フラグ設定
    $QueryObj->removeQuery($query_id);
    $SelectionObj->removeQuerySelection($query_id);

    // 問1を表示
    header("location: query.php?id=$quiz_id&p=0&bid=$bid");
    exit();
}


# viewの表示


// 画像の取消
if (filter_input(INPUT_POST, "image_remove") == 'image_remove') {
    // quiz_queryをnullでupdate
    $QueryObj->removeImageFile($query_id);
}

// 音声の取消
/*
if (filter_input(INPUT_POST, "sound_remove") == 'sound_remove') {
    // quiz_queryをnullでupdate
    $QueryObj->removeSoundFile($query_id);
}
*/

// 現在の問題数
$count_query = $QueryObj->countQuery();


// 選択肢
if (isset($query_id) && $query_id > 0) {
    $selection = $SelectionObj->makeSelectionInput($query_id);
}

//debug($selection);

// 関連コンテンツ
$subject_section = $FollowObj->followContents();
//debug($subject_section);

// 問題の情報
$query_item = $QueryObj->getQueryInfo($query_id);
//debug($query_item);

// path
$path = $query_item['quiz_id'] . '_' . $query_item['query_id'] . '.deploy';

// 画像ファイルの取得
$image = $query_item['image_file_name'];
$image_file_path = $url2 . 'file/image/' . $path;

// 音声ファイルの取得
#$sound = $query_item['sound_file_name'];
#$sound_file_path = $url2 . 'file/sound/' . $path;

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
    <link rel="stylesheet" type="text/css" href="../css/quiz.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>

<style>
article, aside, dialog, figure, footer, header,
hgroup, menu, nav, section { display: block; }
td.sort {text-align: center;}
td.sort:hover {cursor: move; text-align: center;}
img {
    margin:0 5px 5px 0;
    max-width:200px;
    vertical-align:bottom;
}
</style>
<script src="../../js/jquery-1.10.2.js"></script>
<script src="../../js/jquery-ui-1.10.4.custom.js"></script>
<script src="../../js/jquery.upload_thumbs.js"></script>
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
                    <li class="open">
                        <a class="togglebtn"><span class="icon-movie-manage"></span>コンテンツ設定</a>
                        <ul class="togglemenu open">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">コンテンツグループ設定</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php" class="active">コンテンツ登録・編集</a></li>
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
                    <li>
                        <a class="togglebtn"><span class="icon-graph"></span>受講状況</a>
                        <ul class="togglemenu">
                            <li><a href="../history/index.php">受講者から確認</a></li>
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
                <li>コンテンツ設定</li>
                <li><a href='../contents/index.php?bid=<?php echo $bid ?>'>コンテンツ登録・編集</a></li>
                <li class="active"><a>テスト作成</a></li>
            </ol>
        </div>
        <!-- ▼user information -->
        <div id="userinfo" class="button-dropdown">
            <a class="link" href="javascript:void(0)">
                <div class="erea-image"></div>
                <div class="erea-name">
                    <p class="authority">学校管理者</p>
                    <p class="username"><?php echo $_SESSION['auth']['admin_name']; ?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="../account/index.php"><span class="icon-lock"></span>アカウント設定</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-out"></span>ログアウト</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>テスト作成</h2>
        </div>
        <!-- ▲h2 -->

        <!-- progress -->
        <div id="progress">
            <ul class="clearfix">
                <li class="active">
                    <span class="text">基本設定</span>
                    <span class="circle"></span>
                </li>
                <li class="active">
                    <span class="text">問題作成</span>
                    <span class="circle"></span>
                </li>
                <li>
                    <span class="text">内容確認</span>
                    <span class="circle"></span>
                </li>
            </ul>
        </div>

        <form action="" method="post" enctype="multipart/form-data">
        <div id="col-item-control" class="clearfix">

            <!-- 各問題 -->
            <div id="col-quizlist">
                <div class="in">
                    <ul id="nav-list">
                    <?php foreach ((array) $nav as $key => $item): ?>
                        <li id="sortitem_<?php echo $item['query_id']; ?>">
                            <p class="handle active">
                                問 <span class="number"><?php echo $key+1; ?></span>
                                <span class="title"><?php echo $item['title']; ?></span>
                            </p></li>

                    <?php endforeach; ?>
                    <!-- アクティブ時は<a class="active"> -->
                    </ul>
                    <!-- <a class="btn-add" href="query.php?id=<?php echo $quiz_id; ?>&status=add&bid=<?php echo $bid; ?>">問題を追加</a>  -->
                    <!-- <button class="btn-add" type="submit" name="submit" id="submit" value="add">問題を追加</button> -->
                    <!-- <p class="btn-save"><button type="submit" name="submit" id="submit" value="save">この問題を保存</button></p> -->
                </div>
            </div>

            <!-- 情報入力 -->
            <div id="col-inputcontents">
                <p><?php echo $nav_title; ?></p>
                <!-- 必須情報 -->
                <div class="box-required">

                   <!-- 問題文 -->
                   <dl class="input-group">
                       <dt>問題文<span class="text_limit">250文字以内</span></dt>
                       <dd><textarea rows="5" name="query_text"><?php echo $query_item['query_text'];?></textarea></dd>
                       <p class="attention"><?php echo (! empty($error_message['query_text'])) ? $error_message['query_text'] : ''; ?> </p>
                   </dl>

                   <!-- 配点 -->
                   <dl class="input-group">
                       <dt>配点</dt>
                       <dd><input type="number" min="1" max="100" name="score" value="<?php echo isset($query_item['score']) ? $query_item['score'] : '10'; ?>"> 点</dd>
                   </dl>

                   <!-- 選択肢 -->
                   <dl class="input-group">
                       <dt>選択肢</dt>
                       <dd>
                           <table class="select-answer">
                               <thead>
                               <tr>
                                   <th class="move"></th>
                                   <th class="correct">正解</th>
                                   <th class="answer">解答</th>
                                   <th class="delete">削除</th>
                               </tr>
                               </thead>
                               <tbody id="querydata">
                               <?php $j=0;?>
                               <?php foreach ($selection as $i => $item): ?>
                               <?php $j++;?>
                               <tr>
                                   <td class="move"></td>
                                   <td class="correct"><label for="lbl<?php echo $j;?>" class="checkbox"><input type="hidden" name="correct[<?php echo $i; ?>]" value="0"/>
                                            <input id="lbl<?php echo $j;?>" type="checkbox" name="correct[<?php echo $i; ?>]" value="1"<?php echo $item['checked']; ?> /><span class="icon"></span></label></td>
                                   <td class="answer"><input type="text" name="text[<?php echo $i; ?>]" value="<?php echo $item['text']; ?>" /></td>
                                   <input type="hidden" name="selection_id[<?php echo $i; ?>]" value="<?php echo $item['selection_id']; ?>" />
                                   <td class="delete"><button type="button" class="rowRemove" value="削除"><span class="icon-cross"></span></button></td>
                               </tr>
                               <?php endforeach; ?>
                               </tbody>
                           </table>
                           <p><button type="button" id="rowAdd" class="btn-add">選択肢の追加</button></p>
                       </dd>
                       <p class="attention"><?php echo (! empty($error_message['text'])) ? $error_message['text'] : ''; ?> </p>
                       <p class="attention"><?php echo (! empty($error_message['correct'])) ? $error_message['correct'] : ''; ?> </p>
                   </dl>
                </div>

                <!-- ファイル -->
                <div class="box-accordion">
                    <p class="title clearfix"><span>参考ファイル</span></p>
                    <div class="in">
                        <button class="navbar-toggler collapsed query-edit" type="button" data-toggle="collapse" data-target="#query-file" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">編集</button>
                        <div class="contents clearfix navbar-collapse collapse" id="query-file">

                            <!-- 画像 -->
                            <div class="w-50">
                                <dl class="input-group">
                                <?php if (file_exists($image_file_path) || $image != ''): ?>
                                <img class="thumbs" src="<?php echo $image_file_path; ?>" alt="画像" />
                                <p>ファイル名： <?php echo $image; ?></p>
                                <?php endif; ?>
                                    <dt>画像<button type="submit" name="image_remove" value="image_remove">取消</button></dt>
                                    <dd><input type="file" name="images[0]" accept="image/*"></dd>
                                </dl>
                            </div>

                            <!-- 音声
                            <div class="w-50">
                                <dl class="input-group">
                                <?php //if (file_exists($sound_file_path) || $sound != ''): ?>
                                <audio controls><source src="<?php //echo $sound_file_path; ?>"></audio>
                                <p>ファイル名： <?php //echo $sound; ?></p>
                                <?php //endif; ?>
                                    <dt>音声<button type="submit" name="sound_remove" value="sound_remove">取消</button></dt>
                                    <dd><input type="file" name="sound" accept="audio/*"></dd>
                                </dl>
                            </div>
                            -->

                        </div>
                    </div>
                </div>

                <!-- 不正解時の解説 -->
                <div class="box-accordion">
                    <p class="title clearfix"><span>不正解時の解説</span></p>
                    <div class="in">
                        <button class="navbar-toggler collapsed query-edit" type="button" data-toggle="collapse" data-target="#query-incorrect-explanation" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">編集</button>

                        <div class="contents navbar-collapse collapse" id="query-incorrect-explanation">
                            <div class="w-100">
                                <!-- 解説文 -->
                                <dl class="input-group">
                                    <dt>解説文<span class="text_limit">250文字以内</span></dt>
                                    <dd><textarea rows="5" name="description"><?php echo $query_item['description']; ?></textarea></dd>
                                </dl>
                                <!-- コンテンツ -->
                                <!--
                                <dl class="input-group">
                                    <dt>コンテンツ</dt>
                                    <dd>
                                        <table class="incorrect-explanation">
                                        <?php foreach ((array) $subject_section as $item): ?>
                                            <tr>
                                              <td><label class="checkbox"><input type="checkbox" name="contents[]" value="<?php echo $item['contents_id']; ?>" <?php echo $item['checked']; ?>/><span class="icon"></span></label></td>
                                              <td><?php echo $item['subject_section_name']; ?></td>
                                              <td><?php echo $item['contents_name']; ?></td>
                                            <tr>
                                        <?php endforeach; ?>

                                            <tr>
                                                <td><label class="checkbox"><input type="radio"><span class="icon"></span></label></td>
                                                <td>関連コンテンツ</td>
                                            </tr>
                                            <tr>
                                                <td><label class="checkbox"><input type="radio"><span class="icon"></span></label></td>
                                                <td>関連コンテンツ</td>
                                            </tr>
                                            <tr>
                                                <td><label class="checkbox"><input type="radio"><span class="icon"></span></label></td>
                                                <td>関連コンテンツ</td>
                                            </tr>
                                            <tr>
                                                <td><label class="checkbox"><input type="radio"><span class="icon"></span></label></td>
                                                <td>関連コンテンツ</td>
                                            </tr>
                                            <tr>
                                                <td><label class="checkbox"><input type="radio"><span class="icon"></span></label></td>
                                                <td>関連コンテンツ</td>
                                            </tr>
                                        </table>
                                    </dd>
                                </dl>
                                -->
                            </div>
		                </div>
					</div>
				</div>

<input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>" />
<input type="hidden" name="query_id" value="<?php echo $query_id; ?>" />
<input type="hidden" name="status" value="<?php echo $status; ?>" />
<input type="hidden" name="p" value="<?php echo $p; ?>" />

	            <!-- 問題削除 -->
		        <!-- <div class="box-delete">
		            <a href="query.php?id=<?php echo $quiz_id; ?>&qn=<?php echo $query_id; ?>&status=disable&bid=<?php echo $bid; ?>">この問題を削除</a>
		        </div> -->

		        <div class="box-controlbtns">
		        	<ul>
		        		<li>
		        			<button type="submit" name="submit" value="prev" class="prev">前へ</button>
		        		</li>
		        		<li> <?php echo $page; ?> / <?php echo $last; ?> </li><!-- 現在の問 / 全問題数 -->
						<li>
		        			<button type="submit" name="submit" value="next" class="next">次へ</button>
		        		</li>
		        	</ul>
		        	<ul>
		        		<li>
		        			<button type="submit" name="submit" value="delete" class="delete">問を削除</button>
		        		</li>
						<li>
		        			<button type="submit" name="submit" value="add" class="add">問を追加</button>
		        		</li>
		        		<li>
		        			<button type="submit" name="submit" value="save" class="save">問を保存</button>
		        		</li>
		        	</ul>
		        </div>
			</div>
        </div>

        <!-- 保存 -->
        <div id="col-mainbtn" class="clearfix">
            <ul class="clearfix">
              <li class="save"><button type="submit" name="submit" id="submit" value="confirm"<?php echo $disabled; ?>>内容確認</button></li>
            </ul>
        </div>
        </form>
    </div>
    <!-- ▲main -->
</div>

<script type="text/javascript">
    $(document).ready(function() {
            $("#nav-list").sortable({
                handle : '.handle',
                update : function () {
                    var order = $('#nav-list').sortable('serialize');
                    $("#info").load("nav_sortable.php?"+order);
                }
            });
    });
</script>
<script type="text/javascript">
$('#rowAdd').on('click', function() {
        $('#querydata').append('<tr><td class="move"></td><td class="correct"><label class="checkbox"><input type="hidden" name="correct[<?php echo $i+1; ?>]" value="0"/><input type="checkbox" name="correct[<?php echo $i+1; ?>]" value="1"<?php echo $item['checked']; ?> /><span class="icon"></span></label></td><td class="answer"><input type="text" name="text[<?php echo $i+1; ?>]" value="" /></td><td class="delete"><button type="button" class="rowRemove" name="remove" value="削除"><span class="icon-cross"></span></button></td></tr>');
        });
//追加したボタンにもイベントを適用させるため、onイベント内にクラス名を記述する
$(document).on('click', '.rowRemove', function() {
        $(this).parent().parent().remove();
        });
</script>
<script>
//$(function() {
//        $('#querydata') . sortable();
//        $('#querydata') . disableSelection();
//        });
</script>

<script>
//$(function() {
//        $('#jquery-ui-sortable') . sortable();
//        $('#jquery-ui-sortable') . disableSelection();
//        });
</script>
<script>
$(function() {
        // jQuery Upload Thumbs (https://www.dekasu.net/upload_thumbs/)
        $('form input:file').uploadThumbs({
position : 0,      // 0:before, 1:after, 2:parent.prepend, 3:parent.append,
// any: arbitrarily jquery selector
imgbreak : true    // append <br> after thumbnail images
});
        });
</script>

<pre> <div id="info"></div> </pre>

</body>
</html>
