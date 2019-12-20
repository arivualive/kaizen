<?php
if(isset($view_flag)) {
    session_name('LMS_SuperAdmin');
    session_start();

    require 'class/ClassLoader.php';

    $loader = new ClassLoader();
    $loader->registerDir(dirname(__FILE__) . '/class');
    $loader->registerDir(dirname(__FILE__) . '/models');
    $loader->register();

    $url = 'kaizen/core/module/';
} else {
    require 'class/ClassLoader.php';

    $loader = new ClassLoader();
    $loader->registerDir(dirname(__FILE__) . '/class');
    $loader->registerDir(dirname(__FILE__) . '/module');
    $loader->registerDir(dirname(__FILE__) . '/repository');
    $loader->register();

    require_once 'class/PdoBase.php';

    // DB接続情報設定
    $config = array(
        'host'     => '127.0.0.1',
        'dbname'   => 'kaizen',
        'dbuser'   => 'root',
        'password' => ''
    );

    PdoBase::setConnectionInfo($config);
}
?>
