$(function() {
	//カテゴリー階層の追加制限
	var limit = $('.mode').data('limit') - 1;
	if(limit < 0) { limit = 3; }

	//各カテゴリー最大値の取得
	var max = '1-0x0';
	$(':checkbox[name="category[]"]').each(function() {
		//現在の「value」値と配列「max」を分割
		var value = $(this).val().split('-');
		var temp = max.split('-');
		//value値と配列「max」を比較
		if(parseInt(value[0]) >= parseInt(temp[0]) && parseInt(value[1]) > parseInt(temp[1])) { max = $(this).val(); }
		if(parseInt(value[0]) > parseInt(temp[0])) { max = $(this).val(); }
	});
	//console.log( max );

	//追加ボタンの初期化処理
	$('ul.padding').each(function() {
		var num = 0; var parent = '';
		var obj = $(this).prevAll('.item2').children('input[name="save[]"]');

		if($(obj).val()) {
			var temp = $(obj).val().split('{}');
			//分割した「value」値からカテゴリー階層を取得
			num = parseInt(temp[0]);
			//分割した「value」値を「data-parent」属性に設定
			parent = temp[1];
		}
		//カテゴリー追加ボタンタグ挿入（第3カテゴリーにはタグを挿入しない）
		if(num != limit) { $(this).append('<label class="mark add2" data-category="' + (num + 1) + '" data-value="' + max + '" data-parent="' + parent + '">&#xf0fe;</label>'); }
		//項目追加ボタンタグ挿入
		$(this).append('<label class="mark add1" data-category="' + (num + 1) + '" data-value="' + max + '" data-parent="' + parent + '">&#xf055;</label>');
		//<ul>タグ内の「項目」と「項目追加ボタン」を<div>タグで囲む
		$(this).children('.item1, .add1').wrapAll('<div class="padding2">');
		//<div>タグを<ul>タグ内の最後に移動
		$(this).children('div').appendTo($(this))
	});

	//チェックボックスとラベルの関連付け
	LabelConnect();

	//階層ボタン処理
	$(document).on('change', ':checkbox[name="folder"]', function() {
		//階層の折りたたみ処理
		$(this).parent('.item2').next('ul').animate({height:'toggle'}, 'fast', 'swing');
		//フォルダスタイルの変更
		if($(this).prop('checked')) {
			$(this).nextAll('.label').addClass('shadow'); //閉じた場合
		} else {
			$(this).nextAll('.label').removeClass('shadow'); //開いた場合
		}
	});

	//チェックボックス連携
	$(document).on('change', ':checkbox[name="category[]"]', function() { //「name」値 'category' が変更されるたびに発動
		//処理スピード測定
		//var startTime = new Date();

		var temp = $(this).prev('input[name="save[]"]').val().split('{}');
		var num = temp[0] - 1; //分割した「value」値からカテゴリー階層(-1)を取得
		var obj = $(this);

		if($(this).prop('checked')) { //変更した要素の「value」値と 'data-'+「name」値が一致する要素に適用
			//子チェックボックス連携
			if(num <= 1) {
				$(':checkbox[data-category' + temp[0] + '=' + obj.val() + ']').prop('checked', true).change();
			} else {
				$(':checkbox[data-category' + temp[0] + '=' + obj.val() + ']').prop('checked', true);
			}
			//親チェックボックス連携
			if(num >= 1) {
				for(var i = num; i > 0; i--) {
					obj = $(':checkbox[value="' + $(obj).data('category' + i) + '"]');
					$(obj).prop('checked', true);
				}
			}
		} else {
			//子チェックボックス連携
			if(num <= 1) {
				$(':checkbox[data-category' + temp[0] + '=' + $(this).val() + ']').prop('checked', false).change();
			} else {
				$(':checkbox[data-category' + temp[0] + '=' + $(this).val() + ']').prop('checked', false);
			}
			//親チェックボックス連携
			if(num >= 1) {
				for(var i = num; i > 0; i--) {
					//同じカテゴリーのチェックボックスが全てオフの場合に発動
					if(!$(':checkbox[data-category' + i + '=' + $(obj).data('category' + i) + ']:checked').length) {
						obj = $(':checkbox[value="' + $(obj).data('category' + i) + '"]');
						$(obj).prop('checked', false);
					}
				}
			}
		}

		//var endTime = new Date();
		//console.log( endTime - startTime + 'ms' );
	});

	//選択・編集モードの切り替え
	$('#toggle').on('change', function() {
		if($('#toggle').prop('checked')) {
			$('#text').html('<i class="fas fa-pencil-alt fa-fw"></i>編集モード').css('color', '#b7472a');
			//チェックボックスとラベルの関連付け解除（カテゴリー項目）
			$(':checkbox[name="category[]"]').each(function(i) {
				$(this).removeAttr('id');
				$('.label').eq(i).removeAttr('for');
			});
			//階層の折りたたみ解除
			$(':checkbox[name="folder"]').prop('checked', false);
			$('ul.padding').css('display', '');
			$('.item2').children('.label').removeClass('shadow');
			//項目の編集可能・レイアウトの変更
			EditableOn();
		} else {
			//編集と選択＆編集で表示を変更
			if($('#toggle').data('mode') == 2) {
				$('#text').html('<i class="fas fa-tablet-alt fa-fw"></i>閲覧モード').css('color', '');
			} else {
				$('#text').html('<i class="far fa-hand-point-up fa-fw"></i>選択モード').css('color', '');
			}
			//チェックボックスとラベルの関連付け（カテゴリー項目）
			LabelConnect();
			//階層の表示
			$('.angle').css('display', '');

			//先頭・後尾移動ボタンの初期化
			ArrowReset();
			//追加・削除・移動ボタンの非表示
			$('.add1, .add2, .del1, .del2, .up1, .up2, .down1, .down2').css('display', '');

			//項目の編集不可
			$('.label').removeAttr('contenteditable');
			$('.label').removeClass('select');
			//レイアウトの変更
			if(window.matchMedia('(min-width:736px)').matches) {
				$('.mark, .label').css('margin', '');
			} else {
				$('.mark, .label').css('margin', '');
				$('.mark').css({'line-height':'', 'height':''});
				$('.label').css('font-size', '');
			}
			$('.padding2').css('padding-left', '');
		}
	});

	//編集モードのフォーカス処理（項目の編集）
	$(document).on('focus', '.label', function() {
		//先頭・後尾移動ボタンの初期化
		ArrowReset();
		//削除ボタンへ置換
		$(this).prevAll('.up1').text('\uf056').addClass('del1').removeClass('up1');
		$(this).prevAll('.up2').text('\uf146').addClass('del2').removeClass('up2');
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();
		//ダミーへ置換
		$(this).next('.down1').text('\u00a0').addClass('down1d').removeClass('down1');
		$(this).next('.down2').text('\u00a0').addClass('down2d').removeClass('down2');
		//文字スタイルの変更
		$(this).css({'color':'#333', 'font-weight':'bold'});
	}).on('blur', '.label', function(){
		//項目名の改行と禁止文字を削除
		$(this).text($(this).text().replace(/\n/g, '').replace(/{/g, '').replace(/}/g, ''));
		//項目名が空白の場合、文字列を挿入
		if(!$(this).text()) {
			var num = $(this).attr('class').replace(/ /g, '').replace(/label/g, '').replace('view2', '').replace('select', '');
			if(num > limit) {
				$(this).text('名称未設定');
			} else {
				$(this).text('第' + num + 'カテゴリー');
			}
		}
		//隠しタグの「value」値を取得・分割
		var obj = $(this).prevAll('input[name="save[]"]');
		var value = $(obj).val().split('{}');
		//隠しタグの「value」値を再設定
		$(obj).val(value[0] + '{}' + value[1] + '{}' + value[2] + '{}' + $(this).text() + '{}' + value[4]);
		//処理を遅延する
		$(this).delay(150).queue(function(){
			//移動ボタンへ置換
			$(this).prevAll('.del1').text('\uf0d9\u00a0\u00a0').addClass('up1').removeClass('del1');
			$(this).prevAll('.del2').text('\uf0d8\u00a0\u00a0').addClass('up2').removeClass('del2');
			//先頭・後尾移動ボタンの非表示
			ArrowCheck();
			//キューを空にする
			$(this).stop();
		});
		//移動ボタンへ置換
		$(this).next('.down1d').text('\u00a0\u00a0\uf0da').addClass('down1').removeClass('down1d');
		$(this).next('.down2d').text('\u00a0\u00a0\uf0d7').addClass('down2').removeClass('down2d');
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();
		//文字スタイルの変更
		$(this).css({'color':'', 'font-weight':''});
	});

	//項目の追加
	$(document).on('click', '.add1, .add2', function() {
		//追加ボタンの「data-value」値を取得・分割
		var value = $(this).data('value').split('-');
		//value値が31bit(0x40000000)を超えないように修正
		if(value[1] == '0x40000000') {
			var newvalue = (parseInt(value[0]) + 1) + '-0x1';
		} else {
			if(value[1] == '0x0') {
				var newvalue = value[0] + '-0x1';
			} else {
				var newvalue = value[0] + '-0x' + (value[1] * 2).toString(16);
			}
		}
		//カテゴリー追加ボタンの「data-value」値を変更
		$('.add1, .add2').data('value', newvalue);
		//第1カテゴリーには 'data-category' 要素を挿入しない
		var num = $(this).data('category');
		var data = '';
		if(num != 1) { data = ' data-category' + (num - 1) + '="' + $(this).data('parent') + '"'; }
		//編集は ' disabled' を追加
		var tag1 = tag2 = '';
		if($('#toggle').data('mode') == 2) { tag1 = ' disabled'; tag2 = ' view2'; }

		//先頭・後尾移動ボタンの初期化
		ArrowReset();
		//項目タグの追加
		if($(this).hasClass('add1')) {
			//追加ボタンの前にタグを挿入（子カテゴリーなし）
			$(this).before(
				'<span class="item1">' +
				'<label class="mark up1">&#xf0d9&nbsp;&nbsp;</label>' +
				'<input type="hidden" name="save[]" value="' + num + '{}' + newvalue + '{}' + $(this).data('parent') + '{}名称未設定{}">' +
				'<input type="checkbox" name="category[]" value="' + newvalue + '"' + data + tag1 + '>' +
				'<label class="label' + tag2 + ' label4">名称未設定</label>' +
				'<label class="mark down1">&nbsp;&nbsp;&#xf0da</label></span>'
			);
		} else {
			//第3カテゴリーにはカテゴリー追加ボタンタグを挿入しない
			var add2 = '';
			if(num != limit) { add2 = '<label class="mark add2" data-category="' + (num + 1) + '" data-value="' + newvalue + '" data-parent="' + newvalue + '">&#xf0fe;</label>'; }
			//追加ボタンの前にタグを挿入（子カテゴリーあり）
			$(this).before(
				'<span class="item2">' +
				'<input type="checkbox" name="folder"><label class="mark angle">&#xf078;</label>' +
				'<label class="mark up2">&#xf0d8&nbsp;&nbsp;</label>' +
				'<input type="hidden" name="save[]" value="' + num + '{}' + newvalue + '{}' + $(this).data('parent') + '{}第' + num + 'カテゴリー{}">' +
				'<input type="checkbox" name="category[]" value="' + newvalue + '"' + data + tag1 + '>' +
				'<label class="label' + tag2 + ' label' + num + '">第' + num + 'カテゴリー</label>' +
				'<label class="mark down2">&nbsp;&nbsp;&#xf0d7</label></span>' + '\n' +
				'<ul class="padding">' + '\n' +
				add2 +
				'<div class="padding2">' +
				'<label class="mark add1" data-category="' + (num + 1) + '" data-value="' + newvalue + '" data-parent="' + newvalue + '">&#xf055;</label>' +
				'</div>' +
				'</ul>' + '\n'
			);
		}
		//スタイルを再セット
		EditableOn();
		//console.log( newvalue );
	});

	//項目の削除
	$(document).on('click', '.del1, .del2', function() {
		//削除する項目名を取得
		var name =$(this).nextAll('.label').text();

		if($(this).hasClass('del1')) {
			//子カテゴリーなし
			if(confirm('「' + name + '」を削除しますか？')) { $(this).parent('.item1').remove(); }
		} else {
			//子カテゴリーあり
			if(confirm('「' + name + '」を削除しますか？ 階層下の項目も削除されます')) {
				$(this).parent('.item2').next('ul').remove();
				$(this).parent('.item2').remove();
			}
		}
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();

		//チェックボックス連携
		var temp = $(this).next('input[name="save[]"]').val().split('{}');
		var num = temp[0] - 1; //分割した「value」値からカテゴリー階層(-1)を取得
		var obj = $(this).nextAll(':checkbox[name="category[]"]');

		if(num >= 1) {
			for(var i = num; i > 0; i--) {
				//同じカテゴリーのチェックボックスが全てオフの場合に発動
				if(!$(':checkbox[data-category' + i + '=' + $(obj).data('category' + i) + ']:checked').length) {
					obj = $(':checkbox[value="' + $(obj).data('category' + i) + '"]');
					$(obj).prop('checked', false);
				}
			}
		}
	});

/*
	//項目の編集
	$('#submit').on('click', function() {
		$('input[name="save[]"]').each(function() {
			//隠しタグの「value」値を取得・分割
			var value = $(this).val().split('{}');
			//隠しタグの「value」値を再設定
			$(this).val(value[0] + '{}' + value[1] + '{}' + value[2] + '{}' + $(this).nextAll('.label').text());
		});
	});
	$(document).on('keyup', '.label', function() {
		//隠しタグの「value」値を取得・分割
		var obj = $(this).prevAll('input[type="hidden"]');
		var value = $(obj).val().split('{}');
		//隠しタグの「value」値を再設定
		$(obj).val(value[0] + '{}' + value[1] + '{}' + value[2] + '{}' + $(this).text());
	});
*/

	//項目の上位移動
	$(document).on('click', '.up1, .up2', function() {
		//先頭・後尾移動ボタンの初期化
		ArrowReset();
		if($(this).hasClass('up1')) {
			//子カテゴリーなし
			//$(this).parent('.item1').prependTo($(this).closest('div'));
			$(this).parent('.item1').insertBefore($(this).parent('.item1').prev('.item1'));
		} else {
			//子カテゴリーあり
			$(this).parent('.item2').next('ul').insertBefore($(this).parent('.item2').prev('ul').prev('.item2'));
			$(this).parent('.item2').insertBefore($(this).parent('.item2').prev('ul').prev('.item2').prev('ul'));
		}
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();
	});

	//項目の下位移動
	$(document).on('click', '.down1, .down2', function() {
		//先頭・後尾移動ボタンの初期化
		ArrowReset();
		if($(this).hasClass('down1')) {
			//子カテゴリーなし
			//$(this).parent('.item1').appendTo($(this).closest('div'));
			$(this).parent('.item1').insertAfter($(this).parent('.item1').next('.item1'));
		} else {
			//子カテゴリーあり
			$(this).parent('.item2').next('ul').insertAfter($(this).parent('.item2').next('ul').next('.item2').next('ul'));
			$(this).parent('.item2').insertAfter($(this).parent('.item2').next('.item2').next('ul'));
		}
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();
	});

	//ページ移動時に警告を表示（ダウンロードリンクは警告を解除）
	$('input[type=checkbox]').change(function() {
		$(window).on('beforeunload', function() {
			return '行った変更は保存されません。';
		});
    });
	$(document).on('click', '.alert', function() {
		$(window).off('beforeunload');
	});

	//送信時に選択モードに切り替え
	$('form').submit(function() {
		//$('#toggle').prop('checked', false);
		//ページ移動時の警告を解除
		$(window).off('beforeunload');
	});

	//ブラウザバックを無効
	if(window.history && window.history.pushState) { //History API が使えるかチェック
		//ダミー履歴を追加
		history.pushState('dummy', null, '');
		$(window).on('popstate', function(e) {
			//「戻る」を実行
			if(!e.originalEvent.state) {
				//ダミー履歴を追加して終了
				history.pushState('dummy', null, '');
				return;
			}
		});
	}

	//チェックボックスとラベル関連付けの動作
	function LabelConnect() {
		//カテゴリー項目
		$(':checkbox[name="category[]"]').each(function(i) {
			$(this).attr('id', i);
			$('.label').eq(i).attr('for', i);
		});
		//フォルダ項目
		$(':checkbox[name="folder"]').each(function(i) {
			$(this).attr('id', 'm' + i);
			$('.angle').eq(i).attr('for', 'm' + i);
		});
	}

	//項目の編集可能・レイアウト変更の動作
	function EditableOn() {
		//階層の非表示
		$('.angle').css('display', 'none');

		//先頭・後尾移動ボタンの初期化
		ArrowReset();
		//追加・削除ボタンの表示
		$('.add1, .add2, .up1, .up2, .down1, .down2').css('display', 'inline-block');
		//先頭・後尾移動ボタンの非表示
		ArrowCheck();

		//項目の編集可能
		$('.label').attr('contenteditable', true);
		$('.label').addClass('select');
		//レイアウトの変更
		if(window.matchMedia('(min-width:736px)').matches) {
			$('.mark, .label').css('margin', '3px 0px');
		} else {
			$('.mark, .label').css('margin', '1px 0px');
			$('.mark').css({'line-height':'36px', 'height':'36px'});
			$('.label').css('font-size', '16px');
		}
		$('.padding2').css('padding-left', '0px');
	}

	//先頭・後尾移動ボタン非表示の動作
	function ArrowCheck() {
		//先頭ボタンをダミーへ置換
		$('ul .item1:first-of-type .up1').text('\u00a0').addClass('up1d').removeClass('up1');
		$('ul .item2:first-of-type .up2').text('\u00a0').addClass('up2d').removeClass('up2');
		//後尾ボタンをダミーへ置換
		$('ul .item1:last-of-type .down1').text('\u00a0').addClass('down1d').removeClass('down1');
		$('ul .item2:last-of-type .down2').text('\u00a0').addClass('down2d').removeClass('down2');

		//console.log( $('ul .item1:nth-of-type(2) .up1') );
		//console.log( $('ul .item1:nth-last-of-type(2) .down1') );
	}

	//先頭・後尾移動ボタン初期化の動作
	function ArrowReset() {
		//先頭ボタンを移動ボタンへ置換
		$('ul .item1:first-of-type .up1d').text('\uf0d9\u00a0\u00a0').addClass('up1').removeClass('up1d');
		$('ul .item2:first-of-type .up2d').text('\uf0d8\u00a0\u00a0').addClass('up2').removeClass('up2d');
		//後尾ボタンを移動ボタンへ置換
		$('ul .item1:last-of-type .down1d').text('\u00a0\u00a0\uf0da').addClass('down1').removeClass('down1d');
		$('ul .item2:last-of-type .down2d').text('\u00a0\u00a0\uf0d7').addClass('down2').removeClass('down2d');
	}
});
