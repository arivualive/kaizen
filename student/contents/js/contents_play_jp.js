"use strict"
// student_data格納用
var data;
$( '#stop' ).attr( 'disabled', true );

$( document ).ready( function () {

  var $script = $( '#script' );

  // phpからstudent情報を取得
  var session = JSON.parse($script.attr('data-session'));
  // student_data格納
  // 各種情報格納用
  function player_data () {
    this.p3Code;
    this.history;
    this.timerID;
  };

  player_data.prototype.studentData = function ( data ) {

    this.contentsNumber = data.contents_id;
    this.schoolId       = data.school_id;
    this.studentID      = data.student_id;
    this.studentName    = data.student_name;
    this.url2           = '../../../file/contents/';
    this.startFrame     = data.start_frame;
    this.proportion     = data.proportion;
    this.comment        = data.comment;
    this.contentsName   = data.contents_name;
    this.extensionId    = data.extension_id;
    this.extension      = data.extension;
    this.size           = data.size;
    this.sectionId      = data.section_id;
    this.swalFlg;
    this.startFrameFlg;
    this.flg           = data.flg;

  };

  data = new player_data();
  data.studentData( session );

  // Player3 再生用関数
  function playerPlaying ( player, url, datas, sendCode/*, sendReachedFrame, startFrame*/ ) {
    player.getFile( url, {
      playMode : tbwp3.PLAY_MODE.LOG,
      startFrame : 0, //datas.startFrame,
      returnCode : function ( code ) {
        datas.p3code = code;
        sendCode( datas, sendReachedFrame, player );
      }
    });

    $( '#stop' ).attr( 'disabled', false );
  }

  // ログ初期データ格納用関数
  var sendP3Code = function ( code, sendReachedFrame, player ) {
    $.ajax(
      '../contents/contents_log_first_data.php',
      {
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        data: {
          player3_code: code.p3code,
          student_id: code.studentID,
          contents_number: code.contentsNumber
        }
      }
    )
    .done( function ( data ) {
      var history = JSON.parse( data ) ;
      code.history = history[ 0 ][ 'history_id' ];
      sendReachedFrame( player, code );
    })
    .fail( function () {
      console.dir( "picker失敗" );
    });

  };

  // reached_frame格納用関数
  var sendReachedFrame = function ( player, code ) {
    var r_frame = player.getLog();
    var url, history_id;

    if ( r_frame !== null ) {
      try {
          url = '../contents/update_reached_frame.php',
          history_id = code.history;
          $.ajax(
            url,
            {
              type : 'POST',
              contentType : 'application/x-www-form-urlencoded;charset=utf-8',
              data : {
                reached_frame : r_frame.reachedFrame,
                duration : r_frame.frameLength,
                history_number : history_id
              }
            }
          )
          .done ( function ( data ) {
          })
          .fail ( function () {
            console.dir( "reached_frame_miss" );
          })
      } catch (e) {
      } finally {
      }
    }
    code.timerID  = setTimeout( function () {
      sendReachedFrame( player, code );
    },1000 );

  };

  // player3 log 格納用関数
  function logInsert ( code, next ) {
    $.ajax(
      '../contents/contents_log_event_data.php',
        {
          type: 'POST',
          contentType: 'application/x-www-form-urlencoded;charset=utf-8',
          dataType:'json',
          data: {
            player3_code: code.p3code,
            student_id: code.studentID,
            contents_number: code.contentsNumber,
            history_number: code.history
          }
        }
      )
      .done( function ( data ) {
        code.proportion = data.proportion;
        var proportion_data = JSON.parse( data );
        location.href = next;
      })
      .fail( function () {
        //console.dir( "ajax-miss" );
        location.href = next;
      });
  }

  var player;

  $( function () {
    // 再生ボタンを押された際の処理
    var path, playerWrap, filePath, divPlayer;
    divPlayer = document.getElementById( 'player' );
    player = tbwp3.entry( divPlayer, { isUsingTbLogo : false } );
    // copntents 再生
    //$( '.contents_play' ).click( function () {
      if ( data.extension === '.mp4' || data.extension === '.MP4' ) {
        path = data.url2 + data.contentsNumber + data.extension;
      } else {
        path = data.url2 + data.contentsNumber + '.deploy';
      }
      playerWrap = document.getElementById( 'player_wrap' );
      playerPlaying ( player, path, data, sendP3Code/*, sendReachedFrame, startFrame */);
    // ログ更新ボタン処理
    $( '#stop' ).click( function () {

      player.pause();
      clearTimeout( data.timerID );
      $( '#stop' ).remove();
      $( '#stopButton' ).append( '<button id="return" >視聴結果を記録</button></li>' );
      //$( '#stopButton' ).append( '<a id="return" >一覧へ戻る</a>');
    });

    $(document).on("click","#return", function() {
       player.stop();
       var msg = $( '#return' ).html();
       var $modalData = $( '#folder' ).data( 'folder' );
       var modalFlg = $( '#folder' ).data( 'folderflg' );
       var nextURL;

       if ( modalFlg == false ) {
         nextURL = "../../student/contentslist.php?bid="+data.sectionId;
         logInsert( data, nextURL );
       } else {
         $( '#return' ).attr( 'data-toggle', 'modal' );
         $( '#return' ).attr( 'data-target', '#contents-continuity' );

         var options = {"backdrop":"static"};
         $( '#contents-continuity' ).modal(options);

         $( '.ok' ).on( 'click', function () {
           var nextURL = $modalData.url;
           logInsert( data, $modalData.url );
         });

         $( '.cancel' ).on( 'click', function () {
           nextURL = "../../student/contentslist.php?bid="+data.sectionId;
           logInsert( data, nextURL );
         });
       }
   });

      /*
      player.stop();
      clearTimeout( data.timerID );

      //$( '#stop' ).html( '一覧へ戻る' );
      $( '#stop' ).html( '視聴結果を記録' );

      var msg = $( '#stop' ).html();

      var $modalData = $( '#folder' ).data( 'folder' );
      var modalFlg = $( '#folder' ).data( 'folderflg' );
      //console.dir( modalFlg );
      var nextURL;

      if ( modalFlg == false ) {


        if ( msg === '視聴結果を記録' ) {
          $( '#stop' ).click( function () {
            //console.dir( "一覧へ");
            nextURL = "../../student/contentslist.php?bid="+data.sectionId;
            logInsert( data, nextURL );
          });
        }

        //console.dir( "0" );
      } else {
        //console.dir( modalFlg );
        // data-toggle="modal" data-target="#contents-continuity
        $( '#stop' ).attr( 'data-toggle', 'modal' );
        $( '#stop' ).attr( 'data-target', '#contents-continuity' );

        var options = {"backdrop":"static"};
        $( '#contents-continuity' ).modal(options);


        $( '.ok' ).on( 'click', function () {
          var nextURL = $modalData.url;
          logInsert( data, $modalData.url );
        });

        $( '.cancel' ).on( 'click', function () {
          nextURL = "../../student/contentslist.php?bid="+data.sectionId;
          logInsert( data, nextURL );
        });

      );

      }

      var msg = $( '#stop' ).html();

    });
    */
  });


  // 最初から or 途中から再生選択
  /*
  function reachedFrameDialog ( datas, play, player, url, fu, sendCode, sendReachedFrame, startFrame ) {
    swal({
      title             : '前回の続きから再生しますか？',
      text              : 'ボタンで選択してください',
      type              : 'info',
      allowOutsideClick : false,
      showCancelButton  : true,
      confirmButtonText : '最初から',
      confirmButtonColor: 'Blue',
      closeOnConfirm    : true,
      closeButtonColor  : 'red',
      cancelButtonText  : '続きから'
    }, function ( isConfirm ) {
      // 最初からを選択 true
      if ( isConfirm ) {
        fu.swalFlg = true;
        startFrame = 0;
        play( player, url, fu, sendCode, sendReachedFrame, startFrame );

      } else {
        // 続きからを選択 false
        fu.swalFlg = false;
        play( player, url, fu, sendCode, sendReachedFrame, startFrame );

      }
    })

  };
  */
  // 前回までの視聴フレームを元に再生選択用関数
  //function contentsPlay ( play, player, url, datas, sendCode/*, sendReachedFrame, reachedFrameDialog*/ ) {
    // 続き再生用 reached_frame 取得

    //play ( player, url, datas, sendCode/*, sendReachedFrame, startFrame */);
    //$( '#stop' ).attr( 'disabled', false );
    /*
    $.ajax(
      '../contents/start_reached_frame.php',
      {
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        data: {
          student_id: datas.studentID,
          contents_number: datas.contentsNumber
        }
      }
    )
    .done ( function ( data ) {

      var startFrame;
      var return_frame = {};

      return_frame = JSON.parse( data );


      // 初回視聴

      if ( return_frame.reached_frame_flg == false ) {
        startFrame = 0;
        play ( player, url, datas, sendCode, sendReachedFrame, startFrame );

      } else {
        // 続きから or 最初から再生選択 popup表示
        startFrame = return_frame.reached_frame;
        reachedFrameDialog ( datas, play, player, url, datas, sendCode, sendReachedFrame, startFrame );
      }

      $( '#stop' ).attr( 'disabled', false );

    })
    .fail( function () {
      console.dir( "reached_frame_miss");
    })
    */
  //}



});
