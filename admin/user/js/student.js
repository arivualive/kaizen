$(function(){
    var $jsdata = $('#js_value');
    $('.userlist').scrollTop(JSON.parse($jsdata.attr('data-param')));

    $('.listvalue').find('.key, .id, .name, .lecture, .new').on('click', function () {
        $scrool_value = $('.userlist').scrollTop();
        if ($("#canRegister").val() == 1) {
            window.location.href = './student.php?id=' + $(this).siblings('.key').text() + '&sc=' + $scrool_value;
        }
    });

    $('#filter_id').keyup(filter_change);
    $('#filter_name').keyup(filter_change);
    $('#filter_lecture').change(filter_change);
    
    function filter_change() {
        //console.log($('#filter_id').val());
        //console.log($('#filter_name').val());
        //console.log($('#filter_lecture').find('select').val());

        f_id = $('#filter_id').val();
        f_name = $('#filter_name').val();
        f_lecture = $('#filter_lecture').find('select').val();

        $('.id').parent('.listvalue').removeClass('f_id');
        $('.id').parent('.listvalue').removeClass('f_name');
        $('.id').parent('.listvalue').removeClass('f_lecture');

        if(f_id != '') {
            $('.id').parent('.listvalue').addClass('f_id');
            $('.id[filter*=' + f_id + ']').parent('.listvalue').removeClass('f_id');
        }

        if(f_name != '') {
            $('.name').parent('.listvalue').addClass('f_name');
            $('.name[filter*=' + f_name + ']').parent('.listvalue').removeClass('f_name');
        }

        if(f_lecture != '') {
            $('.lecture').parent('.listvalue').addClass('f_lecture');
            $('.lecture[filter*=' + f_lecture + ']').parent('.listvalue').removeClass('f_lecture');
        }

    }

    $('#auto_student_id').on('click', function () {
        var i, string, lenghth;
        min = 8;
        max = 16;

        lenghth = Math.floor( Math.random() * (max + 1 - min) ) + min;

        string = "";
        for( i = 0; i < lenghth; ++i ){
            string += "abcdefghijklmnopqrstuvwxyz0123456789".substr( Math.floor( Math.random() * 36 ), 1 );
        }

        $('input[name="id"]').val(string);
    });

    $('#auto_student_password').on('click', function () {
        var i, string, lenghth;
        min = 8;
        max = 16;

        lenghth = Math.floor( Math.random() * (max + 1 - min) ) + min;

        string = "";
        for( i = 0; i < lenghth; ++i ){
            string += "abcdefghijklmnopqrstuvwxyz0123456789".substr( Math.floor( Math.random() * 36 ), 1 );
        }

        $('input[name="password"]').val(string);
    });

    $('#auto_student_password2').on('click', function () {
        var i, string, lenghth;
        min = 8;
        max = 16;

        lenghth = Math.floor( Math.random() * (max + 1 - min) ) + min;

        string = "";
        for( i = 0; i < lenghth; ++i ){
            string += "abcdefghijklmnopqrstuvwxyz0123456789".substr( Math.floor( Math.random() * 36 ), 1 );
        }

        $('input[type="password"][name="password"]').attr('name', 'hide_password');
        $('input[type="text"][name="hide_password"]').attr('name', 'password');
        $('input[type="text"][name="password"]').val(string);
    });

    $('input[type="password"][name="password"]').on('focus', function () {
        $('input[type="password"][name="password"]').attr('name', 'hide_password');
        $('input[type="text"][name="hide_password"]').attr('name', 'password');
        $('input[type="text"][name="password"]').focus();
    });

    $('input[type="text"][name="hide_password"]').on('blur', function () {
        if($('input[type="text"][name="password"]').val() == '') {
            $('input[type="text"][name="password"]').attr('name', 'hide_password');
            $('input[type="password"][name="hide_password"]').attr('name', 'password');
            console.log('not change!!');
        } else {
            console.log('change!!');
        }
    });
});
