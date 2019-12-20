$(function() {
    // [contents_name] フォーム入力時の処理
    $('textarea[name=contents_name]').change(function() {
        if($('textarea[name=contents_name]').val() != ''){
            $('#not_title').hide();
        } else {
            $('#not_title').show();
        }
    });

    // [contents_file] アップロードファイル選択操作時の処理
    $('input[name=contents_file]').change(function() {
        var file = $(this).prop('files')[0];
        if(file.size < (150 * 1024 * 1024)) {
            $('#contents_file_info').html('ファイル名: ' + file.name + ' / サイズ: ' + getFiseSize(file.size) + ' / 種類: ' + file.name.split('.')[file.name.split('.').length - 1]);
            $('#contents_file_info').css('color', '');
            $('#contents_not_file').remove();
            console.log(file.name.split('.')[file.name.split('.').length - 1]);
        } else {
            $('#contents_file_info').html('ファイルサイズが150MBを超えています: (対象ファイルサイズ)' + getFiseSize(file.size));
            $('#contents_file_info').css('color', '#FF0000');
            $('#contents_not_file').remove();
        }
    });

    // [attach_file] アップロードファイル選択操作時の処理
    $('input[name=attach_file]').change(function() {
        var file = $(this).prop('files')[0];
        if(file.size < (150 * 1024 * 1024)) {
            $('#attach_file_info').html('ファイル名:' + file.name + ' / サイズ:' + getFiseSize(file.size) + ' / 種類:' + file.type);
            $('#attach_file_info').css('color', '');
        } else {
            $('#attach_file_info').html('ファイル名:' + file.name + ' / サイズ:' + getFiseSize(file.size) + ' / 種類:' + file.type);
            $('#attach_file_info').css('color', '#FF0000');
        }
    });

    // [attach_file] アップロードファイル選択操作時の処理
    $(document).on('click', '.cancel', function() {
        $('#attach_file_function').find('input[name=attach_file]').val("");
        $('#attach_file_function').find('#attach_file_info').html("");
    });
});
  
 /** 
 * ファイルサイズの単位
 * @param {int} file_size 
 * @return {string}
 */
function getFiseSize(file_size)
{
  var str;
  
  // 単位
  var unit = ['B', 'KB', 'MB', 'GB', 'TB'];

  for (var i = 0; i < unit.length; i++) {  
    if (file_size < 1024 || i == unit.length - 1) {
      if (i == 0) {
        // カンマ付与
        var integer = file_size.toString().replace(/([0-9]{1,3})(?=(?:[0-9]{3})+$)/g, '$1,');
        str = integer +  unit[ i ];
      } else {
        // 小数点第2位は切り捨て
        file_size = Math.floor(file_size * 100) / 100;
        // 整数と小数に分割
        var num = file_size.toString().split('.');
        // カンマ付与
        var integer = num[0].replace(/([0-9]{1,3})(?=(?:[0-9]{3})+$)/g, '$1,');
        if (num[1]) {
          file_size = integer + '.' + num[1];
        }
        str = file_size +  unit[ i ];
      }
      break;
    }
    file_size = file_size / 1024;
  }

  return str;
}