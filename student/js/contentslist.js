$(function() {

$('.sort-dummy-01').on('click', function () {
    $(".list-dummy-01").show();
    $(".list-dummy-02").show();
    $(".list-dummy-03").show();
    $(".list-dummy-04").show();
});
$('.sort-dummy-02').on('click', function () {
    $(".list-dummy-01").show();
    $(".list-dummy-02").hide();
    $(".list-dummy-03").hide();
    $(".list-dummy-04").hide();
});
$('.sort-dummy-03').on('click', function () {
    $(".list-dummy-01").hide();
    $(".list-dummy-02").hide();
    $(".list-dummy-03").hide();
    $(".list-dummy-04").show();
});
$('.sort-dummy-04').on('click', function () {
    $(".list-dummy-01").hide();
    $(".list-dummy-02").show();
    $(".list-dummy-03").hide();
    $(".list-dummy-04").hide();
});
$('.sort-dummy-05').on('click', function () {
    $(".list-dummy-01").hide();
    $(".list-dummy-02").hide();
    $(".list-dummy-03").show();
    $(".list-dummy-04").hide();
});

// log再取得ボタン
$( '.reacquire_results' ).click( function ( e ) {

  //スクロールバー情報の取得
  $scrool_value = $(window).scrollTop();

  var contentID, studentID, bid;

  contentID = e.target.getAttribute( 'data-contentID2' );
  studentID = e.target.getAttribute( 'data-studentID' );
  bid       = e.target.getAttribute( 'data-bid' );
  /*
  console.dir( contentID );
  console.dir( studentID );
  console.dir( bid );
  */
  $.ajax(
    '../student/contents/reacquire_log_data.php',
    {
      type: 'POST',
      contentsType: 'application/x-www-form-urlencoded;charset=utf-8',
      data:
      {
        student_id: studentID,
        contents_number: contentID,
        send_file: 'contentslist'
      }
    }
  ).done ( function ( data ) {
    location.href = "../student/contentslist.php?bid=" + bid + "&sc=" + $scrool_value;;
    //console.dir(data);
  }).fail ( function () {
    console.dir( "no-return" );
  });

})


// log-data再取得 詳細ボタン
$( '.contents_info' ).click( function ( e ) {

  //スクロールバー情報の取得
  $scrool_value = $(window).scrollTop();
  var contentID, studentID, bid;

  contentID = e.target.getAttribute( 'data-contentID2' );
  studentID = e.target.getAttribute( 'data-studentID' );
  bid       = e.target.getAttribute( 'data-bid' );

  $.ajax(
    '../student/contents/reacquire_log_data.php',
    {
      type: 'POST',
      contentsType: 'application/x-www-form-urlencoded;charset=utf-8',
      data:
      {
        student_id: studentID,
        contents_number: contentID,
        send_file: 'contentslist'
      }

    }
  ).done ( function ( data ) {

    $('div[id$=' + contentID + ']' ).on('hidden.bs.modal', function () {
      location.href = "../student/contentslist.php?bid=" + bid + "&sc=" + $scrool_value;
    });

  }).fail ( function () {
    console.dir( "no-return" );
  });

  });
});
