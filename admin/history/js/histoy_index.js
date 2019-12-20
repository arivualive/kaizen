"use strict"

var attributeStudent;

$( document ).ready( function () {

  $( '.student_history' ).hide();

 function tableCreate ( data1, data2, category, student_id ) {

   var csvbtn = $( "#download-csv2" );

   var categoryName = $( '[name=junleSelect] option:selected' ).text();


   //console.dir( data1 );
   //console.dir( data2 );

   $( "#example-table2" ).t({
     height:"500px",
     //layout:"fitData",
     //layout:"fitColumns",
     //layoutColumnsOnNewData:true,
     //resizableColumns:true,
     selectable:1,
     selectableRollingSelection:true,
     //fitColumns:true,
     tooltipsHeader:true,
     movableCols:true,
     movableRows:true,
     tooltips:true,
     columnVertAlign: "bottom",
     columns: data1,
     rowClick: function( e, row ){

       var rowData;
       rowData = row.getData();

       console.dir( rowData );
       //console.dir( rowData.title );
       //console.dir( rowData.section );
       //console.dir( rowData.name );
       //console.dir( category );

       var sendURL = {
         viewingURL       : "../viewing/index.php?st="+student_id+"&id="+rowData.id+"&bid="+student_data.genreId+
          "&ca="+encodeURI(categoryName)+"&se="+encodeURI(rowData.section)+"&ti="+encodeURI(rowData.title)+"&na="+encodeURI(rowData.name),

         quizURL          : "../quiz/result/detail.php?id="+rowData.id+"&an="+rowData.answer_id+"&st="+student_id+"&bid="+student_data.genreId,
         questionnaireURL : "../questionnaire/analysis.php?id="+rowData.id+"&bid="+student_data.genreId,
         reportURL : "../report/analysis.php?id="+rowData.id+"&bid="+student_data.genreId
       };

       //console.dir( sendURL.viewingURL );

       if ( rowData.id == '-' ) {
         alert( 'There is no target content' );
       } else {

         switch ( category ) {

           case "0":
            if ( rowData.type == 'Video' ) {

              window.open( sendURL.viewingURL, 'viewing' );

            } else if ( rowData.type == 'quiz' ) {

              window.open( sendURL.quizURL, 'quiz' );

            } else if ( rowData.type == 'questionnaire' ) {

              window.open( sendURL.questionnaireURL, 'questionnaire' );

            } else if ( rowData.type == 'report' ) {
              window.open( sendURL.reportRL, 'report' );

            } else if ( rowData.id == '-' ) {

              alert( 'There is no target content' );

            }
            break;

           case "1":
            if ( rowData.id == '-' ) {
              alert( 'There is no target video' );
            } else {
              //sendViewingURL = "../../admin/viewing/index.php?st="+student_id+"&id="+rowData.id;
              //location.href = sendURL.viewingURL;
              window.open( sendURL.viewingURL, 'viewing' );
            }
            break;

           case "2":
            if ( rowData.id == '-' ) {
              alert( 'There is no target quiz' );
            } else {
              //sendQuizURL = "../../admin/quiz/result/detail.php?id="+student_id+"&an="+rowData.answer_id+"&st="+rowData.id+"&bid="+student_data.genreId;
              //location.href = sendURL.quizURL;
              window.open( sendURL.quizURL, 'quiz' );
            }
            break;

           case "3":
            if ( rowData.id == '-' ) {
              alert( 'There is no target questionnaire' );
            } else {
              //sendQuestionnaireURL = "../../admin/questionnaire/analysis.php?id="+student_id+"&bid="+student_data.genreId;
              //location.href = sendURL.questionnaireURL;
              window.open( sendURL.questionnaireURL, 'questionnaire' );
            }
             break;

           case "4":
            if ( rowData.id == '-' ) {
              alert( 'There is no target report' );
            } else {
              //sendQuestionnaireURL = "../../admin/questionnaire/analysis.php?id="+student_id+"&bid="+student_data.genreId;
              //location.href = sendURL.questionnaireURL;
              window.open( sendURL.reportURL, 'report' );

            }
           break;

           default:

         }

       }

     }

   });

   $("#example-table2").t( "setData", data2 );

   csvbtn.prop ( "disabled", false );

 };

 function student_data () {
   this.studentData;
   this.studentLength;
   this.genreId;
   this.studentIndex;
 };

 student_data.prototype = function ( data, data2, data3, data4,data5 ) {
   this.studentData = data;
   this.studentLength = data2;
   this.genreId = data3;
   this.studentIndex = data4;
   this.schoolID = data5;
 };

 function student_results () {
   this.studentResults;
 };

 student_results.prototype = function ( data ) {
   this.studentResults = data;
 };

 function attribute () {
   this.attributeResults;
 };

 attribute.prototype.resultsAttrbute = function ( data, data2 ) {
   this.attributeResults = data;
   this.flg = data2;
 };

 attributeStudent = new attribute();


 function attributeDisplay2 ( attribute, show ) {

   for ( var le = 0; le < attribute.length; le++ ) {
     var select = attribute[ le ];
     //$( "#example-table" ).t( show, select );
     $( "#example-table" ).t( show, select );
     //console.dir(attribute[ le ].toggle());
   }

 };

 function EncodeUrl ( string ) {

   return encodeURI( string );
 }

/*
 function jsonParseData ( data ) {

   var studentData;
   var str_array = data.split('</font>');
   var st = str_array[ str_array.length -1 ];
   var sgfs = st.split('</font>');
   studentData = JSON.parse( sgfs );
   return studentData;
 }
*/
 function loadingImage ( msg ) {
   if ( msg == undefined ) {
     msg = "";
   }

   var dispMsg = "<div class='loadingMsg'>"+msg+"</div>";

   if ( $( "#loading" ).length == 0 ) {
     $( "body" ).append( "<div id='loading'>"+dispMsg+"</div>" );
   }
 }

 function removeLoading () {
   $( "#loading" ).remove();
 }

 // first-table CSV　ボタン
 function downloadCsv ( data ) {

   var header = {};
   var sectionData = {};

   header = Object.keys ( data.csv_header ).map( function ( key ) {
     return data.csv_header[ key ];
   });

   sectionData = Object.keys( data.csv_student_data ).map( function ( key ) {
     return data.csv_student_data[ key ];
   });

   var vq = {};

   vq = {
     fileCsv : $( "#file_csv" ),
     postForm : $( "#post-form" ),
     keyData : $( "#key_data" )
   };

   var argument = [];

   argument = Papa.unparse( {
          fields : header,
          data : sectionData
   });

   vq.postForm.attr(
         "action",
         "../../core/php/export_csv_data.php"
   );

   vq.keyData.attr( "value", argument );
   vq.postForm.submit();

 }


 function secondTableAllCsvResults ( genreValue, firstNumber, secondNumber, category_number, school_id ) {

   $.ajax (
     '../history/history_model.php',
     {
       type: 'POST',
       contentType: 'application/x-www-form-urlencoded;charset=utf-8',
       data: {
         genre_id: genreValue,
         first_number: firstNumber,
         second_number: secondNumber,
         category: category_number,
         school_id: school_id
       }
     }
   )
   .done( function ( data ) {

     var studentData;
     //studentData = jsonParseData( data );
     studentData = JSON.parse( data || "null" )
     downloadCsv( studentData );

  });
 }

// genre select-box
$( '#genre' ).change( function (e) {

  loadingImage( "processing" );

  var genreValue    = $( this ).val();
  var attributeText = $( '#attribute' ).text();
  var genreId       = "genreId";
  var school_id     = e.target.getAttribute( 'data-schoolID' );

  // Affiliation indication・非表示切替え
  if ( attributeText == 'Hide affiliation' ) {
    $( '#attribute' ).text( 'Affiliation indication' );
  };

  $( "#example-table" ).remove();

  //$( "#attribute" ).after('<div id="example-table"></div>');
  $( "#update-time" ).after('<div id="example-table"></div>');

  if ($( '.student_history' ).is(':visible')) {
    // 表示されている場合の処理
    $( '.student_history' ).hide();
  } else {
    // 非表示の場合の処理
  }

  // genre_id に紐づく講座を取得しに行く
  $.ajax (
    '../history/history_model.php',
    {
      type: 'POST',
      contentType: 'application/x-www-form-urlencoded;charset=utf-8',
      data: {
        genre_id: genreValue,
        school_id: school_id
      }
    }
  )
  .done ( function ( data ) {

    var return_data;

    $( '#download-csv' ).prop( 'disabled', false );

    return_data = JSON.parse( data || "null" );
    //return_data = data;

    attributeStudent.resultsAttrbute( return_data, "showColumn" );

    //console.dir( return_data );


	var download_file = 'json/' + $('#genre option:selected').val(); //ダウンロード先変更
	var junleSelect = $('#genre option:selected').text().replace('　　', '').replace('▼', ''); //ダウンロード名変更
	var replace = {
		'0':/０/g, '1':/１/g, '2':/２/g, '3':/３/g, '4':/４/g, '5':/５/g, '6':/６/g, '7':/７/g, '8':/８/g, '9':/９/g,
		'-':/－/g, '/':/／/g, ' ':/　/g
	};

	$.each(replace, function(i, elem) {
		junleSelect = junleSelect.replace(elem, i);
	});
	//console.log(junleSelect);

	var now = new Date();
	var m = now.getMonth() + 1;
	var d = now.getDate();
	var today = ('0' + m).slice(-2) + ('0' + d).slice(-2); //今日の月日

	if(download_file == 'json/dummy') {
		$('#single-csv').attr('href', '#single-csv');
		$('#single-csv').removeAttr('download');
		$('#multi-csv').attr('href', '#multi-csv');
		$('#multi-csv').removeAttr('download');
	} else {
		$('#single-csv').attr({href:download_file + '.csv', download:junleSelect + '-' + today + '.csv'});
		$('#multi-csv').attr({href:download_file + 'd.csv', download:junleSelect + '-Details' + today + '.csv'});
	}
	//$('#single-csv').text('CSV出力'); //ダウンロードリンク表示
	//$('#multi-csv').text('詳細CSV出力'); //ダウンロードリンク表示


    $( "#update-time" ).text('Data update - ' + return_data.update_time); //更新時間を挿入

    $( "#example-table" ).t({
      height:"auto",
      //width:"300px",
      layout:"fitColumns",
      tooltipsHeader:true,
      pagination:"local",
      paginationSize:15,
      selectable:1,
      selectableRollingSelection:true,
      //fitColumns:true,
      movableCols:true,
      movableRows:true,
      tooltips:true,
      columnVertAlign: "bottom",
      columns: return_data.table_create,
      //toggleColumn: "school_name-1",
      rowClick:function(e, row){

        loadingImage( "processing" );

        var studentHistory = [];
        var studentLength  = return_data.student_data.length;
        //var junleSelect    = $( '[name=junleSelect] option:selected' ).text();
        studentHistory     = row.getData();

        student_data.prototype( studentHistory, studentLength, genreValue, studentHistory.number, school_id, return_data.attribute );

        $( '.student_history' ).show();
        $( "#example-table2" ).remove();

        $( "#name" ).html( studentHistory.sn );
        $( "#category" ).html( "【Content category】"+junleSelect );
        $( "#errmsg" ).after( '<div id="example-table2"></div>' );
        var categorySelect = $( "#categorySelect" ).val();

        $( '.csv_number' ).attr({
          'min':1,
          'max':studentLength

        });

        // 送信するデータを配列からObjectへ変換
        var tableObject = {};
        var testobj = {};
        tableObject = return_data.table_create;
        // genre_id に紐づく講座を取得しに行く
        $.ajax (
          '../history/history_model.php',
          {
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded;charset=utf-8',
            data: {
              genre_id: genreValue,
              student_id: studentHistory.sid,
              category: categorySelect,
              table_type: 2,
              school_id: school_id
            }
          }
        )//<br />
        .done ( function ( data ) {
          var studentData;

          //studentData = jsonParseData ( data );
          studentData = JSON.parse( data );
          student_results.prototype ( studentData );

          //console.dir( studentData );

          tableCreate (
            studentData.contents_table,
            studentData.contents,
            categorySelect,
            studentHistory.sid
          );

        })
        .fail ( function ( data ) {

        })
        .always( function ( data ) {
          removeLoading();
        });

      }

    });

    $("#example-table").t( "setData", return_data.student_data );

    // table-1 csv
    $( "#download-csv" ).click( function ( data ) {
      downloadCsv( return_data );
    });

    // table2 csv
    $( "#download-csv2" ).click( function () {

      var firstNumber   = $( "#firstNumber" ).val();
      var secoundNumber = $( "#secoundNumber" ).val();

      if ( firstNumber == "" && secoundNumber == "" ) {

        firstNumber   = student_data.studentIndex;
        secoundNumber = student_data.studentIndex;

      }

      var studentResultsData = student_results.studentResults;
      var categoryNumber = $( "#categorySelect" ).val();

      if ( categoryNumber != "0" ) {

        downloadCsv ( studentResultsData );

      } else {

          if ( secoundNumber < firstNumber ) {
            $( "#errmsg" ).html( "※ Please enter a larger number than the left" );

          } else if ( firstNumber == "" ) {
            $( "#errmsg" ).html( "※ Please enter the numbers on the left" );

          } else if ( secoundNumber == "" ) {
            $( "#errmsg" ).html( "※ Please enter the number on the right" );

          } else if ( secoundNumber > student_data.studentLength ) {
              secoundNumber = student_data.studentLength;

              secondTableAllCsvResults (
                student_data.genreId,
                firstNumber,
                secoundNumber,
                categoryNumber,
                student_data.schoolID
              );

          } else if ( secoundNumber == firstNumber ) {

              secondTableAllCsvResults (
                student_data.genreId,
                firstNumber,
                secoundNumber,
                categoryNumber,
                student_data.schoolID
              );

          } else {

            secondTableAllCsvResults (
              student_data.genreId,
              firstNumber,
              secoundNumber,
              categoryNumber,
              student_data.schoolID
            );
          }

      }

    });

  })
  .fail( function () {
    //console.dir( "fail" );
  })
  .always( function ( data ) {
    removeLoading();
  });


  $( "#categorySelect" ).change( function ( e ) {

    loadingImage( "processing" );

    var changeCategory, school_id2;

    changeCategory = $( this ).val();
    school_id2     = e.target.getAttribute( 'data-schoolID2' );

    $( '#download-csv2' ).prop( 'disabled', false );

    var firstInput  = $( "#firstNumber" );
    var secondInput = $( "#secoundNumber" );
    var csvbtn      = $( "#download-csv2" );

    $( "#example-table2" ).remove();

    csvbtn.prop( "disabled", true );

    if ( changeCategory !== "0" ) {

        $( '#inputNumber' ).hide();
        $( "#categorySelect" ).after( csvbtn );
        csvbtn.after( '<div id="example-table2"></div>' );

    } else {

      $( '#inputNumber' ).show();
      $( '#secoundNumber' ).after( csvbtn );

      csvbtn.after( '<div id="example-table2"></div>' );
    }


    $.ajax (
      '../history/history_model.php',
      {
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded;charset=utf-8',
        data: {
          genre_id: genreValue,
          student_id: student_data.studentData.sid,
          category: changeCategory,
          table_type: 2,
          school_id: school_id2
        }
      }
    )
    .done ( function ( data ) {

      var returnStudentTable;

      //returnStudentTable = jsonParseData ( data );
      returnStudentTable = JSON.parse( data );
      student_results.prototype( returnStudentTable );

      switch ( changeCategory ) {
        case "0":
          tableCreate (
            returnStudentTable.contents_table,
            returnStudentTable.contents,
            changeCategory,
            student_data.studentData.sid
          );
          break;

        case "1":
          tableCreate (
            returnStudentTable.contents_table,
            returnStudentTable.contents,
            changeCategory,
            student_data.studentData.sid
          );
          break;

        case "2":
          tableCreate (
            returnStudentTable.contents_table,
            returnStudentTable.contents,
            changeCategory,
            student_data.studentData.sid
          );
          break;

        case "3":
          tableCreate (
            returnStudentTable.contents_table,
            returnStudentTable.contents,
            changeCategory,
            student_data.studentData.sid
          );
          break;

        case "4":
          tableCreate (
            returnStudentTable.contents_table,
            returnStudentTable.contents,
            changeCategory,
            student_data.studentData.sid
          );
          break;
        default:

      }

    })
    .fail ( function ( data ) {

    })
    .always( function ( data ) {
      removeLoading();
    });

  });

});

// Affiliation indication・非表示切替え
$( function () {
  //var flg = "showColumn";
  $( '#attribute' ).click( function () {

    var display = $( this ).text();

    if ( attributeStudent.flg == "showColumn" ) {
      $( this ).text( 'Hide affiliation' );
      //$("#example-table").t("showColumn","school_name-1");
      attributeDisplay2( attributeStudent.attributeResults.attribute, attributeStudent.flg );
      attributeStudent.flg = "hideColumn";
    } else {
      $( this ).text( 'Affiliation indication' );
      //$("#example-table").t("hideColumn","school_name-1");
      attributeDisplay2( attributeStudent.attributeResults.attribute, attributeStudent.flg );
      attributeStudent.flg = "showColumn";
    }

  });
});

});
