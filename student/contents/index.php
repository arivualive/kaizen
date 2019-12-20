<?php
require_once "../../config.php";
$curl = new Curl($url);

$data = array( 'repository' => 'ContentsRepository', 'method' => 'findContentsAll' );
$contents_data = $curl->send( $data );

// 外部jSファイルへ安全にデータを渡す
function json_safe_encode ( $data ) {
  return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
// jsへ渡す為の代入
$send_js = [];
$send_js = $_SESSION[ "auth" ];
$send_js += [ "url" => $url2 ];
/*
echo "<pre>";
print_r( $_SESSION );
echo "</pre>";
exit();
*/

 ?>
 <!DOCTYPE html>
 <html>
 <head>
 <meta charset="utf-8" />
 <link rel="stylesheet" href="../css/sweetalert-master/dist/sweetalert.css">
 <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>-->
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
 <script type="text/javascript" src="../css/sweetalert-master/dist/sweetalert.min.js"></script>
 <script type="text/javascript" src="js/index.js" id="script"
  data-session = '<?php echo json_safe_encode( $send_js ); ?>'
 ></script>
 <link rel="stylesheet" href="../css/student.css">
 <title>newlms-contets</title>
 <!--[if IE]>
 <script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script>

 <![endif]-->
 <style>
 article, aside, dialog, figure, footer, header,
 hgroup, menu, nav, section { display: block; }
 </style>
 </head>
 <body>
 <h1><a href="https://<?php echo BASE_URL;?>/student/">Menu</a></h1>
 <h2><?php echo $_SESSION[ 'auth' ][ 'student_name' ]; ?></h2>
 <div id="player_wrap">
   <div id="player" style="width: 900px;height: 500px; background: #000000; margin: 0 auto; z-index: 2"></div>
 </div>
 <button class="contents_play" id="play1" value="64">PLAY1</button>
 <button class="contents_play" id="play2" value="60">PLAY2</button>
 <button class="contents_play" id="play3" value="61">PLAY3</button>
 <button class="contents_play" id="play4" value="62">PLAY4</button>
 <button class="contents_play" id="play5" value="63">PLAY5</button>

 <button class="contents_stop" id="stop" value="">STOP</button>
 <button class="log_update" id="update" value="">ログ更新</button>
 <span id="proportion"></span>
 <ul class="btns">
    <li class="contents_play" value="59"><a href=""></a></li><!-- 再生 -->
    <li class="file"><a href=""></a></li><!-- 添付ファイル -->
    <li class="info"><a data-toggle="modal" data-target="#Modal-contentsinfo"></a></li><!-- 詳細 -->
 </ul>
 <script type="text/javascript" src="https://tbwp3.kaizen2.net/scripts/tbwp3"></script>
 </body>
 </html>
