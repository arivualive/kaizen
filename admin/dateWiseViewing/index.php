<?php
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

if (isset($_SESSION['auth']['admin_id'])) {
    $admin_id = $_SESSION['auth']['admin_id'];
}

if (isset($_SESSION['auth']['school_id'])) {
    $school_id = $_SESSION['auth']['school_id'];
}

$curl = new Curl($url);
$studentInfo = new AdminStudentModel($school_id, $curl);

if (isset($_GET['id'])) {
    if($_GET['id'] != 0) {
        $data['student_id'] = $_GET['id'];
        $student_data = $studentInfo->getStudent($data, 'person')[0];
        $database = $student_data['bit_subject'];
        //debug($student_data);
    }
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
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/bootstrap-reboot.css">
    <link rel="stylesheet" type="text/css" href="../css/icon-font.css">
    <link rel="stylesheet" type="text/css" href="../css/common.css">
    <link rel="stylesheet" type="text/css" href="../css/detawiseviewing.css">

    <!-- New -->
    <link rel="stylesheet" href="../../css/student_viewing.css">
    <link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="css/styles.css">


    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <link rel="stylesheet" type="text/css" href="css/daterangepicker.css" />
</head>

<body>
    <div id="wrap">

        <!-- ▼navgation -->
        <div id="nav-fixed">
            <!-- ▼h1 -->
            <div class="brand">
                <a href="../info.php">
                    <h1>
                        <div class="img_h1">
                            <img src="../images/logo.jpg" alt="ThinkBoard LMS">
                        </div>
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
                            <li><a href="../contents/index.php" class="active">Content registration / editing</a></li>
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
                            <li><a href="../history/index.php">Confirmation from the student</a></li>
                            <!--<li><a href="dateWiseViewing/index.php" class="active">動画授業から確認</a></li>-->
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
                    <li>
                        <a href="../info.php">HOME</a>
                    </li>
                    <li>Attendance status</li>
                    <li class="active">
                        <a>Check from video class</a>
                    </li>
                </ol>
            </div>
            <!-- ▼user information -->
            <div id="userinfo" class="button-dropdown">
                <a class="link" href="javascript:void(0)">
                    <div class="erea-image"></div>
                    <div class="erea-name">
                        <p class="authority">School Admin</p>
                        <p class="username"><?php echo $_SESSION['auth']['admin_name']; ?></p>
                    </div>
                </a>
                <ul class="submenu">
                    <li role="presentation">
                        <a href="#">
                            <span class="icon-lock"></span>Change password
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="../auth/logout.php">
                            <span class="icon-sign-in"></span>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- ▲header -->

        <!-- ▼main-->
        <div id="maincontents">
            <!-- ▼h2 -->
            <div class="h2">
                <h2>Check from video class</h2>
            </div>
            <!-- ▲h2 -->

            <!-- ▼main System -->
            <div>
            <div class="mt-5 col-daterange">
                <!-- Date Range -->
                <h5 class="mb-3">Select period</h5>
                <div class="form-group">
                    <input type="text" class="form-control" id="daterange" placeholder="Select value">
                </div>
            </div>
                <div class="col-listtable">
                    <table class="js-dynamitable table table-bordered">
                        <!-- table heading -->
                        <thead class="sort">
                            <!-- Sortering
                            * js-sorter-asc => ascending sorter trigger
                            * js-sorter-desc => desending sorter trigger
                            -->
                            <tr>
                                <th>#
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                                <!-- Content Name -->
                                <th>Video class title
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                                <!-- Student Name -->
                                <th>Student name
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                                <!-- Date -->
                                <th>date
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                                <!-- Watch Duration -->
                                <th>Watching time
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                                <!-- Total Duration -->
                                <th>Video class time
                                    <span class="js-sorter-desc icon-arrow-solid-b pull-right"></span>
                                    <span class="js-sorter-asc icon-arrow-solid-t pull-right"></span>
                                </th>
                            </tr>

                            <!-- Filtering
                                * js-filter => filter trigger (input, select)
                                -->
                            <tr>
                                <th>
                                    <!-- input filter -->
                                </th>
                                <th>
                                    <input class="js-filter  form-control" type="text" value="">
                                </th>
                                <th>
                                    <input class="js-filter  form-control" type="text" value="">
                                </th>
                                <th>
                                    <input class="js-filter  form-control" type="text" value="">
                                </th>
                                <th>
                                    <input class="js-filter  form-control" type="text" value="">
                                </th>
                                <th>
                                    <input class="js-filter  form-control" type="text" value="">
                                </th>

                            </tr>
                        </thead>
                        <tbody class="tbtlHistories" >

                        </tbody>
                    </table>
                </div>

                <div class="container col-pager">
                    <div class="row justify-content-center align-self-center">
                        <div id="pagination">
                            <!-- page numbers will be loaded inside here -->
                        </div>
                    </div>
                </div>
                <!-- Guides -->
                <div class="col-speedmeter">
                    <nav>
                        <ul>
                            <li class="title">
                                <div id="stop-speed">
                                    <span id="stop">Stop / seek</span>
                                </div>

                                <div id="normal-speed">
                                    <span id="normal">Equal magnification</span>
                                </div>

                                <div id="double-speed">
                                    <span id="double">2x speed</span>
                                </div>

                                <div id="triple-speed">
                                    <span id="triple">3x speed</span>
                                </div>

                                <div id="quadruple-speed">
                                    <span id="quadruple">4x speed</span>
                                </div>
                            </li>
                            <li>
                                <span class="speed speed-x0"></span>
                                <span class="speed speed-x1-0"></span>
                                <span class="speed speed-x1-1"></span>
                                <span class="speed speed-x1-2"></span>
                                <span class="speed speed-x1-3"></span>
                                <span class="speed speed-x1-4"></span>
                                <span class="speed speed-x1-5"></span>
                                <span class="speed speed-x1-6"></span>
                                <span class="speed speed-x1-7"></span>
                                <span class="speed speed-x1-8"></span>
                                <span class="speed speed-x1-9"></span>
                                <span class="speed speed-x2-0"></span>
                                <span class="speed speed-x2-1"></span>
                                <span class="speed speed-x2-2"></span>
                                <span class="speed speed-x2-3"></span>
                                <span class="speed speed-x2-4"></span>
                                <span class="speed speed-x2-5"></span>
                                <span class="speed speed-x2-6"></span>
                                <span class="speed speed-x2-7"></span>
                                <span class="speed speed-x2-8"></span>
                                <span class="speed speed-x2-9"></span>
                                <span class="speed speed-x3-0"></span>
                                <span class="speed speed-x3-1"></span>
                                <span class="speed speed-x3-2"></span>
                                <span class="speed speed-x3-3"></span>
                                <span class="speed speed-x3-4"></span>
                                <span class="speed speed-x3-5"></span>
                                <span class="speed speed-x3-6"></span>
                                <span class="speed speed-x3-7"></span>
                                <span class="speed speed-x3-8"></span>
                                <span class="speed speed-x3-9"></span>
                                <span class="speed speed-x4-0"></span>
                            </li>
                        </ul>
                    </nav>
                </div>

                <div class="col-grough">
                    <div id="chartdiv"></div>
                </div>
            </div>
            <!-- ▲main System -->

        </div>
        <!-- ▲main -->
    </div>
</body>

<!-- New -->
<script src="https://code.jquery.com/jquery-2.2.3.js"></script>
<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/daterangepicker.js"></script>

<!-- dynamitable -->
<script src="js/dynamitable.jquery.min.js"></script>
<link href="https://www.jqueryscript.net/css/jquerysctipttop.css" rel="stylesheet" type="text/css">


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh"
        crossorigin="anonymous"></script>
<script src="../js/bootstrap.js"></script>
<script src="../js/script.js"></script>

<!-- Application File -->
<script type="text/javascript" src="js/index.js"></script>

</html>
