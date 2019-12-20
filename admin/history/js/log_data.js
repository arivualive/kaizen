
$( document ).ready( function () {

  $( '.require_button' ).on( 'click', function ( e ) {

    var contentID = e.target.getAttribute( 'data-cid' );
    var studentID = e.target.getAttribute( 'data-sid' );
    var historyID = e.target.getAttribute( 'data-hid' );

    $.ajax(
      '../history/reacquire_log_data.php',
      {
        type: 'POST',
        contentsType: 'application/x-www-form-urlencoded;charset=utf-8',
        data:
        {
          student_id: studentID,
          contents_number: contentID
        }
      }
    ).done ( function ( data ) {
      console.dir( historyID );
    }).fail ( function () {
      console.dir( 'error' );
    });

  });

});
