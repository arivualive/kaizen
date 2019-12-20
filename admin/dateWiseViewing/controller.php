<?php
require_once 'class.viewing_data_create.php';


Class Controller
{
    public function getHistories($url, $startDate, $endDate, $page, $itemsPerPage)
    {
        if($page == 1)
        {
            $curl = new Curl($url);
            $query = [];
            $query['startDate'] = $startDate;
            $query['endDate'] = $endDate;

            $data = array('repository' => 'ContentsLogRepository', 'method' => 'getHistoriesByDate', 'params' => $query);

            $histories = $curl->send($data);
            // 2019/5/31 count関数対策
            $getTotalRows = 0;
            if(is_countable($histories)) {
              $getTotalRows = count($histories);
            }
            //$getTotalRows = count($histories);

            $_SESSION['totalPages'] = ceil($getTotalRows / $itemsPerPage);
        }


        $curl = new Curl($url);
        $query = [];
        $query['startDate'] = $startDate;
        $query['endDate'] = $endDate;
        $query['page'] = $page;
        $query['itemsPerPage'] = $itemsPerPage;

        $data = array('repository' => 'ContentsLogRepository', 'method' => 'getHistoriesByDatePagination', 'params' => $query);
        $histories = $curl->send($data);

        $pagination = '';
        if ($_SESSION['totalPages'] != 0)
        {
            $pagePosition = (($page - 1) * $itemsPerPage);
            $pagination = $this->getPagination($page, $_SESSION['totalPages']);
        }

        return ['histories' => json_encode($histories), 'pagination' => $pagination];
    }

    public function getGraphData($url, $historyId)
    {
        $curl = new Curl($url);
        $viewing_data = new ViewingDataCreate();

        # 対象生徒　（ 検証の為、決め打ち ）
        $queryParameters = [];
        $queryParameters['historyId'] = $historyId;

        # log基本情報　視聴日など
        $queryRequest = array('repository' => 'ContentsLogRepository', 'method' => 'getGraphDataByHistoryId', 'params' => $queryParameters);
        $logs = $curl->send($queryRequest);


        # Checking for no data
        if(count($logs) < 1)
            return null;

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

        # Generating Graph object
        $graphs = [];
        foreach ($histories as $key => $history)
        {
            // 2019/5/31 count関数対策
            $count_$histories = 0;
            if(is_countable($histories[$key])) {
              $count_$histories = count($histories[$key]);
            }

            $graph = [
                'balloonText' => "Regeneration date : ".$count_$histories > 0 ? $histories[$key][0]['play_start_datetime'] : '')."<br>Content time  [[value]]</br>" . "Play time [[category]]",
                //'balloonText' => "再生日 : ".(count($histories[$key]) > 0 ? $histories[$key][0]['play_start_datetime'] : '')."<br>Content time  [[value]]</br>" . "Play time [[category]]",
                "bullet" => 'round',
                'bulletSize' => 10,
                'lineThickness' => 3,
                'legendValueText' => '[[value]]',
                'valueField' => 'Y'.$key,
                'lineColorField' => 'L'.$key
            ];
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
                if($event['reason'] == 'ALL_CLEAR' && $event['event_action_id'] != 255)
                {
                    if($previousEvent['reason'] == 'INVISIBLE')
                    {
                        $previouslyAddedValues['L'.$event['history_id']] = $currentPlayingSpeed;
                    }
                    else
                    {
                        $previousEvent = $event;
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
                else if($event['reason'] == 'BEFORE_SEEK' || $event['reason'] == 'AFTER_SEEK')
                {
                    $previouslyAddedValues['L'.$event['history_id']] = $currentPlayingSpeed;
                }
                # Detecting other events
                else
                {
                    $previouslyAddedValues['L'.$event['history_id']] = $viewing_data->speedValueNumber($event['speed_id']);
                    $currentPlayingSpeed = $viewing_data->speedValueNumber($event['speed_id']);
                }

                $dataProvider[$progressTime] = $previouslyAddedValues;
                $previousEvent = $event;
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

        return $clientData;
    }

    function getPagination($currentPage, $totalPages)
    {
        $pagination = '';

        if ($totalPages > 0 && $totalPages != 1 && $currentPage <= $totalPages) {
            $pagination .= '<ul class="page">';

            $rightLinks = $currentPage + 3;
            $previous = $currentPage - 1;
            $next = $currentPage + 1;
            $firstLink = true;

            if ($currentPage > 1) {
                $previousLink = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a href="#" data-page="1" title="First">&laquo;</a></li>';
                $pagination .= '<li><a href="#" data-page="' . $previousLink . '" title="Previous">&lt;</a></li>';
                for ($i = ($currentPage - 2); $i < $currentPage; $i++) {
                    if ($i > 0) {
                        $pagination .= '<li><a href="#" data-page="' . $i . '" title="Page' . $i . '">' . $i . '</a></li>';
                    }
                }
                $firstLink = false;
            }

            if ($firstLink) {
                $pagination .= '<li class="first active">' . $currentPage . '</li>';
            } elseif ($currentPage == $totalPages) {
                $pagination .= '<li class="last active">' . $currentPage . '</li>';
            } else {
                $pagination .= '<li class="active">' . $currentPage . '</li>';
            }

            for ($i = $currentPage + 1; $i < $rightLinks; $i++) {
                if ($i <= $totalPages) {
                    $pagination .= '<li><a href="#" data-page="' . $i . '" title="Page ' . $i . '">' . $i . '</a></li>';
                }
            }
            if ($currentPage < $totalPages) {
                $nextLink = ($next > $totalPages) ? $totalPages : $next;
                $pagination .= '<li><a href="#" data-page="' . $nextLink . '" title="Next">&gt;</a></li>';
                $pagination .= '<li class="last"><a href="#" data-page="' . $totalPages . '" title="Last">&raquo;</a></li>';
            }

            $pagination .= '</ul>';
        }

        return $pagination;
    }
}
