"use strict"
// student_data格納用
var data;

$( document ).ready( function () {

  var $script = $( '#script' );
  // phpからstudent情報を取得
  var session = JSON.parse($script.attr('data-session'));
  // student_data格納
  console.dir( session );
  data = new player_data();
  data.studentData( session );

});

// 各種情報格納用
function player_data ( ) {
  this.p3Code;
  this.history;
  this.timerID;
};

player_data.prototype.studentData = function ( data ) {
  this.schoolId    = data.school_id;
  this.studentID   = data.student_id;
  this.studentName = data.student_name;
  this.url2        = data.url;
  this.startFrame  = data.start_frame;
  this.proportion  = data.proportion;
  this.swalFlg;
  this.startFrameFlg;
};

// 最初から or 途中から再生選択
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
      //return isConfirm;
    } else {
      // 続きからを選択 false
      fu.swalFlg = false;
      play( player, url, fu, sendCode, sendReachedFrame, startFrame );
      //return isConfirm;
    }
  })

}

// 前回までの視聴フレームを元に再生選択用関数
function contentsPlay ( play, player, url, datas, sendCode, sendReachedFrame, reachedFrameDialog ) {
  // 続き再生用 reached_frame 取得
  $.ajax(
    'https://rafi.develop.kjs/student/contents/start_reached_frame.php',
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

  })
  .fail( function () {
    console.dir( "reached_frame_miss");
  })

}

// Player3 再生用関数
function play ( player, url, datas, sendCode, sendReachedFrame, startFrame ) {

  player.getFile( url, {
    playMode : tbwp3.PLAY_MODE.LOG,
    startFrame : startFrame, //datas.startFrame,
    returnCode : function ( code ) {
      datas.p3code = code;
      sendCode( datas, sendReachedFrame, player );
    }
  });

}

// 'https://ookubo.develop.kjs/student/contents/contents_log_first_data.php',
// ログ初期データ格納用関数
var sendP3Code = function ( code, sendReachedFrame, player ) {
  $.ajax(
    code.url2 + 'student/contents/contents_log_first_data.php',
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
    code.history = history[0][ "history_id" ];
    sendReachedFrame( player, code );
  })
  .fail( function () {
    console.dir( "picker失敗" );
  });

}

// reached_frame格納用関数
var sendReachedFrame = function ( player, code ) {

  var r_frame = player.getLog();
  var url, history_id;
  //console.dir( r_frame );
  if ( r_frame !== null ) {
    // https://ookubo.develop.kjs/student/contents/update_reached_frame.php
    try {
        url = `https://rafi.develop.kjs/student/contents/update_reached_frame.php`;
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

// https://ookubo.develop.kjs/student/contents/contents_log_event_data.php
// player3 log 格納用関数
function logInsert ( code ) {

  $.ajax(
    'https://rafi.develop.kjs/student/contents/contents_log_event_data.php',
      {
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        data: {
          player3_code: code.p3code,
          student_id: code.studentID,
          contents_number: code.contentsNumber,
          history_number: code.history
        }
      }
    )
    .done( function ( data ) {

      /*code.proportion = data.proportion;*/
      console.dir( data.proportion );
      var code = [];
      code.proportion = data.proportion;
      console.dir( code );
      var proportion_data = JSON.parse( data );
      console.dir( proportion_data );

      $( '#proportion' ).text( proportion_data + " %" );
    })
    .fail( function () {
      console.dir( "ajax失敗" );
    });

}

var player;

$( function () {
  // 再生ボタンを押された際の処理
  var divPlayer = document.getElementById( 'player' );
  player = tbwp3.entry( divPlayer, { isUsingTbLogo : false } );
  // copntents 再生
  $( '.contents_play' ).click( function () {

    var playerWrap = document.getElementById( 'player_wrap' );
    var filePath 　= 'https://coc-lms.tbshare.net/test/file/school_contents/809.deploy';
    // contents_number を取得
    data.contentsNumber = $( this ).val();
    contentsPlay ( play, player, filePath, data, sendP3Code, sendReachedFrame, reachedFrameDialog );

  });

  // 停止ボタンを押された際の処理
  $( '#stop' ).click( function () {
    player.pause();
    clearTimeout( data.timerID );
  });

  // ログ更新ボタン処理
  $( '#update' ).click( function () {
    setTimeout( function() {
      logInsert( data );
    },5000/*10000 */);

  })

});
