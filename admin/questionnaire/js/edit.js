$(function(){

    /*
    $(document).ready(function(){
        formCount++;
        $("#question").append(
        "<div class='each-question' data-number='" + formCount + "'>" +
            "<div class='number-erea'>" +
                "<div class='in'>" +
                    "<p class='number'><span>質問</span><span class='form-number'>" + formCount + "</span></p>" +
                    "<ul class='btns'>" +
                        "<li class='top'><button type='button'></button></li>" +
                        "<li class='bottom'><button type='button'></button></li>" +
                        "<li class='delete form'><button type='button'></button></li>" +
                    "</ul>" +
                        "</div>" +
                    "</div>" +
                    "<div class='info-erea create'>" +
                        "<div class='in'>" +
                            "<dl class='input-group type-questionnaire'>" +
                                "<dt>Format selection</dt>" +
                                "<dd class='clearfix'>" +
                                    "<select class='form-list' name='query_type[" + (formCount-1) + "]'>" +
                                        "<option value='0'>Single selection format</option>" +
                                        "<option value='1'>Multiple selection format</option>" +
                                        "<option value='2' selected='selected'>Free answer form</option>" +
                                        "<option value='3'>Numeric response format</option>" +
                                    "</select>" +
                                    "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + (formCount-1) + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                                    "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + (formCount-1) + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                                "</dd>" +
                            "</dl>" +
                            "<dl class='input-group'>" +
                                "<dt>質問文<span class='text_limit'>Within 500 characters</span></dt>" +
                                "<dd><textarea maxlength='500' rows='4' name='query[" + (formCount-1) + "]'></textarea></dd>" +
                            "</dl>" +
                        "</div>" +
                    "</div>" +
                "</div>" +
            "</div>" +
        "</div>"
        );
    });
    */

    $(document).ready(function(){
        formCount = $('.each-question').length;
        console.log(formCount);
    });

    $('#add-form').on('click', function(){
        formCount++;
        $("#question").append(
        "<div class='each-question' data-number='" + formCount + "'>" +
            "<div class='number-erea'>" +
                "<div class='in'>" +
                    "<p class='number'><span>Question</span><span class='form-number'>" + formCount + "</span></p>" +
                    "<ul class='btns'>" +
                        "<li class='top'><button type='button'></button></li>" +
                        "<li class='bottom'><button type='button'></button></li>" +
                        "<li class='delete form'><button type='button'></button></li>" +
                    "</ul>" +
                "</div>" +
            "</div>" +
            "<div class='info-erea create'>" +
                "<div class='in'>" +
                    "<dl class='input-group type-questionnaire'>" +
                        "<dt>Format selection</dt>" +
                        "<dd class='clearfix'>" +
                            "<select class='form-list' name='query_type[" + (formCount-1) + "]'>" +
                                "<option value='0'>Single selection format</option>" +
                                "<option value='1'>Multiple selection format</option>" +
                                "<option value='2' selected='selected'>Free answer form</option>" +
                                "<option value='3'>Numeric response format</option>" +
                            "</select>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + (formCount-1) + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + (formCount-1) + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                        "</dd>" +
                    "</dl>" +
                    "<dl class='input-group'>" +
                        "<dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>" +
                        "<dd><textarea maxlength='500' rows='4' name='query[" + (formCount-1) + "]'></textarea></dd>" +
                    "</dl>" +
                "</div>" +
            "</div>" +
        "</div>"
        );

        //各データ整形
        $('.each-question').each(function(i){
            $(this).attr('data-number',(i+1));
            $(this).find('.form-number').text(i+1);

            //$_POST[query_type]
            $(this).find('.form-list').attr('name', 'query_type[' + i + ']');

            //$_POST[flg_query_must]
            $(this).find('.checkbox').children('input[type="radio"]').attr('name', 'flg_query_must[' + i + ']');

            //$_POST[query]
            $(this).find('.input-group').children('dd').children('textarea').attr('name', 'query[' + i + ']');

            //単一選択
            if($(this).find(".form-list").val() == 0) {

                //$_POST[text]
                $(this).find('dd.item').each(function(j){
                    $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                    console.log(j);
                });

            //複数選択
            } else if($(this).find(".form-list").val() == 1) {

                //$_POST[text]
                $(this).find('dd.item').each(function(j){
                    $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                    console.log(j);
                });

            //自由選択
            } else if($(this).find(".form-list").val() == 2) {
            //数値選択
            } else if($(this).find(".form-list").val() == 3) {

                $(this).find('[name^=min_label]').attr('name', 'min_label[' + i + ']');
                $(this).find('[name^=max_label]').attr('name', 'max_label[' + i + ']');
                $(this).find('[name^=min_limit]').attr('name', 'min_limit[' + i + ']');
                $(this).find('[name^=max_limit]').attr('name', 'max_limit[' + i + ']');
                $(this).find('[name^=step]').attr('name', 'step[' + i + ']');

            }

            //ソートボタンの表示変更
            if(i == 0 && (formCount-1 == 0)) {
                $(this).find('.top').children('button').removeClass("active");
                $(this).find('.bottom').children('button').removeClass("active");
            } else if(i == 0) {
                $(this).find('.top').children('button').removeClass("active");
                $(this).find('.bottom').children('button').addClass("active");
            } else if(i == (formCount-1)) {
                $(this).find('.top').children('button').addClass("active");
                $(this).find('.bottom').children('button').removeClass("active");
            } else {
                $(this).find('.top').children('button').addClass("active");
                $(this).find('.bottom').children('button').addClass("active");
            }
        });
    });

    $('#question').on('change', '.form-list', function() {
        formChoicesNumber = $(this).parents('.each-question').attr('data-number')-1;

        if($(this).val() == 0) {
            $(this).parents('.info-erea').append(
                "<div class='in'>" +
                    "<dl class='input-group type-questionnaire'>" +
                        "<dt>Format selection</dt>" +
                        "<dd class='clearfix'>" +
                            "<select class='form-list' name='query_type[" + formChoicesNumber + "]'>" +
                                "<option value='0' selected='selected'>Single selection format</option>" +
                                "<option value='1'>Multiple selection format</option>" +
                                "<option value='2'>Free answer form</option>" +
                                "<option value='3'>Numeric response format</option>" +
                            "</select>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                        "</dd>" +
                    "</dl>" +
                    "<dl class='input-group'>" +
                        "<dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>" +
                        "<dd><textarea maxlength='500' rows='4' name='query[" + formChoicesNumber + "]'></textarea></dd>" +
                    "</dl>" +
                    "<dl class='input-group select-answer'>" +
                        "<dt>Selection item<span class='text_limit'>Set two or more</span></dt>" +
                        "<dd class='item'><input type='text' maxlength='100' name='text[" + formChoicesNumber + "][0]'><button class='delete item'>Delete</button></dd>" +
                        "<dd class='item'><input type='text' maxlength='100' name='text[" + formChoicesNumber + "][1]'><button class='delete item'>Delete</button></dd>" +
                        "<dd class='add'><button class='insert item'>Add item</button></dd>" +
                    "</dl>" +
                "</div>"
            );
        } else if($(this).val() == 1) {
            $(this).parents('.info-erea').append(
                "<div class='in'>" +
                    "<dl class='input-group type-questionnaire'>" +
                        "<dt>Format selection</dt>" +
                        "<dd class='clearfix'>" +
                            "<select class='form-list' name='query_type[" + formChoicesNumber + "]'>" +
                                "<option value='0'>Single selection format</option>" +
                                "<option value='1' selected='selected'>Multiple selection format</option>" +
                                "<option value='2'>Free answer form</option>" +
                                "<option value='3'>Numeric response format</option>" +
                            "</select>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                        "</dd>" +
                    "</dl>" +
                    "<dl class='input-group'>" +
                        "<dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>" +
                        "<dd><textarea maxlength='500' rows='4' name='query[" + formChoicesNumber + "]'></textarea></dd>" +
                    "</dl>" +
                    "<dl class='input-group select-answer'>" +
                        "<dt>Selection item<span class='text_limit'>Set two or more</span></dt>" +
                        "<dd class='item'><input type='text' maxlength='100' name='text[" + formChoicesNumber + "][0]'><button class='delete item'>Delete</button></dd>" +
                        "<dd class='item'><input type='text' maxlength='100' name='text[" + formChoicesNumber + "][1]'><button class='delete item'>Delete</button></dd>" +
                        "<dd class='add'><button class='insert item'>Add item</button></dd>" +
                    "</dl>" +
                "</div>"
            );
        } else if($(this).val() == 2) {
            $(this).parents('.info-erea').append(
                "<div class='in'>" +
                    "<dl class='input-group type-questionnaire'>" +
                        "<dt>Format selection</dt>" +
                        "<dd class='clearfix'>" +
                            "<select class='form-list' name='query_type[" + formChoicesNumber + "]'>" +
                                "<option value='0'>Single selection format</option>" +
                                "<option value='1'>Multiple selection format</option>" +
                                "<option value='2' selected='selected'>Free answer form</option>" +
                                "<option value='3'>Numeric response format</option>" +
                            "</select>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                        "</dd>" +
                    "</dl>" +
                    "<dl class='input-group'>" +
                        "<dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>" +
                        "<dd><textarea maxlength='500' rows='4' name='query[" + formChoicesNumber + "]'></textarea></dd>" +
                    "</dl>" +
                "</div>"
            );
        } else if($(this).val() == 3) {
            $(this).parents('.info-erea').append(
                "<div class='in'>" +
                    "<dl class='input-group type-questionnaire'>" +
                        "<dt>Format selection</dt>" +
                        "<dd class='clearfix'>" +
                            "<select class='form-list' name='query_type[" + formChoicesNumber + "]'>" +
                                "<option value='0'>Single selection format</option>" +
                                "<option value='1'>Multiple selection format</option>" +
                                "<option value='2'>Free answer form</option>" +
                                "<option value='3' selected='selected'>Numeric response format</option>" +
                            "</select>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='0' checked><span class='icon'></span>Optional answer</label></p>" +
                            "<p class='any-required'><label class='checkbox'><input type='radio' name='flg_query_must[" + formChoicesNumber + "]' value='1'><span class='icon'></span>Required answer</label></p>" +
                        "</dd>" +
                    "</dl>" +
                    "<dl class='input-group'>" +
                        "<dt>Question sentence<span class='text_limit'>Within 500 characters</span></dt>" +
                        "<dd><textarea maxlength='500' rows='4' name='query[" + formChoicesNumber + "]'></textarea></dd>" +
                    "</dl>" +
                    "<div class='clearfix'>" +
                        "<dl class='input-group value-answer'>" +
                            "<dt>Numeric input</dt>" +
                            "<dd>" +
                                "<input type='text' placeholder='Label of the minimum value' maxlength='20' class='label' name='min_label[" + formChoicesNumber + "]'>" +
                                "<input type='number' name='min_limit[" + formChoicesNumber + "]' value='1'>" +
                                "<p>～</p>" +
                                "<input type='text' placeholder='Label for maximum value' maxlength='20' class='label' name='max_label[" + formChoicesNumber + "]'>" +
                                "<input type='number' name='max_limit[" + formChoicesNumber + "]' value='5'>" +
                            "</dd>" +
                        "</dl>" +
                        "<dl class='input-group step'>" +
                            "<dt>number of steps</dt>" +
                            "<dd>" +
                                "<input type='number' name='step[" + formChoicesNumber + "]' value='1'>" +
                            "</dd>" +
                        "</dl>" +
                    "</div>" +
                "</div>"
            );
        }

        //最後に自要素を含む箇所を削除(先に削除するとthisで取れない)
        $(this).parents('.in').remove();
    });

    //フォーム-削除(ゴミ箱)
    $('#question').on('click', '.delete.form', function() {
        $(this).parents('.each-question').remove();
        formCount--;

        //各データ整形
        $('.each-question').each(function(i){
            $(this).attr('data-number',(i+1));
            $(this).find('.form-number').text(i+1);

            //$_POST[query_type]
            $(this).find('.form-list').attr('name', 'query_type[' + i + ']');

            //$_POST[flg_query_must]
            $(this).find('.checkbox').children('input[type="radio"]').attr('name', 'flg_query_must[' + i + ']');

            //$_POST[query]
            $(this).find('.input-group').children('dd').children('textarea').attr('name', 'query[' + i + ']');

            //単一選択
            if($(this).find(".form-list").val() == 0) {

                //$_POST[text]
                $(this).find('dd.item').each(function(j){
                    $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                    console.log(j);
                });

            //複数選択
            } else if($(this).find(".form-list").val() == 1) {

                //$_POST[text]
                $(this).find('dd.item').each(function(j){
                    $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                    console.log(j);
                });

            //自由選択
            } else if($(this).find(".form-list").val() == 2) {
            //数値選択
            } else if($(this).find(".form-list").val() == 3) {

                $(this).find('[name^=min_label]').attr('name', 'min_label[' + i + ']');
                $(this).find('[name^=max_label]').attr('name', 'max_label[' + i + ']');
                $(this).find('[name^=min_limit]').attr('name', 'min_limit[' + i + ']');
                $(this).find('[name^=max_limit]').attr('name', 'max_limit[' + i + ']');
                $(this).find('[name^=step]').attr('name', 'step[' + i + ']');

            }

            //ソートボタンの表示変更
            if(i == 0 && (formCount-1 == 0)) {
                $(this).find('.top').children('button').removeClass("active");
                $(this).find('.bottom').children('button').removeClass("active");
            } else if(i == 0) {
                $(this).find('.top').children('button').removeClass("active");
                $(this).find('.bottom').children('button').addClass("active");
            } else if(i == (formCount-1)) {
                $(this).find('.top').children('button').addClass("active");
                $(this).find('.bottom').children('button').removeClass("active");
            } else {
                $(this).find('.top').children('button').addClass("active");
                $(this).find('.bottom').children('button').addClass("active");
            }
        });
    });

    //Selection item-削除
    $('#question').on('click', '.delete.item', function() {
        $(this).parents('.item').remove();
    });

    //Selection item-追加
    $('#question').on('click', '.insert.item', function() {
        formChoicesNumber = $(this).parents('.each-question').attr('data-number')-1;
        $(this).parents('.input-group.select-answer').append(
            "<dd class='item'><input type='text' maxlength='100'><button class='delete item'>Delete</button></dd>" +
            "<dd class='add'><button class='insert item'>Add item</button></dd>"
        );

        $(this).parents('.input-group.select-answer').find('input').each(function(i){
            $(this).attr('name', 'text[' + formChoicesNumber + '][' + i + ']');
        });

        $(this).parents('.add').remove();
    });

    //ソートボタン-上
    $('#question').on('click', '.top', function() {
        if($(this).children('.active').length) {
            //必要な数値を取得
            this_number = $(this).parents(".each-question").find(".form-list").val();
            target_number = $(this).parents(".each-question").prev(".each-question").find(".form-list").val();

            $(this).parents(".each-question").find('.checkbox').children('input[type="radio"]').attr('name', 'this_flg_query_must');
            $(this).parents(".each-question").prev(".each-question").find('.checkbox').children('input[type="radio"]').attr('name', 'target_flg_query_must');

            //ソート実行
            $(this).parents(".each-question").prev(".each-question").insertAfter($(this).parents(".each-question"));

            //各データ整形
            $('.each-question').each(function(i){
                $(this).attr('data-number',(i+1));
                $(this).find('.form-number').text(i+1);

                //$_POST[query_type]
                $(this).find('.form-list').attr('name', 'query_type[' + i + ']');

                //$_POST[flg_query_must]
                $(this).find('.checkbox').children('input[type="radio"]').attr('name', 'flg_query_must[' + i + ']');

                //$_POST[query]
                $(this).find('.input-group').children('dd').children('textarea').attr('name', 'query[' + i + ']');

                //単一選択
                if($(this).find(".form-list").val() == 0) {

                    //$_POST[text]
                    $(this).find('dd.item').each(function(j){
                        $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                        console.log(j);
                    });

                //複数選択
                } else if($(this).find(".form-list").val() == 1) {

                    //$_POST[text]
                    $(this).find('dd.item').each(function(j){
                        $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                        console.log(j);
                    });

                //自由選択
                } else if($(this).find(".form-list").val() == 2) {
                //数値選択
                } else if($(this).find(".form-list").val() == 3) {

                    $(this).find('[name^=min_label]').attr('name', 'min_label[' + i + ']');
                    $(this).find('[name^=max_label]').attr('name', 'max_label[' + i + ']');
                    $(this).find('[name^=min_limit]').attr('name', 'min_limit[' + i + ']');
                    $(this).find('[name^=max_limit]').attr('name', 'max_limit[' + i + ']');
                    $(this).find('[name^=step]').attr('name', 'step[' + i + ']');

                }

                //ソートボタンの表示変更
                if(i == 0 && (formCount-1 == 0)) {
                    $(this).find('.top').children('button').removeClass("active");
                    $(this).find('.bottom').children('button').removeClass("active");
                } else if(i == 0) {
                    $(this).find('.top').children('button').removeClass("active");
                    $(this).find('.bottom').children('button').addClass("active");
                } else if(i == (formCount-1)) {
                    $(this).find('.top').children('button').addClass("active");
                    $(this).find('.bottom').children('button').removeClass("active");
                } else {
                    $(this).find('.top').children('button').addClass("active");
                    $(this).find('.bottom').children('button').addClass("active");
                }
            });
        }
    });

    //ソートボタン-下
    $('#question').on('click', '.bottom', function() {
        if($(this).children('.active').length) {
            //必要な数値を取得
            this_number = $(this).parents(".each-question").find(".form-number").text();
            target_number = $(this).parents(".each-question").next(".each-question").find(".form-number").text();

            $(this).parents(".each-question").find('.checkbox').children('input[type="radio"]').attr('name', 'this_flg_query_must');
            $(this).parents(".each-question").next(".each-question").find('.checkbox').children('input[type="radio"]').attr('name', 'target_flg_query_must');

            //ソート実行
            $(this).parents(".each-question").next(".each-question").insertBefore($(this).parents(".each-question"));

            //各データ整形
            $('.each-question').each(function(i){
                $(this).attr('data-number',(i+1));
                $(this).find('.form-number').text(i+1);

                //$_POST[query_type]
                $(this).find('.form-list').attr('name', 'query_type[' + i + ']');

                //$_POST[flg_query_must]
                $(this).find('.checkbox').children('input[type="radio"]').attr('name', 'flg_query_must[' + i + ']');

                //$_POST[query]
                $(this).find('.input-group').children('dd').children('textarea').attr('name', 'query[' + i + ']');

                //単一選択
                if($(this).find(".form-list").val() == 0) {

                    //$_POST[text]
                    $(this).find('dd.item').each(function(j){
                        $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                        console.log(j);
                    });

                //複数選択
                } else if($(this).find(".form-list").val() == 1) {

                    //$_POST[text]
                    $(this).find('dd.item').each(function(j){
                        $(this).children('input[type="text"]').attr('name', 'text[' + i + '][' + j + ']');
                        console.log(j);
                    });

                //自由選択
                } else if($(this).find(".form-list").val() == 2) {
                //数値選択
                } else if($(this).find(".form-list").val() == 3) {

                    $(this).find('[name^=min_label]').attr('name', 'min_label[' + i + ']');
                    $(this).find('[name^=max_label]').attr('name', 'max_label[' + i + ']');
                    $(this).find('[name^=min_limit]').attr('name', 'min_limit[' + i + ']');
                    $(this).find('[name^=max_limit]').attr('name', 'max_limit[' + i + ']');
                    $(this).find('[name^=step]').attr('name', 'step[' + i + ']');

                }

                //ソートボタンの表示変更
                if(i == 0 && (formCount-1 == 0)) {
                    $(this).find('.top').children('button').removeClass("active");
                    $(this).find('.bottom').children('button').removeClass("active");
                } else if(i == 0) {
                    $(this).find('.top').children('button').removeClass("active");
                    $(this).find('.bottom').children('button').addClass("active");
                } else if(i == (formCount-1)) {
                    $(this).find('.top').children('button').addClass("active");
                    $(this).find('.bottom').children('button').removeClass("active");
                } else {
                    $(this).find('.top').children('button').addClass("active");
                    $(this).find('.bottom').children('button').addClass("active");
                }
            });
        }
    });
});
