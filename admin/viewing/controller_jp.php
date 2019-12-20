<?php
require_once 'class.viewing_data_create.php';


Class Controller
{

    public function getContentLog($url, $histories, $send_data )
    {
        $curl = new Curl($url);
        $viewing_data = new ViewingDataCreate();

        # 対象生徒　（ 検証の為、決め打ち ）
        $queryParameters = [];
        $queryParameters['student_id'] = $send_data[ 'student_id' ];
        $queryParameters['contents_number'] = $send_data[ 'contents_id' ];
        $queryParameters['histories'] = $histories;

        # log基本情報　視聴日など
        $queryRequest = array('repository' => 'ContentsLogRepository', 'method' => 'logProportionData', 'params' => $queryParameters);
        $logs = $curl->send($queryRequest);

        //return $logs;

        # Preparing values for X axis as decisecond
        # Calculating with decisecond unit (10^-1) inspite of avoiding multiple events in same second
        $maxProgressTime = floor (max(array_column($logs, 'progress_time')) / 100);
        $dataProvider = [];
        for ($i=0; $i <= $maxProgressTime; $i++)
        {
            array_push($dataProvider, ['X' => gmdate("i:s", $i / 10)]);
        }

        # Preparing Log data as group by History
        $histories = [];
        foreach($logs as $log)
        {
            $histories[$log['history_id']][] = $log;
        }

        //var_export($histories);

        # Generating Graph object
        $graphs = [];
        foreach ($histories as $key => $history)
        {
          // 2019/6/03 count関数対策
          if(is_countable($histories[$key])){
            $graph = [
                'balloonText' => $history[ 0 ][ 'history_id' ]."</br>再生日 : ".(count($histories[$key]) > 0 ? $histories[$key][0]['play_start_datetime'] : '')."<br>Content time  [[value]]</br>" . "Play time [[category]]",
                "bullet" => 'round',
                'bulletSize' => 10,
                'lineThickness' => 3,
                'legendValueText' => '[[value]]',
                'valueField' => 'Y'.$key,
                'lineColorField' => 'L'.$key,
            ];
          }
          /*
            $graph = [
                'balloonText' => $history[ 0 ][ 'history_id' ]."</br>再生日 : ".(count($histories[$key]) > 0 ? $histories[$key][0]['play_start_datetime'] : '')."<br>Content time  [[value]]</br>" . "Play time [[category]]",
                "bullet" => 'round',
                'bulletSize' => 10,
                'lineThickness' => 3,
                'legendValueText' => '[[value]]',
                'valueField' => 'Y'.$key,
                'lineColorField' => 'L'.$key,
            ];
            */
            array_push($graphs, $graph);

            $previousEvent = null;
            $currentPlayingSpeed = null;
            foreach ($history as $key => $event)
            {
                # Getting position value in second for Y axis
                $position = floor($event['position'] / 1000);
                # Getting progress time value in decisecond unit (10^-1) inspite of avoiding multiple events in same second for X axis
                $progressTime = floor($event['progress_time'] /100);
                $previouslyAddedValues = $dataProvider[$progressTime];

                # Assigning Y Axis value
                $previouslyAddedValues['Y'.$event['history_id']] = $position;

                # Assigning Line Color value
                # Ignoring Unnecessary events
                if($event['reason'] == 'WAIT_MEDIA'/*'ALL_CLEAR'*/ && $event['event_action_id'] != 255)
                {
                    if($previousEvent['reason'] == 'INVISIBLE')
                    {
                        $previouslyAddedValues['L'.$event['history_id']] = $currentPlayingSpeed;
                    }
                    else
                    {
                        $previousEvent = $event;
                        //$previousEvent = $viewing_data->speedValueNumber( $event['speed_id'] );
                        continue;
                    }
                }
                # Detecting start event
                else if($previousEvent == null && $event['reason'] == 'LOAD_IMAGE')
                {
                    $previouslyAddedValues['L'.$event['history_id']] = $viewing_data->speedValueNumber(10);
                    $currentPlayingSpeed = $viewing_data->speedValueNumber(10);
                }
                # Detecting play stop points
                else if($event['reason'] == 'SEARCH' || $event['reason'] == 'PAUSE' ||  $event['reason'] == 'INVISIBLE' ||$event['event_action_id'] == 255)
                {
                  if ( $event[ 'event_action_id' ] == 255 ) {
                    $watch_duration = $event[ 'progress_time' ];
                  }
                    $previouslyAddedValues['L'.$event['history_id']] = $viewing_data->speedValueNumber(0);
                }
                # Detecting events connected with previous event
                else if($event['reason'] == 'TOP_BUTTON' || $event['reason'] == 'NEXT_BUTTON' || $event['reason'] == 'PREVIOUS_BUTTON' || $event['reason'] == 'AUTO_REPEAT' )
                {
                    $previousProgressTime = floor($previousEvent['progress_time'] / 100);
                    $previousPosition = floor($previousEvent['position'] / 1000);

                    $temp = $dataProvider[$previousProgressTime];
                    $temp['L'.$event['history_id']] = $viewing_data->speedValueNumber(0);
                    $temp['Y'.$event['history_id']] = $previousPosition;
                    $dataProvider[$previousProgressTime] = $temp;

                    $previouslyAddedValues['L'.$event['history_id']] = $viewing_data->speedValueNumber($event['speed_id']);
                }
                # Detecting Seeking during playing
                else if($event['reason'] == 'BEFORE_SEEK' || $event['reason'] == 'BACK_SEEK')
                {
                    $previouslyAddedValues['L'.$event['history_id']] = $currentPlayingSpeed;
                }
                # Detecting other events
                else
                {
                    //return $event['speed_id'];
                    $speed['speed_id'][] = $event['speed_id'];
                    $speed['position'][] = $event['position'];

                    $previouslyAddedValues['L'.$event['history_id']] = $viewing_data->speedValueNumber($event['speed_id']);
                    $currentPlayingSpeed = $viewing_data->speedValueNumber($event['speed_id']);
                }

                $dataProvider[$progressTime] = $previouslyAddedValues;
                $previousEvent = $event;
                $contents_time = $viewing_data->milliSec2Time2( $event[ 'duration' ] );
            }
        }

        # Generating splited full-duration (Guides) for Y Axis in second
        $guides = [];
        if(count($logs) > 0)
        {
             // 視聴時間分割
            $splitPart = 10;
            $splitTimes = floor($logs[0]['duration'] / 1000) / $splitPart;
            $splitedDuration = [];


            for ($i = 0; $i <= $splitPart; $i++)
            {
                $splitedDuration[$i] = $splitTimes * $i;
            }

            foreach ($splitedDuration as $key => $value)
            {
                array_push($guides, ['value' => $value, 'label' => gmdate("i:s", $value)]);
            }
        }

        $clientData = [];
        $clientData['dataProvier'] = $dataProvider;
        $clientData['graphs'] = $graphs;
        $clientData['guides'] = $guides;
        $clientData['histories'] = $histories;
        $clientData['duration'] = $contents_time;
        $clientData['watch_duration'] = $watch_duration;

        //return $speed;

        return $clientData;
    }

    public function getHistories($url, $send_data )
    {
        $curl = new Curl($url);
        $query = [];

        //$query['student_id'] = $send_data[ 'student_id' ];
        //$query['content_id'] = $send_data[ 'content_id' ]; //64 43

        $data = array('repository' => 'ContentsLogRepository', 'method' => 'getHistoryIds', 'params' => $send_data);
        $histories = $curl->send($data);

        return $histories;
    }
}
