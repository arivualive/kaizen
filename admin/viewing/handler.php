<?php
    require_once "../../config.php";
    require_once 'controller.php';

    $curl = new Curl($url);

    function debug_r( $data ) {

      echo "<pre>";
      print_r( $data );
      echo "</pre>";
    }

    $send_data[ 'student_id' ]  = filter_input( INPUT_POST, "student_id" );
    $send_data[ 'content_id' ] = filter_input( INPUT_POST, "contents_number" );
    $send_data[ 'contents_number' ] = $send_data[ 'content_id' ];

    //$getdata = filter_input( INPUT_GET, "action" );
    //debug_r( $curl );

    /*
    f (filter_input(INPUT_GET, "id")) {
        $student_data[ 'contents_id' ] = filter_input(INPUT_GET, "id");
    }
    */
    $log_history = array('repository' => 'ContentsLogRepository', 'method' => 'findStudentLog', 'params' => $send_data );
    $log_history_data = $curl->send($log_history);

    $log_history[ 'method' ] = 'getContentTitle';

    $contents_name = $curl->send($log_history);

    //debug_r($getdata);
    //debug_r($_GET);
    //debug_r( $log_history_data );

    $history_data = [];

    for ( $i = 0; $i < count( $log_history_data ); $i++ ) {
      $history_data[] = $log_history_data[ $i ][ 'history_id' ];
    }
    //$histories = join("','",$history_data);

    //var_export($history_data);
    // Getting all logs
    if (!empty($_POST) && isset($_GET['action']) && $_GET['action'] == 'contents-old')
    //if ( !empty( $history_data ))
    {

        $viewing_data = new ViewingDataCreate();

        $queryData = [];
        $queryData['student_id'] = $send_data[ 'student_id' ];
        $queryData['contents_number'] = $send_data[ 'contents_number' ];
        $queryData['histories'] = $_POST["histories"];
        //$queryData['histories'] = $history_data;

        // log基本情報　視聴日など
        $log_data = array('repository' => 'ContentsLogRepository', 'method' => 'logProportionData', 'params' => $queryData);
        $log_event_data = $curl->send($log_data);

        //debug_r( $log_event_data );

        // log の中の最大視聴時間を取得 & speed_id を color-codeへ変換
        foreach ($log_event_data as $key => $value)
        {
            $arr[$key] = $value['progress_time'];
            // eventが入っているlogのhistory_idを抽出
            $histoty_id[$key] = $value['history_id'];

            $color = $value['speed_id'];
            $position = $value['position'];
            $time = $value['progress_time'];

            $log_event_data[$key]['lineColor'] = $viewing_data->speedValueNumber($color);
            $log_event_data[$key]['duration'] = $viewing_data->milliSec2Time($time); // x軸
            $log_event_data[$key]['data'] = $viewing_data->milliSec2Time($position); // y軸
        }

        $max_progress_time = max($arr);

        // history_id を抽出
        $unique = array_unique($histoty_id);
        $align_unique['history_id'] = array_values($unique);
        // 2019/6/03 count関数対策
        $count_history = 0;
        if(is_countable($align_unique['history_id'])){
          $count_history = count($align_unique['history_id']);
        }
        //$count_history = count($align_unique['history_id']);
        // log基本情報　視聴日など
        $log_basic_data = [];
        $log_event_array = [];
        // 2019/6/03 count関数対策
        $count_log_event_data = 0;
        if(is_countable($log_event_data)){
          $count_log_event_data = count($log_event_data);
        }

        //$count_log_event_data = count($log_event_data);

        // 視聴時間分割
        $split_time = $max_progress_time / 10;
        $test_event = [];

        for ($s = 0; $s <= 10; $s++)
        {
            $test_event[$s] = $viewing_data->milliSec2Time(floor($split_time * $s));
        }

        // 最長の視聴時間分の配列を作成
        $max_split_time = array_fill(0, $test_event[10], "");
        $st = 0;

        foreach ($max_split_time as $key => $value)
        {
            $max_split_time[$key] = $st;
            $st++;
        }

        $multi_line = [];
        $graphs = [];

        for ($i = 0; $i < $count_history; $i++)
        {
            $log_data = array('repository' => 'ContentsLogRepository', 'method' => 'findStudentLogByHistoryId', 'params' => array('history_id' => $align_unique['history_id'][$i]));
            $log_basic_data[$i] = $curl->send($log_data);

            $history_id = $log_basic_data[$i][0]['history_id'];
            $es = "event";
            $e_count = 0;
            $es_merge = $es . $e_count;

            $x = "x" . $i;
            $y = "y" . $i;
            $x_point = "x_point";
            $y_point = $y . "_point";
            $line_color = "lineColor" . $i;

            $play_start_datetime = $log_basic_data[$i][0]['play_start_datetime'];

            // graphs Object
            $graphs[$i]["balloonText"] = "再生日 : " . $play_start_datetime . "<br>contents_time : [[$y_point]]</br>" . "playing_time : [[$x_point]]";
            $graphs[$i]["bullet"] = "round";
            $graphs[$i]["bulletSize"] = 10;
            $graphs[$i]["lineThickness"] = 3;
            $graphs[$i]["lineColorField"] = "lineColor" . $i;
            $graphs[$i]["valueField"] = "y" . $i;
            // 2019/6/03 count関数対策
            $event_count = 0;
            if(is_countable($log_basic_data[$i])){
              $event_count = count($log_basic_data[$i]);
            }
            //$event_count = count($log_basic_data[$i]);

            for ($e = 0; $e < $count_log_event_data; $e++)
            {
                if ($history_id === $log_event_data[$e]['history_id'])
                {
                    $log_basic_data[$i][0]['event'][$es_merge] = $log_event_data[$e];
                    $log_basic_data['event_array'][$i][$es_merge][$x] = $log_event_data[$e]['duration'];
                    $log_basic_data['event_array'][$i][$es_merge][$y] = $log_event_data[$e]['data'];
                    $log_basic_data['event_array'][$i][$es_merge][$y_point] = $viewing_data->milliSec2Time2($log_event_data[$e]['data']);
                    $log_basic_data['event_array'][$i][$es_merge][$line_color] = $log_event_data[$e]['lineColor'];

                    foreach ($max_split_time as $key => $value)
                    {
                        if ($log_basic_data['event_array'][$i][$es_merge][$x] == $value)
                        {
                            $multi_line[$key][$y] = $log_basic_data['event_array'][$i][$es_merge][$y];
                            $multi_line[$key][$y_point] = $viewing_data->milliSec2Time2($multi_line[$key][$y] * 1000);
                            $multi_line[$key][$line_color] = $log_basic_data['event_array'][$i][$es_merge][$line_color];
                        }
                    }

                    $log_event_array['event_array'][$i][$es_merge][$x] = $log_event_data[$e]['duration'];
                    $log_event_array['event_array'][$i][$es_merge][$y] = $log_event_data[$e]['data'];
                    $e_count++;
                    $es_merge = $es . $e_count;
                }
            }
        }

        // contents 長さを分割
        $contents_split_time = $log_basic_data[0][0]['duration'] / 10;

        for ($s = 0; $s <= 10; $s++)
        {
            $log_basic_data['content_split'][$s]['value'] = $viewing_data->milliSec2Time($contents_split_time * $s);
            $log_basic_data['content_split'][$s]['label'] = $viewing_data->milliSec2Time2($contents_split_time * $s);
        }

        foreach ($multi_line as $key => $x_value)
        {
            $multi_line[$key]['x_point'] = $key;
        }

        $multi_line = array_values($multi_line);

        // $log_data と　$max_split_time　の　x軸が同じ要素を結合する
        $log_basic_data['max_progress_time'] = $max_progress_time;
        $log_basic_data['multi_line'] = $multi_line;
        $log_basic_data['graphs'] = $graphs;

        foreach ((array) $log_basic_data['multi_line'] as $key => $value)
        {
            $sort[$key] = $value['x_point'];
        }

        sort($sort);
        // 2019/6/03 count関数対策
        $sort_count = 0;
        if(is_countable($sort)){
          $sort_count = count($sort);
        }
        //$sort_count = count($sort);
        for ($s = 0; $s < $sort_count; $s++)
        {
            for ($l = 0; $l < $sort_count; $l++)
            {
                if ($log_basic_data['multi_line'][$l]['x_point'] == $sort[$s])
                {
                    $sort[$s] = $log_basic_data['multi_line'][$l];
                }
            }
        }

        $log_basic_data['multi_line'] = $sort;

        // x軸データを hh:mm:ss へ変換
        foreach ($log_basic_data['multi_line'] as $key => $x_value)
        {
            $x_sec = $x_value['x_point'] * 1000;
            $x_sec = $viewing_data->milliSec2Time2($x_sec);
            //$y_sec = $y_value[ '']
            $log_basic_data['multi_line'][$key]['x_point'] = $x_sec;
        }

        //var_export($log_basic_data);
        //$js_send = json_safe_encode($log_basic_data);
        //debug_r( $log_basic_data );
        //exit();

        print json_encode($log_basic_data);
    }

    // Getting all logs
    if (isset($_GET['action']) && $_GET['action'] == 'histories')
    //if (!empty( $history_data ))
    {   //$test = 'history';
        //debug_r($send_data);
        $controller = new Controller();
        $histories = $controller->getHistories( $url, $send_data );
        echo json_encode($histories);
    }

    // Getting all logs
    if (!empty($_POST) && isset($_GET['action']) && $_GET['action'] == 'contents')
    //if (!empty( $history_data ))
    {
        //debug_r( $_POST["histories"] );
        $send_data[ 'student_id' ] = $_POST[ 'student_id' ];
        $send_data[ 'contents_id' ] = $_POST[ 'contents_number' ];
        $controller = new Controller();
        //debug_r($send_data);
        //$send_data[ 'contents_number' ] = $send_data[ 'content_id' ];
        $logs = $controller->getContentLog($url, $_POST["histories"], $send_data );
        //debug_r( $logs );
        //$logs = $controller->getContentLog($url, $history_data);
        echo json_encode($logs);
    }
