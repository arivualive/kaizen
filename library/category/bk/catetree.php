<?php
//CSVファイルを処理（CSVファイル名を指定し、複数の値をリストに渡す）
list($csvTree,,, $csvRowD, $csvRowE) = load_csv($_POST['csvfile'], 0);

for($current = count($csvTree); $current >= 1; $current --) {
	foreach($csvTree[$current] as $key => $value) {
		${'category' . $current}[$key] .= '<ul class="padding';
		if($current == 1) { ${'category' . $current}[$key] .= ' padding1'; }
		${'category' . $current}[$key] .= '">' . "\n";

		foreach($value as $line) {
			//フォルダと項目を判別（真はフォルダ、否は項目）
			if($csvTree[$current + 1][$line]) {
				${'category' . $current}[$key] .= '<span class="item2"><input type="checkbox" name="folder"><label class="mark';
				if(empty($_POST['mode'])) { ${'category' . $current}[$key] .= ' view3'; } //閲覧モード
				${'category' . $current}[$key] .= ' angle">&#xf078;</label><label class="mark up2">&#xf0d8&nbsp;&nbsp;</label>';
			} else {
				${'category' . $current}[$key] .= '<span class="item1"><label class="mark up1">&#xf0d9&nbsp;&nbsp;</label>';
			}
			//隠しタグを挿入
			${'category' . $current}[$key] .= '<input type="hidden" name="save[]" value="' . $current . '{}' . $line . '{}' . $key . '{}' . $csvRowD[$line] . '{}' . $csvRowE[$line] . '"><input type="checkbox" name="category[]" value="' . $line . '"';
			if($current != 1) { ${'category' . $current}[$key] .= ' data-category' . ($current - 1) . '="' . $key . '"'; }

			//（-）で value値を分割し一時保存の配列に格納
			$temp = explode('-', $line);
			//配列の底を2とする対数のキーが空でなければ checked を追加
			if($check[$temp[0]][log(hexdec($temp[1]), 2)]) { ${'category' . $current}[$key] .= ' checked'; }

			if(empty($_POST['mode']) || $_POST['mode'] == 2) { ${'category' . $current}[$key] .= ' disabled'; } //閲覧モード
			${'category' . $current}[$key] .= '>';
			//ラベルを挿入
			$ua = $_SERVER['HTTP_USER_AGENT']; //ブラウザ判定
			${'category' . $current}[$key] .= ((strpos($ua,'MSIE')) || (strpos($ua,'Edge')) || (strpos($ua,'Firefox'))) ? '<label style="font-weight:normal" class="label' : '<label class="label';
			if(empty($_POST['mode'])) { ${'category' . $current}[$key] .= ' view1'; } //閲覧モード
			if($_POST['mode'] == 2) { ${'category' . $current}[$key] .= ' view2'; } //編集モード（編集のみ）
			if($csvTree[$current + 1][$line]) {
				${'category' . $current}[$key] .= ' label' . $current . '">' . $csvRowD[$line] . '</label><label class="mark down2">&nbsp;&nbsp;&#xf0d7</label></span>' . "\n";
			} else {
				${'category' . $current}[$key] .= ' label4">' . $csvRowD[$line] . '</label><label class="mark down1">&nbsp;&nbsp;&#xf0da</label></span>' . "\n";
			}
			//子項目を挿入
			if($csvTree[$current + 1][$line]) {
				${'category' . $current}[$key] .= ${'category' . ($current + 1)}[$line];
			}
		}
		${'category' . $current}[$key] .= '</ul>' . "\n";
	}
}
//配列が空の場合は初期化
if(!$category1['']) { $category1[''] = '<ul class="padding padding1">' . "\n" . '</ul>' . "\n"; }
?>
<?php if(!$_POST['noform']) echo '<form action="' . $_SERVER['REQUEST_URI'] . '" method="POST">' . "\n"; ?>
<input type="hidden" name="bid" value="<?php echo $_GET['bid'] ?>">
<input type="hidden" name="flag" value="1">
<input type="hidden" name="csvfile" value="<?php echo $_POST['csvfile'] ?>">
<input type="hidden" name="mode" value="<?php echo $_POST['mode'] ?>">
<input type="hidden" name="temp" value="<?php echo $_POST['temp'] ?>">
<div class="border" spellcheck="false">
<?php if($_POST['mode'] >= 2) { //編集モード ?>
<div class="mode" data-limit="<?php echo $_POST['limit'] ?>">
<span id="text"><i class="<?php echo ($_POST['mode'] == 2) ? 'fas fa-tablet-alt fa-fw"></i>閲覧' : 'far fa-hand-point-up fa-fw"></i>選択' ?>モード</span>
<input type="checkbox" id="toggle" class="toggle" data-mode="<?php echo $_POST['mode'] ?>"><label class="switch" for="toggle"></label>
</div>
<?php } ?>
<?php echo $category1[''] ?>
</div>
<?php if(!$_POST['noform']) { ?>
<div class="space">
<?php if($_POST['mode'] >= 1) { //編集・選択モード ?>
<button><i class="fas fa-save"></i><?php echo ($_POST['mode'] == 3) ? ' 選択と編集' : (($_POST['mode'] == 2) ? ' 編集' : ' 選択') ?>項目を保存</button>&ensp;
<?php } ?>
<button type="button" class="back" onclick="location.href='<?php echo $backpath ?>'"><!--<i class="fas fa-undo-alt"></i> -->戻る</button>
</div>
</form>
<?php } ?>
