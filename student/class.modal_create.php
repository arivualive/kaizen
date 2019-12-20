<?php

class modalCreate {

  // modalを表示するページ
  public function type_result ( $data ) {

    $type_judge = [];
    $url = '/kaizen/student/';

    if ( isset( $data[ 'contents_id' ] )) {

      $type_judge[ 'select' ] = 'contents_id';
      $type_judge[ 'id' ] = $data[ 'contents_id' ];
      $type_judge[ 'title' ] = $data[ 'contents_name' ];
      $type_judge[ 'url' ] = $url.'contents/contents_play.php?';

    } else if ( isset( $data[ 'questionnaire_id' ] )) {

      $type_judge[ 'select' ] = 'questionnaire_id';
      $type_judge[ 'id' ] = $data[ 'questionnaire_id' ];
      $type_judge[ 'title' ] = $data[ 'title' ];
      $type_judge[ 'url' ] = $url.'questionnaire/questionnaire.php?';

    } else if ( isset( $data[ 'quiz_id' ] )) {

      $type_judge[ 'select' ] = 'quiz_id';
      $type_judge[ 'id' ] =  $data[ 'quiz_id' ];
      $type_judge[ 'title' ] = $data[ 'title' ];
      $type_judge[ 'url' ] = $url.'quiz/start.php?';
    }

    return $type_judge;
  }

  // 次のコンテンツ
  public function next_page_url ( $data, $s_id, $bid, $skip ) {
    //return $data;
    if ( empty( $data[ 'primary_key' ] ) ) {
      return $data[ 'flg' ] = 'false';
    }

    //return $data;

    if ( count( $skip[ 'skip_contents' ]) > 0 ) {
      $data[ 'skip_contents' ] = $skip[ 'skip_contents' ];
    }

    $url = '/kaizen/student/';

    switch ( $data[ 'type' ] ) {
      case 0:
        $data[ 'url' ] = $url.'contents/contents_play.php?c_id='.$data[ 'primary_key' ]
          .'&bid='.$bid.'&s_id='.$s_id.'&e_id='.$data[ 'contents_extension_id' ];

        if ( $data[ 'contents_extension_id' ] < 7 ) {
          $data[ 'class' ] = "type tb";
          $data[ 'type_string' ] = 'Video class(TB format)';
        } else {
          $data[ 'class' ] = 'type mp4';
          $data[ 'type_string' ] = 'Video class(MP4 format)';
        }

        $data[ 'flg' ] = "true";

      break;

      case 1:
        $data[ 'url' ] = $url.'questionnaire/questionnaire.php?id='.$data[ 'primary_key' ].'&bid='.$data[ 'bit_classroom' ];
        $data[ 'class' ] = 'type questionnaire';
        $data[ 'type_string' ] = 'questionnaire';
        $data[ 'flg' ] = "true";
      break;

      case 2:
        $data[ 'url' ] = $url.'report/report.php?id='.$data[ 'primary_key' ].'&bid='.$data[ 'bit_classroom' ];
        $data[ 'class' ] = 'type report';
        $data[ 'type_string' ] = 'report';
        $data[ 'flg' ] = "true";
      break;

      case 3:
        $data[ 'url' ] = $url.'quiz/start.php?id='.$data[ 'primary_key' ].'&bid='.$data[ 'bit_classroom' ];
        $data[ 'class' ] = 'type test';
        $data[ 'type_string' ] = 'quiz';
        $data[ 'flg' ] = "true";
      break;

    }

    return $data;

  }

  // 次に受けるコンテンツデータを生成
  public function data_create ( $data, $contents ) {
    //return $data;
    $folder_data = [];
    $next_data = [];

    if ( !isset( $data[ 'folder_data' ] )) {

      $next_data[ 'flg' ] = 'false';

    } else {

      $folder_data = $data[ 'folder_data' ];
      $student_id = $data[ 'student_id' ];

      $select = $this->type_result( $contents );
      $select_string = $select[ 'select' ];

      $skip_contents = [];

      foreach ( $folder_data as $key => $value ) {
        // 2019/6/03 count関数対策
        $f_count = 0;
        if(is_countable($folder_data[ $key ])){
          $f_count = count( $folder_data[ $key ] );
        }
        //$f_count = count( $folder_data[ $key ] );
        // bid が入っている為、-1
        for ( $f = 0; $f <= $f_count-1; $f++ ) {

          if ( $folder_data[ $key ][ $f ][ 'primary_key' ] == $select[ 'id' ] &&
            $folder_data[ $key ][ $f ][ 'title' ] == $select[ 'title' ] ) {

              $fn = 1;
              $fs = 0;

              if ( $folder_data[ $key ][ $f+1 ][ 'next_flg' ] == 1 ) {

                for ( $is = $f+1; $is < $f_count; $is++ ) {
                  // 次のコンテンツが受講済の場合
                  if ( $folder_data[ $key ][ $is ][ 'next_flg' ] == 0 ) {

                    $next_data = $this->next_page_url( $folder_data[ $key ][ $is ], $student_id,
                      $folder_data[ $key ][ 'bid' ], $skip_contents );
                    return $next_data;

                  } else {

                    $skip_contents[ 'skip_contents' ][ $fs ][ 'title' ] = $folder_data[ $key ][ $is ][ 'title' ];
                    $skip_contents[ 'skip_contents' ][ $fs ][ 'type' ]  = $folder_data[ $key ][ $is ][ 'type' ];
                    $fs++;
                  }
                }

              } else {
                $next_data = $this->next_page_url( $folder_data[ $key ][ $f+1 ], $student_id,
                  $folder_data[ $key ][ 'bid' ], $skip_contents );
                return $next_data;
              }

          } else {
            $next_data[ 'flg' ] = "false";

          }
        }
      }

    }

    return $next_data;

  }

  public function modal_display ( $data ) {

    $c_msg = "";
    $q_msg = "";

    if ( isset( $data[ 'skip_contents' ] )) {
      // 2019/6/03 count関数対策
      $s_count = 0;
      if(is_countable($data[ 'skip_contents' ])){
        $s_count = count( $data[ 'skip_contents' ] );
      }
      //$s_count = count( $data[ 'skip_contents' ] );
      $c_msg = "The following linked content has already been taken.";
      $q_msg = "Do you want to take the next content?";
      $s_title = "<span class='finish'>";

      for ( $s = 0; $s < $s_count; $s++ ) {

        if ( $data[ 'skip_contents' ][ $s ][ 'type' ] == 0 ) {
          $s_title .= "<span class='type movie'>Video class</span> ". $data[ 'skip_contents' ][ $s ][ 'title' ]."<br>";
        } else if ( $data[ 'skip_contents' ][ $s ][ 'type' ] == 1 ) {
          $s_title .= "<span class='type questionnaire'>questionnaire</span> ". $data[ 'skip_contents' ][ $s ][ 'title' ]."<br>";
        } else if ( $data[ 'skip_contents' ][ $s ][ 'type' ] == 2 ) {
          $s_title .= "<span class='type report'>report</span>". $data[ 'skip_contents' ][ $s ][ 'title' ]."<br>";
        } else {
          $s_title .= "<span class='type quiz'>quiz</span>". $data[ 'skip_contents' ][ $s ][ 'title' ]."<br>";
        }
        //$s_title[] = explode( '<br>', $data[ 'skip_contents' ][ $s ][ 'title' ] );
      }
      $s_title .= "</span>";

    } else {

      $c_msg = "There is content associated with it.";
      $q_msg = "Do you continue to attend?";
      $s_title = "";
    }

    if ( isset( $data[ 'rq_result' ] )) {
      $quiz_result = $data[ 'rq_result' ];
    } else {
      $quiz_result = "";
    }


    //return $s_title;

    $modal = '<div class="modal fade contents-continuity" id="contents-continuity" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="text">' .$c_msg. '<br>'.$s_title .$q_msg. '</p>
                    <!--<button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>-->
                </div>
                <div class="modal-body">
                    <p class="'.$data[ 'class' ].'">'.$data['type_string'].'</p>
                    <!-- <p class="type mp4">Video class(MP4 format)</p> -->
                    <!-- <p class="type test">quiz</p> -->
                    <!-- <p class="type questionnaire">questionnaire</p> -->
                    <!-- <p class="type report">report</p> -->
                    <p class="title">'.$data['title'].'</p>
                    <ul class="btns">
                      <li><button class="ok" data-next='.$data['url'].'>To attend</button></li>
                      '.$quiz_result.'
                      <li><button class="cancel">I will attend later</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';

    /*
    $modal = '<div class="modal fade contents-continuity" id="contents-continuity" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="text">関連付けされているコンテンツがあります。<br>続けて受講しますか？</p>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="type tb">Video class(TB形式)</p>
                    <!-- <p class="type mp4">Video class(MP4 format)</p> -->
                    <!-- <p class="type test">quiz</p> -->
                    <!-- <p class="type questionnaire">アンケート</p> -->
                    <!-- <p class="type report">レポート</p> -->
                    <p class="title">コンテンツタイトル</p>
                    <ul class="btns">
                      <li><button class="ok" data-next=>受講する</button></li>
                      <li><button class="cancel">後で受講する</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>';
    */
    return $modal;

  }


}



?>
