<?php //echo "tewst";



$absolute = '/home/kaizen2.net/web/'; // テスト環境
//$absolute = 'https://kaizen2.net/';
$url = '/kaizen/core/module/';

echo $absolute.'htdocs/class/Curl.php';
echo $absolute.'htdocs/admin/history/class.tbwp3_access.php';

require ( $absolute . 'htdocs/class/Curl.php' );
require_once ( $absolute . 'htdocs/admin/history/class.tbwp3_access.php');

/*
require ( $absolute . '/class/Curl.php' );
require_once ( $absolute . '/admin/history/class.tbwp3_access.php');
*/
$curl = new Curl( $url );
$tbwp3 = new Tbwp3Access();




?>
