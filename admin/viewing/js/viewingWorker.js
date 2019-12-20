// log 再取得
onmessage = function ( event ) {

  var sendData = event.data;
  var worker = 'workersenddata';

  //console.dir( sendData );
  /*
  var student_id      = sendData.student_id;
  var contents_number = sendData.contents_id;
  */
  var reacquireData = {
    student_id: sendData.student_id,
    contents_number: sendData.contents_number
  };

  //console.dir(reacquireData);

  function EncodeHTMLForm ( data ) {

    var params = [];

    for ( var name in data ) {
      var value = data[ name ];
      var param = encodeURIComponent( name ) + '=' + encodeURIComponent( value );
      params.push( param );
    }
    return params.join( '&' ).replace( /%20/g, '+' );
  }

  //console.dir( EncodeHTMLForm(reacquireData));

  var req = new XMLHttpRequest();

  req.onreadystatechange = function ( data ) {

    //console.dir( data );

    if ( req.readyState == 4 ) { // 通信完了
      if ( req.status == 200 ) { // 通信成功
        postMessage( "完了" );
      }
    } else {
        postMessage( "通信中" );
    }
  }

  req.open( 'POST', '../reacquire_log_data.php', true );
  req.setRequestHeader ( 'content-type',
    'application/x-www-form-urlencoded;charset=UTF-8' );
  req.send( EncodeHTMLForm(reacquireData));

  var rData = eval( req.responseText );
  //console.dir( rData );

  /*
  $.ajax(
    '../viewing/reacquire_log_data.php',
    {
      type: 'POST',
      contentsType: 'application/x-www-form-urlencoded;charset=utf-8',
      data:
      {
        student_id: sendData.student_id,
        contents_number: contents_id
      }
    }
  ).done ( function ( data ) {
    var returnData = JSON.parse( data );
    postMessage( returnData );
  }).fail ( function ( data ) {
    postMessage( "失敗");
  })
  */

}
