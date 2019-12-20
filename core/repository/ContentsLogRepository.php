<?php
require_once '../config.php';
date_default_timezone_set('Asia/Dhaka');

class ContentsLogRepository extends PdoBase
{
    public function findStudentLog( $data ) {
      $sql = '
        SELECT
          *
        FROM
          log_contents_history_student
        WHERE
          student_id = :student_id
        AND
          school_contents_id = :contents_id

      ';

      return $this->fetchAll($sql, array(
          ':student_id' => $data['student_id']
        , ':contents_id' => $data['contents_number']
      ));
    }

    public function getContentTitle ( $data ) {
      $sql = '
        SELECT
          contents_name
        FROM
          tbl_contents
        WHERE
          contents_id = :contents_id
        AND
          enable = 1

      ';

      return $this->fetch($sql, array(
        ':contents_id' => $data['contents_number']
      ));
    }

    public function reacquirefindStudentLog( $data ) {
      $sql = '
        SELECT
          *
        FROM
          log_contents_history_student
        WHERE
          student_id = :student_id
        AND
          proportion < 100

      ';

      return $this->fetchAll($sql, array(
          ':student_id' => $data['student_id']
      ));
    }

    // history_id での抽出
    public function findStudentLogByHistoryId( $data ) {
      //return $data;
      $sql = '
        SELECT
          *
        FROM
          log_contents_history_student
        WHERE
          history_id = :history_id

      ';

      return $this->fetchAll($sql, array(
          ':history_id' => $data[ 'history_id' ]
      ));

    }

    // contents-拡張子を取得
    public function getContentsExtension ( $ex ) {

      $sql = '
        SELECT
          extension
        FROM
          mst_contents_extension
        WHERE
          contents_extension_id = :extension_id
      ';

      return $this->fetchAll( $sql, array(
        'extension_id' => $ex[ 'extension_id' ]
      ));

    }

    // 視聴ログ初期データ挿入
    public function firstLogInsert ( $data ) {

      $time = date("Y/m/d H:i:s");

      $sql = '
        INSERT INTO log_contents_history_student (
          contents_category_id
          , school_contents_id
          , student_id
          , player_code
          , registered_datetime
          , contents_download_datetime
          , play_start_datetime
          , duration
          , reached_frame
        ) VALUES (
             1
            , :contents_id
            , :student_id
            , :player_code
            , :registered_datetime
            , :contents_download_datetime
            , :play_start_datetime
            , 0
            , 0
        )';
        /*
        $sql = '
          INSERT INTO log_contents_history_student (
            contents_category_id
            , school_contents_id
            , student_id
            , player_code
            , registered_datetime
            , contents_download_datetime
            , play_start_datetime
            , duration
            , reached_frame
          ) VALUES (
               1
              , :contents_id
              , :student_id
              , :player_code
              , CURRENT_TIMESTAMP()
              , CURRENT_TIMESTAMP()
              , CURRENT_TIMESTAMP()
              , 0
              , 0
          )';
          return $this->execute($sql, array(
              ':contents_id' => $data[ 'contents_number' ]
              ,':student_id' => $data[ 'student_id' ]
              ,':player_code' => $data['player3_code']
          ));
          */
          return $this->execute($sql, array(
              ':contents_id' => $data[ 'contents_number' ]
              ,':student_id' => $data[ 'student_id' ]
              ,':player_code' => $data['player3_code']
              ,':registered_datetime' => $time
              ,':contents_download_datetime' => $time
              ,':play_start_datetime' => $time
          ));

      return $this->lastInsertId();

    }

    // log主キーとなるhistory_idを取得
    public function getHistoryId ( $data ) {
      $sql = '
        SELECT
          history_id
        FROM
          log_contents_history_student
        WHERE
          student_id = :student_id
        AND
          player_code = :player_code
      ';

      return $this->fetchAll($sql, array(
          ':student_id' => $data['student_id']
        , ':player_code' => $data['player3_code']
      ));

    }

    // start_frame 取得
    public function getReachedFrame( $data ) {


      $sql = '
        SELECT
          a.registered_datetime
        , b.reached_frame
        FROM
          (
            SELECT
              max( registered_datetime ) as registered_datetime
            FROM
              log_contents_history_student
            WHERE
              student_id = :student_id
            AND
              school_contents_id = :school_contents_id
            ) AS a
        INNER JOIN
          log_contents_history_student as b
        ON
          b.registered_datetime = a.registered_datetime

        ';
      return $this->fetchAll( $sql, array(
         ':student_id' => $data[ 'student_id' ]
        ,':school_contents_id' => $data[ 'contents_number' ]
      ));
    }

    // reached_frame UPDATE
    public function updateReachedFrame ( $data ) {
      $sql = '
        UPDATE
          log_contents_history_student
        SET
           reached_frame = :reached_frame
          ,duration = :duration
        WHERE
          history_id = :history_number

      ';

      return $this->execute($sql, array(
          ':reached_frame' => $data[ 'reached_frame' ] * 10
        , ':duration' => $data[ 'duration' ] * 10
        , ':history_number' => $data[ 'history_number' ]

      ));

    }

    // 最終 reached_frame UPDATE
    public function lastReachedFrame ( $data, $history_id ) {
      $sql = '
        UPDATE
          log_contents_history_student
        SET
           reached_frame = :reached_frame
        WHERE
          history_id = :history_number

      ';

      return $this->execute($sql, array(
          ':reached_frame' => $data[ 'position' ]
        , ':history_number' => $history_id
      ));

    }

    // グラフ異常対策として
    public function dataMultiSort ( $data ) {
      // 2019/6/03 count関数対策
      $data_count = 0;
      if(is_countable($data)){
        $data_count = count( $data );
      }
      //$data_count = count( $data );
      //return $data_count;

      if ( $data_count == '1' ) {

        $data[ 0 ][ 'action_number' ] = 0;
        $data[ 1 ][ 'progress_time' ] = $data[ 0 ][ 'progress_time' ];
        $data[ 1 ][ 'position' ] = 0;
        $data[ 1 ][ 'action_number' ] = 255;
        $data[ 1 ][ 'speed_number' ]  = $data[ 0 ][ 'speed_number' ];
        $data[ 1 ][ 'volume_number' ] = 100;
        $data[ 1 ][ 'reasons' ][ 0 ]  = 3;

      } else {

        $data_last_count = $data_count - 1;
        array_multisort( array_column( $data, 'progress_time' ), SORT_ASC, $data );
        // event 先頭はaction_number を 0へ
        $data[ 0 ][ 'action_number' ] = 0;
        // event 最後はaction_number を 255へ
        $data[ $data_last_count ][ 'action_number' ] = 255;

      }

      return $data;

    }

    // history_updata time を入れる
    public function player3HistoryUploadTime ( $history_id ) {
      //return $history_id[ 'history_id' ];
      $time = date("Y/m/d H:i:s");
      $sql = '
        UPDATE
          log_contents_history_student
        SET
           history_upload_datetime = :history_upload_datetime
        WHERE
          history_id = :history_id
      ';

      return $this->exec($sql, array(
        ':history_id' => $history_id[ 'history_id' ]
        ,':history_upload_datetime' => $time
      ));
      /*
      $sql = '
        UPDATE
          log_contents_history_student
        SET
           history_upload_datetime = CURRENT_TIMESTAMP()
        WHERE
          history_id = :history_id
      ';

      return $this->exec($sql, array(
        ':history_id' => $history_id[ 'history_id' ]
      ));
      */
    }


    // event data insert SQL
    public function player3EventLogInsertSql ( $data, $history_id ) {

      if ( empty( $data[ 'volume_number' ] )) {
        $data[ 'volume_number' ] = 100;
      }

      $sql = '
        INSERT INTO log_contents_history_student_event (
              contents_category_id
            , history_id
            , progress_time
            , position
            , event_action_id
            , speed_id
            , volume_id
          ) VALUES (
              1
            , :history_id
            , :progress_time
            , :position
            , :event_action_id
            , :speed_id
            , :volume_id

        )
      ';

        $this->exec($sql, array(
            ':history_id' => $history_id
          , ':progress_time' => $data[ 'progress_time' ]
          , ':position' => $data[ 'position' ]
          , ':event_action_id' => $data[ 'action_number' ]
          , ':speed_id' => $data[ 'speed_number' ]
          , ':volume_id' => $data[ 'volume_number' ]
        ));

      return $this->lastInsertId();

    }

    public function player3ReasonValueSql ( $id, $data ) {

      $sql = '
        INSERT INTO log_contents_history_student_event_reason (
            event_id
          , event_reason_id
        ) VALUES (
            :event_id
          , :event_reason_id
        )
      ';

      return $this->execute($sql, array(
            ':event_id' => $id
          , ':event_reason_id' => $data
      ));

    }

    // blocks-data　select
    public function player3BlocksDataSql ( $data ) {

      $sql = '
        SELECT
          *
        FROM
          tbl_contents_blocks
        WHERE
          contents_id = :contents_id
      ';

      return $this->fetchAll($sql, array(
          ':contents_id' => $data['contents_number']
      ));

    }

    // log 再取得の前のevet-data を delete
    public function getEventidEventLogData ( $data ) {

      //return $data;

      $sql = '
        SELECT
          event_id
        FROM
          log_contents_history_student_event
        WHERE
          history_id = :history_id
      ';

      return $this->fetchAll($sql, array(
          ':history_id' => $data[ 'history_id' ]
      ));

    }

    // log 再取得の前のevet-data を delete
    public function deleteEventLogData ( $data ) {
      //return $data;
      $sql = '
        DELETE FROM
          log_contents_history_student_event
        WHERE
          history_id = :history_id
      ';

      $this->exec($sql, array(
          ':history_id' => $data[ 'history_id' ]
      ));

    }

    // log再取得の前のreason-eventをdelete
    public function deleteReasonValueData ( $data ) {
      //return $data;

      foreach ( $data as $key => $value ) {

        $sql = '
          DELETE FROM
            log_contents_history_student_event_reason
          WHERE
            event_id = :event_id
        ';

        $this->exec( $sql, array(
          'event_id' => $value[ 'event_id' ]
        ));

      }

    }

    // event data insert SQL
    public function reacquirePlayer3EventLogInsertSql ( $data, $history_id ) {

      $sql = '
        INSERT INTO log_contents_history_student_event (
              contents_category_id
            , history_id
            , progress_time
            , position
            , event_action_id
            , speed_id
            , volume_id
          ) VALUES (
              1
            , :history_id
            , :progress_time
            , :position
            , :event_action_id
            , :speed_id
            , :volume_id

        )
      ';

        $this->exec($sql, array(
            ':history_id' => $history_id
          , ':progress_time' => $data[ 'progress_time' ]
          , ':position' => $data[ 'position' ]
          , ':event_action_id' => $data[ 'action_number' ]
          , ':speed_id' => $data[ 'speed_number' ]
          , ':volume_id' => $data[ 'volume_number' ]
        ));

      return $this->lastInsertId();

    }

    // log_event 再格納
    public function reacquirePlayer3EventLogInsert ( $datas ) {
      // hisoty_id を取り出し
      $history_id = array_pop( $datas );
      $log = $this->dataMultiSort ( $datas );

      $lsst_id = "";
      //return $log;
      foreach ( $log as $data ) {
        $last_id = $this->reacquirePlayer3EventLogInsertSql( $data, $history_id );
        // 2019/6/03 count関数対策
        $r_count = 0;
        if(is_countable($data[ 'reasons' ])){
          $r_count = count( $data[ 'reasons' ] );
        }
        //$r_count = count( $data[ 'reasons' ] );

        for ( $r = 0; $r < $r_count; $r++ ) {
          $this->player3ReasonValueSql( $last_id, $data[ 'reasons' ][ $r ] );
        }
      }

      $last = end( $log );
      $this->lastReachedFrame( $last, $history_id );
      //return $last;

    }

    // blocks-data　insert
    public function player3BlocksDataInsertSql ( $data ) {

      $sql = '
        INSERT INTO tbl_contents_blocks (
            contents_id
          , first_frame
          , final_frame
        ) VALUES (
            :contents_id
          , :first_frame
          , :final_frame
        )
      ';

      return $this->execute($sql, array(
            ':contents_id' => $data[ 'contents_id' ]
          , ':first_frame' => $data[ 'first_frame' ]
          , ':final_frame' => $data[ 'final_frame' ]
      ));

    }

    // log_event 格納
    public function player3EventLogInsert ( $datas ) {

      if ( empty( $datas ) ) {
        return 'false';
      }

      $history_id = array_pop( $datas );
      $log = $this->dataMultiSort ( $datas );

      $lsst_id = "";
      //return $log;
      foreach ( $log as $data ) {
        $last_id = $this->player3EventLogInsertSql( $data, $history_id );
        // 2019/6/03 count関数対策
        $r_count = 0;
        if(is_countable($data[ 'reasons' ])){
          $r_count = count( $data[ 'reasons' ] );
        }
        //$r_count = count( $data[ 'reasons' ] );

        for ( $r = 0; $r < $r_count; $r++ ) {
          $this->player3ReasonValueSql( $last_id, $data[ 'reasons' ][ $r ] );
        }
      }

      $last = end( $log );
      $this->lastReachedFrame( $last, $history_id );
      //return $last;
      return 'true';

    }

    public function testfunction ( $datas ) {

      return $datas;

    }


    public function logProportionData ( $data ) {

      //$questionMarks = join(",", array_pad(array(), count($data[ 'histories' ]), "?"));
      $histories = join("','",$data['histories']);
      //return $data;
      $sql = "
        SELECT
            b.position,
            a.history_id
          , a.duration
          , b.progress_time
          , b.event_action_id
          , b.speed_id
          , d.reason
          , a.play_start_datetime
        FROM
          log_contents_history_student as a
        INNER JOIN
          log_contents_history_student_event as b
        ON
          a.history_id = b.history_id
        INNER JOIN
          log_contents_history_student_event_reason as c
        ON
          b.event_id = c.event_id
        INNER JOIN
          mst_event_reason as d
        ON
          c.event_reason_id = d.event_reason_id
        WHERE
          a.history_id IN ('$histories')
        AND
          c.event_reason_id IN (1,3,4,5,6,7,8,9,12,14,17,18,24,25)
        AND
          !(b.event_action_id = 2 AND c.event_reason_id IN (3))

        ORDER BY
          history_id
      ";

      return $this->fetchAll($sql, array(
        ':student_id' => $data[ 'student_id' ]
      , ':school_contents_id' => $data[ 'contents_number' ]
      //, ':history_id' => $data[ 'history_id' ]
      ));

    }

    public function proportionData ( $data ) {

      $sql = "
        SELECT
            a.history_id
          , a.school_contents_id
          , a.student_id
          , a.duration
          , a.proportion
          , b.progress_time
          , b.position
          , b.event_action_id
          , b.speed_id
          , c.event_reason_id
          , d.reason
        FROM
          log_contents_history_student as a
        INNER JOIN
          log_contents_history_student_event as b
        ON
          a.history_id = b.history_id
        INNER JOIN
          log_contents_history_student_event_reason as c
        ON
          b.event_id = c.event_id
        INNER JOIN
          mst_event_reason as d
        ON
          c.event_reason_id = d.event_reason_id
        WHERE
          student_id = :student_id
        AND
          school_contents_id = :school_contents_id
        ORDER BY
          history_id
      ";

      return $this->fetchAll($sql, array(
        ':student_id' => $data[ 'student_id' ]
      , ':school_contents_id' => $data[ 'contents_number' ]
      //, ':history_id' => $data[ 'history_id' ]
      ));

    }


    // log_contents_history_student のproportion UPDATE
    public function proportionUpadate ( $align_unique ) {

      foreach ( $align_unique[ 'history_id' ] as $value ) {
        $sql = '
          UPDATE
            log_contents_history_student
          SET
             proportion = :proportion
            ,proportion_flg = :proportion_flg
          WHERE
            history_id = :history_id
        ';

        $this->execute($sql, array(
           ':proportion' => $align_unique[ 'proportion' ]
          ,':proportion_flg' => $align_unique[ 'proportion_flg' ]
          ,':history_id' => $value
        ));

      }

    }

    public function logData ( $data ) {
      // 2019/6/03 count関数対策
      $log_count = 0;
      if(is_countable($data[ 'history_id' ])){
        $log_count = count( $data[ 'history_id' ] );
      }
      //$log_count = count( $data[ 'history_id' ] );

      $log = [];

      for ( $i = 0; $i < $log_count; $i++ ) {
        $sql = '
          SELECT
              a.history_id
            , a.school_contents_id
            , a.student_id
            , a.duration
            , a.proportion
            , a.proportion_flg
            , b.progress_time
            , b.position
            , b.event_action_id
            , c.event_reason_id
            , d.reason
          FROM
            log_contents_history_student as a
          INNER JOIN
            log_contents_history_student_event as b
          ON
            a.history_id = b.history_id
          INNER JOIN
            log_contents_history_student_event_reason as c
          ON
            b.event_id = c.event_id
          INNER JOIN
            mst_event_reason as d
          ON
            c.event_reason_id = d.event_reason_id
          WHERE
            a.history_id = :history_id
          ORDER BY
            a.history_id, b.position, c.event_reason_id desc

        ';

        $log[ $i ] = $this->fetchAll($sql, array(
          ':history_id' => $data[ 'history_id' ][ $i ]
        ));

      }

      return $log;
    }

    // log主キーとなるhistory_idを取得
    public function getHistoryIds ( $data ) {
      //return $data;
      $sql = '
      SELECT DISTINCT
          a.history_id,
          a.duration,
          a.play_start_datetime,
          (select MAX(x.progress_time) from log_contents_history_student_event x where x.history_id = a.history_id ) max_progress_time,
          (select MAX(x.position) - MIN(x.position)  from log_contents_history_student_event x where x.history_id = a.history_id) watch_duration
        FROM
          log_contents_history_student a
        INNER JOIN
          log_contents_history_student_event b ON a.history_id = b.history_id
        WHERE
          student_id = :student_id
        AND
          school_contents_id = :content_id
      ';

      return $this->fetchAll($sql, array(
          ':student_id' => $data['student_id']
        , ':content_id' => $data['content_id']
      ));

    }

    // log主キーとなるhistory_idを取得
    public function getContentsByDate ( $data ) {
      $sql = '
        SELECT DISTINCT
          school_contents_id id,
          school_contents_id value
        FROM
          log_contents_history_student
        WHERE
          (play_start_datetime BETWEEN :startDate AND :endDate)
        ';

      return $this->fetchAll($sql, array(
          ':startDate' => $data['startDate']
        , ':endDate' => $data['endDate']));
    }

    // contents_number から視聴クリア割合を取得 旧DB
    public function getSubjectSectionProportion ( $data ) {

      $sql = 'SELECT
                a.contents_id
                , a.subject_section_id
                , a.contents_name
                , b.proportion
              FROM
                tbl_contents as a
              LEFT JOIN
                tbl_subject_section as b
              ON
                a.subject_section_id = b.subject_section_id
              WHERE
                contents_id = :contents_id
              ORDER BY
                contents_id';

      return $this->fetchAll($sql, array(
          ':contents_id' => $data[ 'contents_number' ]
      ));

    }

    // contents_number から視聴クリア割合を取得 新DB
    public function getSubjectSectionContentProportion ( $data ) {

      $sql = 'SELECT
                  contents_id
                , contents_name
                , proportion
              FROM
                tbl_contents
              WHERE
                contents_id = :contents_id';


      return $this->fetchAll($sql, array(
          ':contents_id' => $data[ 'contents_number' ]
      ));

    }

    // contents_number から視聴クリア割合を取得 新DB
    public function reacquireGetSubjectSectionContentProportion ( $data ) {

      $sql = 'SELECT
                  contents_id
                , contents_name
                , proportion
              FROM
                tbl_contents
              WHERE
                contents_id = :contents_id';


      return $this->fetchAll($sql, array(
          ':contents_id' => $data[ 'school_contents_id' ]
      ));

    }

     // log主キーとなるhistory_idを取得
     public function getHistoriesByContent ( $data ) {
      $sql = '
      SELECT DISTINCT
          a.history_id,
          a.play_start_datetime,
          (select MAX(x.position) - MIN(x.position)  from log_contents_history_student_event x where x.history_id = a.history_id) watch_duration
        FROM
          log_contents_history_student a
        INNER JOIN
          log_contents_history_student_event b ON a.history_id = b.history_id
        WHERE
          school_contents_id = :content_id
      ';

      return $this->fetchAll($sql, array(
         ':content_id' => $data['contentId']));
      }

         public function upadateProportionFlg ( $data ) {
      $sql = '
        UPDATE
          log_contents_history_student
        SET
           proportion_flg = :proportion_flg
        WHERE
          student_id = :student_id
        AND
          school_contents_id = : school_contents_id

      ';

      return $this->execute($sql, array(
          ':proportion_flg' => $data[ 'proportion_flg' ]
        , ':student_id'     => $data[ 'student_id' ]
        , ':school_contents_id' => $data[ 'contents_number' ]
      ));

    }

    // 視聴割合テスト
    public function getGraphDataByHistoryId ( $data ) {

      $sql = "
        SELECT
            b.position,
            a.history_id
          , a.duration
          , b.progress_time
          , b.event_action_id
          , b.speed_id
          , d.reason
          , a.play_start_datetime
        FROM
          log_contents_history_student as a
        INNER JOIN
          log_contents_history_student_event as b
        ON
          a.history_id = b.history_id
        INNER JOIN
          log_contents_history_student_event_reason as c
        ON
          b.event_id = c.event_id
        INNER JOIN
          mst_event_reason as d
        ON
          c.event_reason_id = d.event_reason_id
        AND
          c.event_reason_id IN (1,3,4,5,6,7,8,9,12,14,17,18,24,25)
        AND
          !(b.event_action_id = 2 AND c.event_reason_id IN (3))
        AND
          a.history_id = :history_id
        ORDER BY
          history_id
      ";

      return $this->fetchAll($sql, array(
       ':history_id' => $data[ 'historyId' ]
      ));

    }
    // log主キーとなるhistory_idを取得
    public function getHistoriesByDate ( $data ) {
      $sql = '
        SELECT
          c.student_name,
          d.contents_name,
          a.play_start_datetime,
          (select MAX(x.position) - MIN(x.position)  from log_contents_history_student_event x where x.history_id = a.history_id) watch_duration,
          a.duration,
          a.history_id
        FROM
          log_contents_history_student a
          INNER JOIN
          tbl_student c ON a.student_id = c.student_id
          INNER JOIN
          tbl_contents d ON a.school_contents_id = d.contents_id
        WHERE
          (play_start_datetime BETWEEN :startDate AND :endDate)
        ';

      return $this->fetchAll($sql, array(
          ':startDate' => $data['startDate']
        , ':endDate' => $data['endDate']));
    }

    // log主キーとなるhistory_idを取得
    public function getHistoriesByDatePagination ( $data ) {
      $page = $data["page"];
      $itemsPerPage = $data["itemsPerPage"];
      $sql = "
        SELECT
          c.student_name,
          d.contents_name,
          a.play_start_datetime,
          (select MAX(x.position) - MIN(x.position)  from log_contents_history_student_event x where x.history_id = a.history_id) watch_duration,
          a.duration,
          a.history_id
        FROM
          log_contents_history_student a
          INNER JOIN
          tbl_student c ON a.student_id = c.student_id
          INNER JOIN
          tbl_contents d ON a.school_contents_id = d.contents_id
        WHERE
          (play_start_datetime BETWEEN :startDate AND :endDate)
        LIMIT $page, $itemsPerPage ";

      //print_r($sql);
      return $this->fetchAll($sql, array(
          ':startDate' => $data['startDate']
        , ':endDate' => $data['endDate']));
    }

    // eventIdを取得（ ログ再取得用 ）
    public function getEventId ( $data ) {

      $sql = '
        SELECT
           a.event_id
         , a.history_id
         , b.reason_id
         , b.event_id
        FROM
           log_contents_history_student_event as a
        INNER JOIN
           log_contents_history_student_event_reason as b
        ON
           a.event_id = b.event_id
        WHERE
           a.history_id = :history_id
      ';

      return $this->fetchAll( $sql, array(
        ':history_id' => $data[ 'history_id' ]
      ));
    }

    // log 再取得用のSQL
    public function reaquireProportionData ( $data ) {
      //return $data;

      for ( $i = 0; $i < count( $data[ 'school_contents_id' ] ); $i++ ) {

        $sql = "
          SELECT
              a.history_id
            , a.school_contents_id
            , a.student_id
            , a.duration
            , a.proportion
            , b.progress_time
            , b.position
            , b.event_action_id
            , b.speed_id
            , c.event_reason_id
            , d.reason
          FROM
            log_contents_history_student as a
          INNER JOIN
            log_contents_history_student_event as b
          ON
            a.history_id = b.history_id
          INNER JOIN
            log_contents_history_student_event_reason as c
          ON
            b.event_id = c.event_id
          INNER JOIN
            mst_event_reason as d
          ON
            c.event_reason_id = d.event_reason_id
          WHERE
            student_id = :student_id
          AND
            school_contents_id = :school_contents_id
          ORDER BY
            a.history_id, b.position, c.event_reason_id desc
        ";

        $log[ $i ] = $this->fetchAll($sql, array(
          ':student_id' => $data[ 'student_id' ]
          ,':school_contents_id' => $data[ 'school_contents_id' ][ $i ]
        ));

      }

      return $log;

      /*
      return $this->fetchAll($sql, array(
        ':student_id' => $data[ 0 ][ 'student_id' ]
      , ':school_contents_id' => $data[ 0 ][ 'school_contents_id' ]
      //, ':history_id' => $data[ 'history_id' ]
      ));
      */
    }

    // log 再取得用のSQL
    public function reaquireProportionData2 ( $data ) {

        $sql = "
          SELECT
              a.history_id
            , a.school_contents_id
            , a.student_id
            , a.duration
            , a.proportion
            , b.progress_time
            , b.position
            , b.event_action_id
            , b.speed_id
            , c.event_reason_id
            , d.reason
          FROM
            log_contents_history_student as a
          INNER JOIN
            log_contents_history_student_event as b
          ON
            a.history_id = b.history_id
          INNER JOIN
            log_contents_history_student_event_reason as c
          ON
            b.event_id = c.event_id
          INNER JOIN
            mst_event_reason as d
          ON
            c.event_reason_id = d.event_reason_id
          WHERE
            student_id = :student_id
          AND
            school_contents_id = :school_contents_id
          ORDER BY
            a.history_id, b.position, c.event_reason_id desc
        ";

        $log[] = $this->fetchAll($sql, array(
          ':student_id' => $data[ 'student_id' ]
          ,':school_contents_id' => $data[ 'school_contents_id' ]
        ));

      return $log;

      /*
      return $this->fetchAll($sql, array(
        ':student_id' => $data[ 0 ][ 'student_id' ]
      , ':school_contents_id' => $data[ 0 ][ 'school_contents_id' ]
      //, ':history_id' => $data[ 'history_id' ]
      ));
      */
    }

    // log再取得したコンテンツを取得
    public function getContentsReacquire ( $data ) {

      $sql = '
        SELECT
            a.history_id
          , a.school_contents_id
          , a.student_id
          , b.proportion
        FROM
          log_contents_history_student as a
        INNER JOIN
          tbl_contents as b
        ON
          a.school_contents_id = b.contents_id
        WHERE
          history_id = :history_id
      ';

      return $this->fetch( $sql, array(
        ':history_id' => $data[ 'history_id' ]
      ));
    }

    // log 再取得用
    public function reacquireLogData ( $data ) {
      // 2019/6/03 count関数対策
      $log_count = 0;
      if(is_countable($data)){
        $log_count = count( $data );
      }
      //$log_count = count( $data );

      $log = [];

      for ( $i = 0; $i < $log_count; $i++ ) {
        $sql = '
          SELECT
              a.history_id
            , a.school_contents_id
            , a.student_id
            , a.duration
            , a.proportion
            , a.proportion_flg
            , b.position
            , b.event_action_id
            , c.event_reason_id
            , d.reason
          FROM
            log_contents_history_student as a
          INNER JOIN
            log_contents_history_student_event as b
          ON
            a.history_id = b.history_id
          INNER JOIN
            log_contents_history_student_event_reason as c
          ON
            b.event_id = c.event_id
          INNER JOIN
            mst_event_reason as d
          ON
            c.event_reason_id = d.event_reason_id
          WHERE
            a.history_id = :history_id
          ORDER BY
            a.history_id, b.position, c.event_reason_id desc

        ';

        $log[ $i ] = $this->fetchAll($sql, array(
          ':history_id' => $data[ $i ]
        ));

      }

      return $log;
    }


    // log再取得後のUPDATE
    public function reacquireProportionUpadate ( $data ) {

      foreach ( $data[ 'history_id' ] as $value  ) {
        $sql = '
          UPDATE
            log_contents_history_student
          SET
             proportion = :proportion
            ,proportion_flg = :proportion_flg
            ,reacquire_data_flg = 1
          WHERE
            history_id = :history_id
        ';

        $this->execute($sql, array(
           ':proportion'     => $data[ 'proportion' ]
          ,':proportion_flg' => $data[ 'proportion_flg' ]
          //,':reacquire_data_flg' => $data[ 'reacquire_data_flg' ]
          ,':history_id'     => $value
        ));

      }

    }

    // log再取得
    public function reacquireHistory ( $data ) {

        $sql = '
          SELECT
            history_id
            , school_contents_id
            , student_id
          FROM
            log_contents_history_student
          WHERE
            proportion < :proportion
          AND
            reacquire_data_flg = 0
          AND
            proportion_flg = 0
          ORDER BY
            history_id DESC
        ';

        return $this->fetchAll( $sql, array(
          ':proportion' => $data[ 'proportion' ]
        ));

    }

    // log-data無しの判別用フラグを挿入
    public function nullLogDataFlg ( $data ) {

      $sql = '
        UPDATE
          log_contents_history_student
        SET
          reacquire_data_flg = :reacquire_data_flg
        WHERE
          history_id = :history_id
      ';

      return $this->execute( $sql, array(
         ':reacquire_data_flg' => $data[ 'null_flg' ]
        ,':history_id' => $data[ 'history_id' ]
      ));

    }

  }
