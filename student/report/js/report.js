//画面描画関係
var questionnaireNumber = 0;
$(".sentence-body").hide();
$(".sentence-body").eq(0).show();

$('.start').on('click', function () {
    $(".sentence-body").eq(questionnaireNumber).hide();
    questionnaireNumber++;
    $(".sentence-body").eq(questionnaireNumber).show();
    $("#cantion").hide();
});

$('.next').on('click', function () {
    $(".sentence-body").eq(questionnaireNumber).hide();
    questionnaireNumber++;
    $(".sentence-body").eq(questionnaireNumber).show();
});

$('.back').on('click', function () {
    $(".sentence-body").eq(questionnaireNumber).hide();
    questionnaireNumber--;
    $(".sentence-body").eq(questionnaireNumber).show();
    $("#cantion").hide();
});

$('.list').on('click', function () {

  // ここからmodal処理
  var $modalData = $( '#folder' ).data( 'folder' );
  var modalFlg = $( '#folder' ).data( 'folderflg' );

  if ( modalFlg == true ) {
    var options = {
      "backdrop":"static"
    };

    $( '#contents-continuity' ).modal( options );

    $( '.ok' ).on( 'click', function () {
      location.href = $modalData.url;
    });

    $( '.cancel' ).on( 'click', function () {
      location.href = '../contentslist.php?bid=' + $modalData.bid;
    });
  } else {
    window.location.href = '../contentslist.php?bid=' + $("#bid").val();
  }
    //window.location.href = '../contentslist.php?bid=' + $("#bid").val();
});

//回答データの送信処理
$('.submit').on('click', function () {
    var query_data = [[],[],[]];
    var required = [0,0];
    var answer_flag = 0;

    //console.log($(".any"));
    for( i = 0 ; i < $(".any").length ; i++ ) {
        if($(".any")[i].attributes["value"].value == 1) {
            ++required[0];
        }
    }

    //回答データ確認
    //console.log($(".question"));
    for( i = 0 ; i < $(".question").length ; i++ ) {

        //複数選択方式(チェックボックス)
        //console.log($(".query_data"));
        if($(".question")[i].children[2].children[0].className == "query_check") {
            answer_flag = 0;
            for( j = 0 ; j < $(".question")[i].children[2].children[0].length ; j++ ) {
                if($(".question")[i].children[2].children[0][j].checked == true) {
                    console.log($(".question")[i].children[2].children[0][j]);
                    answer_flag = 1;
                    query_data[0].push($(".question")[i].children[2].children[0][j].value);
                    query_data[1].push($(".question")[i].children[2].children[0][j].name);
                    query_data[2].push(1);
                }
            }
            if(answer_flag == 1) {
                if($(".any")[i].attributes["value"].value == 1) {
                    ++required[1];
                }
            } else {
                query_data.pop();
            }
        }
        //console.log(query_data);

        //単一選択方式(ラジオボタン)
        //console.log($(".query_data"));
        if($(".question")[i].children[2].children[0].className == "query_radio") {
            answer_flag = 0;
            for( j = 0 ; j < $(".question")[i].children[2].children[0].length ; j++ ) {
                if($(".question")[i].children[2].children[0][j].checked == true) {
                    console.log($(".question")[i].children[2].children[0][j]);
                    answer_flag = 1;
                    query_data[0].push($(".question")[i].children[2].children[0][j].value);
                    query_data[1].push($(".question")[i].children[2].children[0][j].name);
                    query_data[2].push(0);
                }
            }
            if(answer_flag == 1) {
                if($(".any")[i].attributes["value"].value == 1) {
                    ++required[1];
                }
            } else {
                query_data.pop();
            }
        }
        //console.log(query_data);

        //自由回答方式(テキスト)
        //console.log($(".query_data"));
        if($(".question")[i].children[2].children[0].className == "query_text") {
            answer_flag = 0;
            //for( j = 0 ; j < $(".query_data")[i].length ; j++ ) {
                if($(".question")[i].children[2].children[0].children[0].value != '') {
                    console.log($(".question")[i].children[2].children[0].children[0]);
                    answer_flag = 1;
                    query_data[0].push($(".question")[i].children[2].children[0].children[0].value.replace(/,/g,'(comma)'));
                    query_data[1].push($(".question")[i].children[2].children[0].children[0].name);
                    query_data[2].push(2);
                }
            //}
            if(answer_flag == 1) {
                if($(".any")[i].attributes["value"].value == 1) {
                    ++required[1];
                }
            } else {
                query_data.pop();
            }
        }
        //console.log(query_data);

        //数値回答方式(スライダーバー)
        //console.log($(".query_data"));
        if($(".question")[i].children[2].children[0].className == "query_slidebar") {
            answer_flag = 0;
            //console.log("test" + i);
            //for( j = 0 ; j < $(".query_data")[i].length ; j++ ) {
                if($(".question")[i].children[2].children[0][0].value != '') {
                    console.log($(".question")[i].children[2].children[0][0]);
                    answer_flag = 1;
                    query_data[0].push($(".question")[i].children[2].children[0][0].value);
                    query_data[1].push($(".question")[i].children[2].children[0][0].name);
                    query_data[2].push(3);
                }
            //}
            if(answer_flag == 1) {
                if($(".any")[i].attributes["value"].value == 1) {
                    ++required[1];
                }
            } else {
                query_data.pop();
            }
        }
        var query_data = query_data.filter(function(element) {
            return (element != '');
        });
        //console.log(query_data);

        if(required[0] == required[1]) {
            document.send_form.elements['sendFlag'].value = true;

            document.send_form.elements['queryData'].value = query_data[0];
            document.send_form.elements['queryId'].value = query_data[1];
            document.send_form.elements['queryType'].value = query_data[2];

            document.send_form.submit();
        } else {
            $("#cantion").show();
        }
    //console.log(required);
    }

    //回答データをセッションに格納
    //if(document.getElementById('input-title').value.length != 0){
    //    document.send_form.elements['title'].value = document.getElementById('input-title').value;
    //} else {
    //    document.getElementsByClassName('submit')[0].classList.add('disabled');
    //    document.getElementById('submit').disabled = true;
    //    document.send_form.elements['sendFlag'].value = false;
    //}
    //console.log(query_data);
});

var sum  = function(arr) {
    return arr.reduce( function (prev, current, i, arr) {
        return prev+current;
    });
};

$(function() {
  var $document   = $(document),
    selector    = '[data-rangeslider]',
    $element    = $(selector);
  function valueOutput(element) {
    var value = element.value,
      output = element.parentNode.getElementsByTagName('output')[0];
      output.innerHTML = value;
  }
  for (var i = $element.length - 1; i >= 0; i--) {
    valueOutput($element[i]);
  };
  $document.on('change', 'input[type="range"]', function(e) {
    valueOutput(e.target);

  });
  $document.on('input', 'input[type="range"]', function(e) {
    valueOutput(e.target);

  });
  $element.rangeslider({
    polyfill: false,
    onInit: function() {},
    onSlide: function(position, value) {
      console.log('onSlide');
      console.log('position: ' + position, 'value: ' + value);
    },
    onSlideEnd: function(position, value) {
      console.log('onSlideEnd');
      console.log('position: ' + position, 'value: ' + value);
    }
  });
});
