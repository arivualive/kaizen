// Loading on page load
window.onload = function () {
    // Loading content numbers
    var $script = $( '#script' );
    var session = JSON.parse($script.attr('data-session'));

    // backgroud でログ再取得

    if ( window.Worker ) {
      var worker = new Worker( 'js/viewingWorker.js' );
      sendData( worker, session );
    } else {
      console.dir( 'not_web_worker' );
    }

    worker.onmessage = function ( event ) {
      //console.dir( event.data );
    }

    $.ajax({
        url:"../viewing/handler.php?action=histories",//"handler.php?action=histories",
        method: 'POST',
        data: {
          student_id: session.student_id,
          contents_number: session.contents_id
        },
        success: function (data) {
          //console.dir( data );
            var histories = JSON.parse(data)
            //console.dir( histories );
            var hCount = histories.length;
            //console.dir( hCount );
            var duration;
            if ( hCount > 0 ) {
              for ( var h = 0; h < hCount; h++ ) {
                if ( histories[ h ][ 'duration' ] != 0 ) {
                    duration = milliSec2Time( histories[ h ][ 'duration' ] );
                } else {
                    duration = '0:00:00';
                }
              }
            }
            //var duration = milliSec2Time( histories[ 0 ][ 'duration' ] );

            $( '#duration' ).text( duration );

            var tablerRow = [];
            $.each(histories, function (i, history) {
                tablerRow.push("<tr>");
                tablerRow.push("<td>" + history.history_id + "</td>");
                tablerRow.push("<td disabled><input type='checkbox' class='line'/> </td>");
                tablerRow.push("<td>" + history.play_start_datetime + "</td>");
                //tablerRow.push("<td>" + fmtMSS(Math.floor((history.watch_duration / 1000))) + "</td>");
                tablerRow.push("<td>" + milliSec2Time( history.max_progress_time ) + "</td>");
                tablerRow.push("<td hidden>" + history.history_id + "</td>");
                tablerRow.push("</tr>");
            });
            $('.record_table').html(tablerRow.join(""));
            bindRowClick();

        }
    });
};

$(document).ready(function () {
    bindRowClick();
});

// backgroud でログ再取得
function sendData ( worker, data ) {
  worker.postMessage({
    student_id : data.student_id,
    contents_number : data.contents_id
  });
};

// Drawing graph according to history numbers
function drawGraph(histories) {

  var logs,chart;

  if ( Object.keys( histories ).length == 0 ) {
    chart = "";
  } else {
    $.ajax({
        url: "handler.php?action=contents",
        method: 'POST',
        data: { histories: histories },
        success: function (data) {
            logs = JSON.parse(data);

            chart = AmCharts.makeChart("chartdiv", {
                "type": "serial",
                "theme": "light",
                "dataProvider": logs.dataProvier,
                "graphs": logs.graphs,
                "valueAxes": [{
                    maximum: logs.guides[logs.guides.length - 1].value,
                    minimum: 0,
                    strictMinMax: true,
                    labelsEnabled: false,
                    autoGridCount: false,
                    gridCount: 0,
                    guides: logs.guides,
                    duration: "ss",
                    durationUnits: {
                        "hh": ":",
                        "mm": ":",
                        "ss": ":"
                    },
                }],
                "chartCursor": {
                     "valueLineEnabled": true,
                     "valueLineBalloonEnabled": true
                 },
                "categoryField": "X",
                "categoryAxis": [{
                    showLastLabel : true
                  }]
            });

        }
    });
  }


}


// Binding row click for history tables
function bindRowClick() {
    $('.record_table tr').click(function (event) {

        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');

            var histories = $(".record_table tr:has(input:checked)").map(function () {
                var $tr = $(this);
                var id = $tr.find("td:last").text();
                return id;
            }).toArray();

            //console.log(histories.join(", "));
            var $tr = $(this);
            var id = $tr.find("td:last").text();
            drawGraph(histories);

        } else if ( event.target.type == 'checkbox' ) {

          var histories = $(".record_table tr:has(input:checked)").map(function () {
              var $tr = $(this);
              var id = $tr.find("td:last").text();
              return id;
          }).toArray();

          //console.log(histories.join(", "));
          var $tr = $(this);
          var id = $tr.find("td:last").text();
          drawGraph(histories);
        }
        //alert(ids.join(", "));
    });
}

function fmtMSS(s)
{
    return (s - (s %= 60)) / 60 + (9 < s ? ':' : ':0') + s
}

function milliSec2Time ( ms )
{
  var hour,minute,sec,milliSec;

  milliSec = ms % 1000;
  ms = ( ms - milliSec ) / 1000;
  sec = ms % 60;
  ms = ( ms - sec ) / 60;
  minute = ms % 60;
  hour = ( ms - minute ) / 60;

  return hour + ':' + (( minute < 10 )? '0' : '' ) + minute + ':' + (( sec < 10 )?'0':'') + sec;
}
