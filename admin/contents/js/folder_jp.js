$(function() {
    //[削除フォーム]ボタン
    $('.delete_form').on('click', function() {
        $('.delete_form').submit();
    });

    //[作成フォーム]ボタン
    $('.create_form').on('click', function() {
        if($('.input_name').val() != '') {
            $('input[name=create_folder_name]').val($('.input_name').val());
        } else {
            $('input[name=create_folder_name]').val('名称未設定フォルダ');
        }
        $('.create_form').submit();
    });

    //[変更フォーム]ボタン
    $('.edit_form').on('click', function() {
        if($('.input_name').val() != '') {
            $('input[name=edit_folder_name]').val($('.input_name').val());
        } else {
            alert("フォルダ名を入力して下さい");
            return;
        }
        $('.edit_form').submit();
    });

    //[取消フォーム]ボタン
    $('.cancel_form').on('click', function() {
        $('.cancel_form').submit();
    });

    //[各コンテンツ-フォルダ]チェックボックス
    $($('.checkbox.folder').find('.list_check')).on('click', function() {
        if($(this).prop('checked')) {
            //ボタンの表示・非表示
            $('.right').find('.delete_form').show();
            $('.right').find('.create_form').hide();
            $('.right').find('.edit_form').show();
            $('.right').find('.cancel_form').show();

            //リスト内の動画・アンケート・レポート・クイズのチェックボックスを表示
            $('.checkbox.contents').show();
            $('.list_check').prop('checked', false);
            $(this).parents('.contents-folder').find('.list_check').prop('checked', true);
            //選択したフォルダ以外のチェックボックスを非表示
            $('.checkbox.folder').hide();
            $(this).parent('.checkbox.folder').show();

            //[フォルダ名]入力欄に選択フォルダの名称を代入
            $('.text-top').html('<img src="../images/ico_folder_s.png"> 選択中');
            $('.input_name').val($(this).parents('.contents-folder').find('.folder_name').val());

            //各フォームのid欄に主キー値を代入
            $('input[name=delete_folder_id]').val($(this).parents('.contents-folder').find('.folder_id').val());
            $('input[name=edit_folder_id]').val($(this).parents('.contents-folder').find('.folder_id').val());

            //選択したフォルダに、識別用のクラスを追加する
            $(this).parents('.contents-folder').addClass("checked");

            //チェックボックスの番号を格納
            $parent_check_number = $(this).parents('.contents-folder').find('.check_number').val();
        } else {
            //ボタンの表示・非表示
            $('.right').find('.delete_form').hide();
            $('.right').find('.create_form').show();
            $('.right').find('.edit_form').hide();
            $('.right').find('.cancel_form').hide();

            //リスト内の動画・アンケート・レポート・クイズのチェックボックスを非表示
            $('.checkbox.contents').hide();
            //フォルダのチェックボックスは表示
            $('.checkbox.folder').show();

            //[フォルダ名]入力欄の内容を空にする
            $('.text-top').html('<img src="../images/ico_folder_p.png"> 新規作成');
            $('.input_name').val('');

            //追加してあった識別用のクラスを削除する
            $(this).parents('.contents-folder').removeClass("checked");

            //チェックボックスの番号を格納
            $parent_check_number = 0;
        }
    });

    //[各コンテンツ-フォルダ]チェックボックス
    $($('.checkbox.contents').find('.list_check')).on('click', function() {
        if($(this).prop('checked')) {
            //チェックボックスの番号を格納
            $child_check_number = $(this).parents('.item').find('.check_number').val();
            //console.log('child_number : ' + $child_check_number);
            //console.log('prent_number : ' + $parent_check_number);
            //console.log('edit_primary_key : ' + $('input[name="edit_primary_key[' + $child_check_number + ']"]').val());
            //console.log('edit_type : ' + $('input[name="edit_type[' + $child_check_number + ']"]').val());
            //console.log('edit_parent_function_group_id : ' + $('input[name="edit_parent_function_group_id[' + $child_check_number + ']"]').val());

            $change_value = $('input[name="edit_primary_key[' + $parent_check_number + ']"]').val();
            $('input[name="edit_parent_function_group_id_change[' + $child_check_number + ']"]').val($change_value);

            //console.log('edit_parent_function_group_id_change : ' + $('input[name="edit_parent_function_group_id_change[' + $child_check_number + ']"]').val());
       } else {
            $child_check_number = $(this).parents('.item').find('.check_number').val();
            //console.log('child_number : ' + $child_check_number);
            //console.log('prent_number : ' + $parent_check_number);
            //console.log('edit_primary_key : ' + $('input[name="edit_primary_key[' + $child_check_number + ']"]').val());
            //console.log('edit_type : ' + $('input[name="edit_type[' + $child_check_number + ']"]').val());
            //console.log('edit_parent_function_group_id : ' + $('input[name="edit_parent_function_group_id[' + $child_check_number + ']"]').val());

            //$change_value = $('input[name="edit_parent_function_group_id[' + $child_check_number + ']"]').val();
            if($('input[name="edit_parent_function_group_id[' + $child_check_number + ']"]').val() != $('input[name="edit_parent_function_group_id_change[' + $child_check_number + ']"]').val()) {
                $change_value = $('input[name="edit_parent_function_group_id[' + $child_check_number + ']"]').val();
            } else {
                $change_value = 0;
            }
            $('input[name="edit_parent_function_group_id_change[' + $child_check_number + ']"]').val($change_value);

            //console.log('edit_parent_function_group_id_change : ' + $('input[name="edit_parent_function_group_id_change[' + $child_check_number + ']"]').val());
        }
    });
});

