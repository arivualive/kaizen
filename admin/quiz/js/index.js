
//console.dir("result");

function downloadCsv ( data ) {

  var header = {};
  var sectionData = {};

  header = Object.keys ( data.csv_header ).map( function ( key ) {
    return data.csv_header[ key ];
  });

  sectionData = Object.keys ( data.csv_student_data ).map( function ( key ) {
    return data.csv_student_data[ key ];
  });

  var vq = {};

  vq = {
    fileCsv : $( "#file_csv" ),
    postForm : $( "#post-form" ),
    keyData : $( "#key_data" )
  };

  var argument = [];

  argument = Papa.unparse({
    fields : header,
    data : sectionData
  });

  vq.postForm.attr(
    "action",
    "https://thinkboard.jp/web_lms3/php/export_csv_data.php"
  );

  vq.keyData.attr( "value", argument );
  vq.postForm.submit();

}



$( function () {

  // CSV 出力
  $( '#csv_results' ).click( function (e) {

    var quizId;

    quizId = e.target.getAttribute( 'data-id' );
    //console.dir( quizId );

    $.ajax (
      '../result/make_csv.php',
      {
        type: 'GET',
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        data: {
          id :quizId
        }
      }
    ).done ( function ( data ) {

      //console.dir( data );
      var csvData;
      csvData = JSON.parse( data );
      //console.dir( csvData );

      downloadCsv( csvData );


    }).fail ( function () {
      console.dir( "ダウンロード失敗" );
    });


  });
})
