<?php
require_once "../../config.php";
require_once( '../../student/class.modal_create.php' );

$curl = new Curl($url);
$modal = new modalCreate();

//$modal_display = $modal->modal_display();
$modal_display = '';
$student_data = [];

$student_data[ 'school_id' ]    = $_SESSION[ 'auth' ][ 'school_id' ];
$student_data[ 'student_name' ] = $_SESSION[ 'auth' ][ 'student_name' ];

if (filter_input(INPUT_GET, "c_id")) {
    $student_data[ 'contents_id' ] = filter_input(INPUT_GET, "c_id");
}
if (filter_input(INPUT_GET, "s_id")) {
    $student_data[ 'student_id' ] = filter_input(INPUT_GET, "s_id");
}
if (filter_input(INPUT_GET, "e_id")) {
    $student_data[ 'extension_id' ] = filter_input(INPUT_GET, "e_id");
}
if (filter_input(INPUT_GET, "bid")) {
    $student_data[ 'section_id' ] = filter_input(INPUT_GET, "bid");
}

$data = array(
  'repository' => 'ContentsRepository',
  'method' => 'findContentsId',
  'params'=> $student_data
);

$contents_data = $curl->send( $data );
$url =str_replace( '-db', '', $url );

$student_data[ 'contents_name' ] = $contents_data[ 'contents_name' ];
$student_data[ 'size' ]          = $contents_data[ 'size' ];
$student_data[ 'comment' ]       = $contents_data[ 'comment' ];
$student_data[ 'url' ]           = $url;
$student_data[ 'extension_id' ]  = $contents_data[ 'contents_extension_id' ];
/*
echo "<pre>";
print_r( $student_data );
echo "</pre>";
*/
$extension = array(
  'repository' => 'ContentsLogRepository',
  'method' => 'getContentsExtension',
  'params' => $student_data
);

//debug( $student_data );

$extension_results = $curl->send( $extension );
$student_data[ 'extension' ] = ".".$extension_results[ 0 ][ 'extension' ];

// 外部jSファイルへ安全にデータを渡す
function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

$folder_flg = "";

$display_data = $modal->data_create( $_SESSION[ 'auth' ], $contents_data );
/*
echo "<pre>";
print_r( $display_data );
echo "</pre>";
*/
if ( isset( $display_data[ 'flg' ] ) ) {
  /*if ( !is_array( $display_data[ 'flg' ] ) ) {
    $display_data[ 'flg' ] = '';
  }*/
  if ( $display_data[ 'flg' ] == 'false' || $display_data == 'false' ) {
    $folder_flg = 'false';
  } else {
    $folder_flg = 'true';
    $modal_display = $modal->modal_display( $display_data );
  }

}


//debug( $display_data );
//debug( $modal_display );


 ?>
 <!DOCTYPE html>
 <html>
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
  <link rel="stylesheet" type="text/css" href="../css/contents.css">
    <!-- js -->
    <script src="../../js/jquery-3.1.1.js"></script>
    <script src="../../js/popper.min.js"></script>
    <script src="../js/bootstrap.js"></script>
    <script src="../js/script.js"></script>

    <!-- ookubo -->
    <!--<link rel="stylesheet" href="../css/sweetalert-master/dist/sweetalert.css">-->
    <!--<script type="text/javascript" src="../css/sweetalert-master/dist/sweetalert.min.js"></script>-->
    <!--<script type="text/javascript" src="js/contents_play.js" id="script"
    data-session = '<?php //echo json_safe_encode( $student_data ); ?>'
    ></script>-->
    <link rel="stylesheet" href="../css/student.css">
    <title>newlms-contets</title>
    <!--[if IE]>
    <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

    <![endif]-->
    <style>
    article, aside, dialog, figure, footer, header,
    hgroup, menu, nav, section { display: block; }
    </style>


    <!--<script Language="JavaScript">
      window.open("../contentslist.php",null,"width=160,height=300");
    </script>-->
</head>
<body id="folder" data-folderflg='<?php echo $folder_flg; ?>' data-folder='<?php echo json_safe_encode( $display_data ); ?>'>

    <div id="wrap" data-studentID="<?php echo $_SESSION[ 'auth' ][ 'student_id' ];?>" data-schoolID="<?php echo $_SESSION[ 'auth' ][ 'school_id' ];?>">

    <!-- header -->
    <div id="header-bar" style="display:none;">
        <div id="header">
            <!-- left -->
            <div class="header-left">
                <!-- h1 -->
                <div class="h1">
                    <a href="https://<?php echo BASE_URL;?>/student/">
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
    <!-- main container -->
    <div id="container-maincontents" class="container-maincontents clearfix">
        <div id="player_wrap">
            <div class="title"><?php echo $contents_data[ 'contents_name' ]; ?></div>
            <div id="player"></div>
            <div class="description"><?php echo $contents_data[ 'comment' ]; ?></div>
        </div>
        <p style="    text-align: center; color: #f00; padding-top: 10px;">When you finish viewing, please press the STOP button and be sure to press the "Recording results" button to return.</p>
        <ul id="player-btns">
            <!--<li><a class="log_update" id="update" href="../contentslist.php">一覧へ戻る</a></li>-->
            <li id="stopButton"><button id="stop" >STOP</button></li>
            <!--<li><button id="stop">STOP</button></li>  data-toggle="modal" data-target="#contents-continuity"   -->
        </ul>
        <span id="proportion"></span>
    </div>
    </div>

    <!-- modal(関連付けコンテンツ) -->
    <?php if ( $modal_display != '' ) {
      echo $modal_display;
    } ?>
    <!--<script type="text/javascript" src="../css/sweetalert-master/dist/sweetalert.min.js"></script>-->
    <script type="text/javascript" src="./js/contents_play.js" id="script"
    data-session = '<?php echo json_safe_encode( $student_data ); ?>' async></script>
    <script type="text/javascript" src="https://tbwp3.kaizen2.net/scripts/tbwp3"></script>
</body>
</html>
