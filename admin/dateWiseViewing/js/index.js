// Loading on page load
var itemsPerPage = 5;
var page = 1;
window.onload = function () 
{
};

$(document).ready(function () {
    bindRowClick();

    // when user clicks on pagination links
    $('#pagination').on("click", ".page a", function (e) {
        e.preventDefault();
        page = $(this).attr("data-page");
        loadHistories(localStorage.getItem('startDate'), localStorage.getItem('endDate'), page)
    });

    $('#daterange').daterangepicker({
        autoApply:false
    }, 
    function(start, end, label) 
    {
        loadHistories(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), page)
        localStorage.setItem('startDate', start.format('YYYY-MM-DD'))
        localStorage.setItem('endDate', end.format('YYYY-MM-DD'))
    });
});


// Drawing graph according to history numbers
function drawGraph(historyId) 
{
    $.ajax({
        url: 'handler.php?action=drawGraph',
        method: 'POST',
        data: {historyId : historyId},
        success: function (data) {
            if(data == 'null')
                return;

            var logs = JSON.parse(data);
            console.dir(logs);

            var chart = AmCharts.makeChart('chartdiv', {
                'type': 'serial',
                'theme': 'light',
                'dataProvider': logs.dataProvier,
                'graphs': logs.graphs,
                'valueAxes': [{
                    maximum: logs.guides[logs.guides.length - 1].value,
                    minimum: 0,
                    strictMinMax: true,
                    labelsEnabled: false,
                    autoGridCount: false,
                    gridCount: 0,
                    guides: logs.guides,
                    duration: 'ss',
                    durationUnits: {
                        'hh': ':',
                        'mm': ':',
                        'ss': ':'
                    },
                }],
                'categoryField': 'X',
                'categoryAxis': [{
                    showLastLabel : true
                  }]
            });
        }
    });
}


// Binding row click for history tables
function bindRowClick() 
{
    $('.tbtlHistories tr').click(function (event) {
        $(".tbtlHistories tr").removeClass("highlight");
        $(this).addClass("highlight");
        drawGraph($(this).find('td:last').text());
    });
}

function fmtMSS(s) 
{
    return (s - (s %= 60)) / 60 + (9 < s ? ':' : ':0') + s
}

// Date range selected
$('.daterange').daterangepicker(
    {
         locale: { format: 'YYYY-MM-DD' }, 
        // startDate: moment().subtract(4, 'week'),
        // endDate: moment()
    }, 
    function(start, end, label) 
    {
        loadHistories(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), page)
        localStorage.setItem('startDate', start.format('YYYY-MM-DD'))
        localStorage.setItem('endDate', end.format('YYYY-MM-DD'))
    }
);

$('#ddlContent').change(function() {
    loadHistories($(this).val());
});

function loadContents(startDate, endDate)
{
    $.ajax({
        url: 'handler.php?action=contents',
        method: 'POST',
        data: { startDate: startDate, endDate : endDate },
        success: function (data) {
            var contents = JSON.parse(data)
            console.log(contents)

            var options = '';
            for (var x = 0; x < contents.length; x++) 
            {
                options += '<option value="' + contents[x]['id'] + '">' + contents[x]['value'] + '</option>';
            }
            $('#ddlContent').html(options);
        }
    });
}

function loadHistories(startDate, endDate, page)
{
    var startingIndex = (page * itemsPerPage) - (itemsPerPage - 1);
    $.ajax({
        url: 'handler.php?action=histories',
        method: 'POST',
        data: { startDate: startDate, endDate : endDate, page : page, itemsPerPage : itemsPerPage },
        success: function (data) 
        {  
            var data = JSON.parse(data)
            var histories = JSON.parse(data.histories)
            console.log(histories);

            var tablerRow = [];
            $.each(histories, function (i, history) {
                tablerRow.push('<tr>');
                tablerRow.push('<td>' + startingIndex++ + '</td>');
                tablerRow.push('<td>' + history.contents_name + '</td>');
                tablerRow.push('<td>' + history.student_name + '</td>');
                tablerRow.push('<td>' + history.play_start_datetime + '</td>');
                tablerRow.push('<td>' + fmtMSS(Math.floor((history.watch_duration / 1000))) + '</td>');
                tablerRow.push('<td>' + fmtMSS(Math.floor((history.duration / 1000))) + '</td>');
                tablerRow.push('<td hidden>' + history.history_id + '</td>');
                tablerRow.push('</tr>');
            });
            $('.tbtlHistories').html(tablerRow.join(''));
            bindRowClick();

            $('#pagination').html(data.pagination);
        }
    });
}



