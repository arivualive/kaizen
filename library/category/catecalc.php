<?php
//識別文字を配列に変換（複数の値をリストに渡す）
if(isset($_POST['string'])) {
	list($check, $temp) = string_to_array($_POST['string']);
} elseif(isset($database)) {
	list($check, $temp) = string_to_array($database);
}

//識別文字を生成（複数の値をリストに渡す）
if(isset($_POST['category'])) {
	list($string, $check) = array_to_string((array)$_POST['category']);
} elseif(isset($_POST['flag'])) {
	list($string, $check) = array(0, array());
}

//各種変数・配列を生成
list($csvTree, $csvRowA, $csvRowC, $csvRowD, $csvRowE) = load_csv($_POST['csvfile'], 0); //CSVファイルを処理

$select = "\n" . '<i class="fas fa-sticky-note fa-fw"></i>項目' . "\n";
for($current = count($csvTree); $current >= 1; $current--) {
	foreach($csvTree[$current] as $value) {
		foreach($value as $line) {
			//フォルダと項目を判別（真はフォルダ、否は項目）
			$order = (!empty($csvTree[$current + 1][$line])) ? 1 : 0;

			//（-）で value値を分割し一時保存の配列に格納
			$temp = explode('-', $line);
			//配列の底を2とする対数のキーが空でなければ $select に追加
			if(!empty($check[$temp[0]][log(hexdec($temp[1]), 2)])) {
				//選択された項目を生成（フォルダは上に、項目は下に追加）
				if($order) {
					$select = $csvRowD[$line] . '（' . $line . '）' . "\n" . $select;
					$selectFolder[] = $line;
				} else {
					$select .= $csvRowD[$line] . '（' . $line . '）' . "\n";
					$selectItem[] = $line;
				}
				$selectAll[] = $line;
				$selectALLtree[$csvRowA[$line]][] = $line;
			}
		}
	}
}
//選択項目がない場合は $select を空に
$select = ($select == "\n" . '<i class="fas fa-sticky-note fa-fw"></i>項目' . "\n") ? '' : '<i class="fas fa-folder fa-fw"></i>フォルダ' . "\n" . $select;

//識別文字を配列に変換
function string_to_array($DATA) {
	$temp = explode('-', $DATA); //（-）で文字列を分割し一時保存の配列に格納

	foreach($temp as $key => $value) { //一時保存の配列をループ処理
		$check[$key + 1] = array_reverse(str_split(base_convert($value, 16, 2))); //2進数に変換した値を1文字ずつ配列に格納し要素を逆順
	}

	return array($check, $temp); //配列と一時保存の配列（説明用）を返す
}

//識別文字を生成
function array_to_string($DATA) {
	//配列を自然順でソート
	natsort($DATA);

	foreach($DATA as $value) {
		$temp = explode('-', $value); //（-）で文字列を分割し一時保存の配列に格納
		$check[$temp[0]][log(hexdec($temp[1]), 2)] = hexdec($temp[1]); //分割された文字と配列の底を2とする対数のキーに格納（PHP 7.0対応）
	}
	end($check); //配列の最後の値を取得

	//配列を最後の値のキーまでループ処理
	$string = '';
	for($temp = 1; $temp <= key($check); $temp ++) {
		if(empty($check[$temp])) {
			$string .= '-0'; //配列が空の場合は（-0）で連結
		} else {
			$string .= '-' . dechex(array_sum($check[$temp])); //配列を合計して16進数に変換し（-）で連結
		}
	}

	$string = ltrim($string, '-'); //文字の先頭から（-）を削除
	return array($string, $check); //識別文字と照合用配列を返す
}

//カテゴリーを照合
function search_value($DATA, $VERIFY, $SELECT) {
	if($DATA) {
		$count = '';
		foreach($DATA as $value) {
			//「value」値と文字列を区別
			$search = (strpos($value, '-0x') !== false) ? '（' . $value . '）' : $value . '（';
			//検索文字が一致した場合、カウントアップ
			if(strpos($SELECT, $search) !== false) { $count ++; }
		}

		//一致方法ごとに処理を分岐
		switch($VERIFY) {
			case 1: if($count) { return 1; } break;
			default: if($count == count($DATA)) { return 1; }
		}
	}
}

//関連付けカテゴリーを照合
function search_multi($DATA, $VALUE, $CSVTREE, $SELECT) {
	if($DATA) {
		list(,,,, $csvRowE) = load_csv($DATA, 1);
		list($check) = string_to_array($csvRowE[$VALUE]);

		for($current = count($CSVTREE); $current >= 1; $current--) {
			foreach($CSVTREE[$current] as $value) {
				foreach($value as $line) {
					//フォルダと項目を判別（真はフォルダ、否は項目）
					$order = (!empty($CSVTREE[$current + 1][$line])) ? 1 : 0;
					//（-）で value値を分割し一時保存の配列に格納
					$temp = explode('-', $line);
					//配列の底を2とする対数のキーが空でなければ $selectItem に追加
					if(!empty($check[$temp[0]][log(hexdec($temp[1]), 2)]) && !$order) { $selectItem[] = $line; }
				}
			}
		}
		//カテゴリーを照合（引数:照合文字, 照合条件, チェック用配列）
		if(search_value($selectItem, 1, $SELECT)) { return 1; }
	}
}

//CSVファイルを処理
function load_csv($DATA, $NOSAVE) {
	//$temp = explode('/', $DATA);
	//$name = $temp[count($temp) - 1];
	//$backup = substr($DATA, 0, strpos($DATA, $name)) . 'BK_' .  substr($name, 0, strpos($name, '.')); //CSVバックアップフォルダ

	//CSVファイルを読み込み「UTF-8」に変換
	$lines = @file($DATA, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(!$lines) { $lines = array(); }
	mb_convert_variables('UTF-8', 'SJIS-win', $lines);
	//先頭の2行を削除して詰める
	unset($lines[0], $lines[1]);
	$lines = array_values($lines);

	if(!$NOSAVE) {
		//コンマと区切り文字、禁止文字を置換
		if(isset($_POST['save'])) {
			foreach((array)$_POST['save'] as $value) {
				$value = str_replace(',', '{c}', $value);
				$value = str_replace('{}', ',', $value);
				$save[] = htmlspecialchars($value, ENT_QUOTES);
			}
		} else {
			$save = array();
		}
		//ツリーに変更があった場合は保存
		if(isset($_POST['flag']) && $save != $lines) {
			//ヘッダーを追加
			$category = '32ビット拡張版（31bit 0x40000000 まで可）' . "\r\n\r\n";
			$category .= 'カテゴリー,value値,親カテゴリー,項目名,関連付け' . "\r\n";
			//POSTされた配列を「Shift-JIS」で保存
			foreach((array)$save as $value) { $category .= $value . "\r\n"; }
			//バックアップフォルダの存在確認
			//if(!file_exists($backup)) { mkdir($backup, 0777); }
			//バックアップしてファイルを保存
			//if(file_exists($DATA)) { rename($DATA, $backup . '/' . date('Ymd-His') . '.txt'); }
			if(file_exists($DATA)) { rename($DATA, $DATA . '.bak'); }
			file_put_contents($DATA, mb_convert_encoding($category, 'SJIS-win', 'UTF-8'), LOCK_EX);

			$lines = $save;
		}
	}

	$csvTree = $csvRowA = $csvRowC = $csvRowD = $csvRowE = array();
	foreach($lines as $line) {
		$item = explode(',', $line);
		$item[3] = str_replace('{c}', ',', $item[3]);

		if(preg_match('/^[1-4]$/', $item[0])) {
			$csvTree[$item[0]][$item[2]][] = $item[1];
			$csvRowA[$item[1]] = $item[0];
			$csvRowC[$item[1]] = $item[2];
			$csvRowD[$item[1]] = $item[3];
			$csvRowE[$item[1]] = $item[4];
		}
	}

	return array($csvTree, $csvRowA, $csvRowC, $csvRowD, $csvRowE); //作業用配列・変数を返す
}
?>
