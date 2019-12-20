<?php
error_reporting(~E_NOTICE);
require_once "../../config.php";
require_once "../../library/permission.php";
//login_check('/admin/auth/');

$isManager = isset($_SESSION['auth']['manage']) ? $_SESSION['auth']['manage'] : 0;
$permission = isset($_SESSION['auth']['permission']) ? $_SESSION['auth']['permission'] : 0;

$curl = new Curl($url);

if (filter_input(INPUT_GET, "id")) {
    $student_data[ 'contents_id' ] = filter_input(INPUT_GET, "id");
}
if (filter_input(INPUT_GET, "st")) {
    $student_data[ 'student_id' ]  = filter_input(INPUT_GET, "st");
}
if (filter_input(INPUT_GET, "bid")) {
  $student_data[ 'bid' ]           = filter_input(INPUT_GET, "bid");
}
if (filter_input(INPUT_GET, "ca")) {
  $student_data[ 'category' ]      = filter_input(INPUT_GET, "ca");
}
if (filter_input(INPUT_GET, "se")) {
  $student_data[ 'section' ]       = filter_input(INPUT_GET, "se");
}
if (filter_input(INPUT_GET, "ti")) {
  $student_data[ 'title' ]         = filter_input(INPUT_GET, "ti");
}
if (filter_input(INPUT_GET, "na")) {
  $student_data[ 'name' ]          = filter_input(INPUT_GET, "na");
}

//debug( $student_data );

function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>ThinkBoard LMS Administrator</title>
    <!-- favicon -->
	<link rel="shortcut icon" href="../images/favicon.ico">
	<!-- css -->
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
	<link rel="stylesheet" type="text/css" href="../css/icon-font.css">
	<link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../../css/history.css">
    <link rel="stylesheet" type="text/css" href="../../css/student_viewing.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/jquery-ui.min.js"></script>

    <!--<link rel="stylesheet" href="../../css/history.css">
    <script type="text/javascript" src="../../js/tabulator-master/dist/js/tabulator.js"></script>
    <link rel="stylesheet" href="../../js/tabulator-master/dist/css/tabulator.css">-->

    <script src="../../js/popper.min.js"></script>
    <script src="../js/script.js"></script>
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
            font-size: 11px;
        }

        .record_table {
            width: 100%;
            border-collapse: collapse;
        }

        .record_table tr:hover {
            background: #eee;
        }

        .record_table td {
            border: 1px solid #eee;
        }
    </style>
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
                    <p class="authority">For administrators</p>
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
                        <a class="togglebtn"><span class="icon-user-add"></span>Student affiliation / ID setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/users.php">Affiliation group setting</a></li>
                            <?php } ?>
                            <!--<li><a href="#">講師ID設定</a></li>-->
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1000")) { ?>
                            <li><a href="../user/student.php">Student ID setting</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-1", "1-2000")) { ?>
                    <li>
                        <a class="togglebtn"><span class="icon-movie-manage"></span>Content setting</a>
                        <ul class="togglemenu">
                            <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                            <li><a href="../access/contents.php">Content group setting</a></li>
                            <?php } ?>
                            <?php if ($isManager || isPermissionFlagOnArray($permission, "1-2000")) { ?>
                            <li><a href="../contents/index.php">Content registration / editing</a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-1")) { ?>
                    <li>
                        <a href="../access/contents-control.php"><span class="icon-movie-set"></span>Target setting</a>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOn($permission, "1-20")) { ?>
                    <li class="open">
                        <a class="togglebtn"><span class="icon-graph"></span>Attendance status</a>
                        <ul class="togglemenu open">
                            <li><a href="../history/index.php" class="active">Confirmation from the student</a></li>
                            <!--<li><a href="dateWiseViewing/index.php">動画授業から確認</a></li>-->
                        </ul>
                    </li>
                    <?php } ?>
                    <?php if ($isManager || isPermissionFlagOnArray($permission, "1-4000")) { ?>
                    <li>
                        <a href="../message/message_list.php"><span class="icon-main-message"></span>message</a>
                    </li>
                    <?php } ?>
                    <li>
                        <a href="../help/TBLMS_Administrator.pdf" target="_blank"><span class="icon-hatena"></span>help</a>
                    </li>
                    <?php if ($isManager) { ?>
                    <li>
                        <a href="../user/admin.php"><span class="icon-user-add"></span>Administrator ID, authority setting</a>
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
                <li>Attendance status</li>
                <li><a href="../history/index.php">Confirmation from the student</a></li>
                <li class="active"><a>Viewing graph</a></li>
            </ol>
        </div>
        <!-- ▼user information -->
        <div id="userinfo" class="button-dropdown">
            <a class="link" href="javascript:void(0)">
                <div class="erea-image"></div>
                <div class="erea-name">
                    <p class="authority">School Admin</p>
                    <p class="username"><?php echo $_SESSION[ 'auth' ][ 'admin_name' ];?></p>
                </div>
            </a>
            <ul class="submenu">
                <li role="presentation"><a href="#"><span class="icon-lock"></span>Change Password</a></li>
                <li role="presentation"><a href="../auth/logout.php"><span class="icon-sign-in"></span>Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- ▲header -->

    <!-- ▼main-->
    <div id="maincontents">

        <!-- ▼h2 -->
        <div class="h2">
            <h2>Viewing graph</h2>
        </div>
        <!-- ▲h2 -->

        <!-- ▼main system -->
        <div>

            <div class="col-viewing-mainingfo clearfix">

                <!-- 受講者情報 -->
                <div class="each-information student">
                    <h3>Student Information</h3>
                    <div class="in">
                        <dl>
                            <dt>Name</dt>
                            <dd><?php echo $student_data[ 'name' ]; ?></dd>
                        </dl>
                    </div>
                </div>

                <!-- 動画授業情報 -->
                <div class="each-information movie">
                    <h3>Video class information</h3>
                    <div class="in">
                        <dl>
                            <dt>Title</dt>
                            <dd><?php echo $student_data[ 'title' ]; ?></dd>
                        </dl>
                        <dl class="contentsgroup">
                            <dt>Content group</dt>
                            <dd><?php echo $student_data[ 'category' ]; ?></dd>
                            <dd><?php echo $student_data[ 'section' ]; ?></dd>
                        </dl>
                        <dl>
                            <dt>Video time</dt>
                            <dd id="duration"></dd>
                        </dl>
                    </div>
                </div>

                <!-- 各Attendance status -->
                <div class="each-information record">
                    <h3>Each attendance situation</h3>
                    <div class="record head">
	                    <table>
    	                    <thead>
        	                    <tr>
            	                    <th></th>
                	                <th>Viewing date</th>
                    	            <th>Watching time</th>
                        	    </tr>
        	                </thead>
						</table>
					</div>
                   <div class="record body">
	                    <table>
    	                    <tbody class="record_table">
        	                    <tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
                	            <tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
								<tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
                	            <tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
                	            <tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
                	            <tr>
            	                	<td>
                	            		<label class="checkbox"><input type="radio"><span class="icon"></span></label>
                    	        	</td>
                            		<td></td>
            	                	<td></td>
                	            </tr>
                        	</tbody>
                    	</table>
					</div>
                </div>

            </div>

            <div class="graph-info">
            	<ul class="clearfix">
            		<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>

					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>

            		<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>
					<li></li>

            		<li></li>
					<li></li>
            	</ul>
        	</div>

        	<div id="chartdiv"></div>
        </div>
        <!-- ▲main system -->

    </div>
    <!-- ▲main -->
</div>
<script type="text/javascript" src="../../js/papaparse.min.js"></script>
<script type="text/javascript" src="./js/index.js" id="script"
    data-session = '<?php echo json_safe_encode( $student_data ); ?>'></script>
<script type="text/javascript" src="../../js/amcharts.js"></script>
<script type="text/javascript" src="../../js/serial.js"></script>
<script type="text/javascript" src="../../js/light.js"></script>
</body>
</html>
