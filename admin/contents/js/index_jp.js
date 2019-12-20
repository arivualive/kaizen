array_number = '';

$(function() {

    $('.sort-dummy-01').on('click', function () {
        $('.item-sort .link').html("全て");
        $('.item-sort .link').addClass("all-sort");
        $('.item-sort .link').removeClass("movie");
        $('.item-sort .link').removeClass("test");
        $('.item-sort .link').removeClass("questionnaire");
        $('.item-sort .link').removeClass("report");
        $(".list-dummy-01").show();
        $(".list-dummy-02").show();
        $(".list-dummy-03").show();
        $(".list-dummy-04").show();
    });
    $('.sort-dummy-02').on('click', function () {
        $('.item-sort .link').html("動画授業");
        $('.item-sort .link').removeClass("all-sort");
        $('.item-sort .link').addClass("movie");
        $('.item-sort .link').removeClass("test");
        $('.item-sort .link').removeClass("questionnaire");
        $('.item-sort .link').removeClass("report");
        $(".list-dummy-01").show();
        $(".list-dummy-02").hide();
        $(".list-dummy-03").hide();
        $(".list-dummy-04").hide();
    });
    $('.sort-dummy-03').on('click', function () {
        $('.item-sort .link').html("テスト");
        $('.item-sort .link').removeClass("all-sort");
        $('.item-sort .link').removeClass("movie");
        $('.item-sort .link').addClass("test");
        $('.item-sort .link').removeClass("questionnaire");
        $('.item-sort .link').removeClass("report");
        $(".list-dummy-01").hide();
        $(".list-dummy-02").show();
        $(".list-dummy-03").hide();
        $(".list-dummy-04").hide();
    });
    $('.sort-dummy-04').on('click', function () {
        $('.item-sort .link').html("アンケート");
        $('.item-sort .link').removeClass("all-sort");
        $('.item-sort .link').removeClass("movie");
        $('.item-sort .link').removeClass("test");
        $('.item-sort .link').addClass("questionnaire");
        $('.item-sort .link').removeClass("report");
        $(".list-dummy-01").hide();
        $(".list-dummy-02").hide();
        $(".list-dummy-03").show();
        $(".list-dummy-04").hide();
    });
    $('.sort-dummy-05').on('click', function () {
        $('.item-sort .link').html("レポート");
        $('.item-sort .link').removeClass("all-sort");
        $('.item-sort .link').removeClass("movie");
        $('.item-sort .link').removeClass("test");
        $('.item-sort .link').removeClass("questionnaire");
        $('.item-sort .link').addClass("report");
        $(".list-dummy-01").hide();
        $(".list-dummy-02").hide();
        $(".list-dummy-03").hide();
        $(".list-dummy-04").show();
    });

    $('.checkbox').on('click', function () {
        array_number = $(this).children('input[name="array_number"]').val();
        //console.dir(array_number);
        console.dir($('.checkbox').length - 1);
        if(array_number == 0) {
            $('.top').removeClass("active");
            $('.up').removeClass("active");
            $('.down').addClass("active");
            $('.bottom').addClass("active");
        } else if(array_number == $('.checkbox').length - 1) {
            $('.top').addClass("active");
            $('.up').addClass("active");
            $('.down').removeClass("active");
            $('.bottom').removeClass("active");
        } else {
            $('.top').addClass("active");
            $('.up').addClass("active");
            $('.down').addClass("active");
            $('.bottom').addClass("active");
        }
    });

    var player3, playerWrap, divPlayer;
    divPlayer = document.getElementById( 'player' );
    player3 = tbwp3.entry( divPlayer, { isUsingTbLogo : false } );
    //var $script = $( '#play_button' );
    // 再生処理
		$( '.play_button' ).on( 'click', function ( e ) {

      $( '.play_button' ).attr( 'data-toggle', 'modal' );
      $( '.play_button' ).attr( 'data-target', '#contents-continuity' );

      var title = e.target.getAttribute( 'value' );

      var options = { "backdrop" : "static" };
      $( '#contents-continuity' ).modal( options );
			//console.dir(e);
			var contentID, studentID, bid;
      contentID = e.target.getAttribute( 'data-filePath' );
			var $filePath = $( '.play_button'  );
			//contentID = JSON.parse(contentID);
      //player3.getFile( 'https://'+contentID, {
      player3.getFile( contentID, {
        playMode : tbwp3.PLAY_MODE.NORMAL,
        returnCode : function ( code ) {}
      });

      $('.text' ).html( title );

		});

    // 停止処理
    $( '.playerStop' ).on( 'click', function () {
      player3.stop();
    });

});

/*
function sortFunction($mode) {
    if(array_number == ''
       || ($mode == 'top' && array_number == '0')
       || ($mode == 'up' && array_number == '0')
       || ($mode == 'down' && array_number == ($('.checkbox').length - 1))
       || ($mode == 'bottom' && array_number == ($('.checkbox').length - 1))
      ) {
        return false;
    } else {
        $('.item-movement').find('input[name="array_number"]').val(array_number);
        console.log(array_number);
        console.log($('.item-movement').find('input[name="array_number"]').val());
        return true;
    }
}
*/
