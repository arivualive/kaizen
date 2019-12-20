<?php if($parts == 'Input') { //識別文字入力 ?>
<?php
$search = '';
list(,,, $csvRowD) = load_csv($_POST['csvfile'], 1);
foreach((array)$csvRowD as  $key => $value) {
	$search .= '<option value="' . $key . '"';
	if(isset($_POST['search'])) { foreach((array)$_POST['search'] as $line) { if($line == $key) { $search .= ' selected'; } } }
	$search .= '>' . $value . '（' . $key . '）</option>' . "\n";
}

$icon = (isset($_POST['search'])) ? ((search_value($_POST['search'], $_POST['verify'], $select)) ? 'fas fa-lightbulb' : 'fas fa-bomb') : 'fas fa-balance-scale';
?>
<script>
if(navigator.userAgent.indexOf('iPhone')>0||navigator.userAgent.indexOf('iPad')>0||navigator.userAgent.indexOf('iPod')>0||navigator.userAgent.indexOf('Android')>0){document.write('<style>select[name=\'search[]\']{font-size:16px;padding:0 0 0 8px;height:36px}</style>');}
</script>
<br>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" autocomplete="off" method="POST">
<input type="hidden" name="csvfile" value="<?php echo $_POST['csvfile'] ?>">
<input type="hidden" name="mode" value="<?php if(isset($_POST['mode'])) echo $_POST['mode'] ?>">
<input type="hidden" name="temp" value="<?php if(isset($_POST['temp'])) echo $_POST['temp'] ?>">
<div class="border2 none">
<div class="box">
<i class="fas fa-qrcode fa-fw"></i>カテゴリー識別文字［name="string"］<br>
<input type="text" size="44" name="string" pattern="^[a-f\d\-]+$" value="<?php echo (isset($_POST['flag'])) ? $string : ((isset($_POST['string'])) ? $_POST['string'] : $database) ?>" placeholder="00000000-00000000-00000000-00000000-00000000" spellcheck="false" required><br>
<br>
<label for="and" class="item"><input type="radio" name="verify" value="0" class="margin line2" id="and"<?php if(empty($_POST['verify'])) echo ' checked' ?>><i class="fas fa-tags fa-fw"></i>AND（全て一致）</label>
<label for="or" class="item"><input type="radio" name="verify" value="1" class="margin line2" id="or"<?php if(!empty($_POST['verify'])) echo ' checked' ?>><i class="fas fa-tag fa-fw"></i>OR（部分一致）</label><span class="item">［name="verify"］</span>
<?php if(isset($_POST['search'])) echo "<br>\n<br>\n" . $result . "\n" ?>
</div>
<div class="box">
<i class="<?php echo $icon ?> fa-fw"></i>カテゴリー照合［name="search[]"］<br>
<select name="search[]" size="10" class="line2" multiple>
<?php echo $search ?>
</select>
</div>
<br>
<button class="button2"><i class="fas fa-calculator"></i> 識別文字を送信</button>
<?php if(!preg_match('/iPhone|iPod|iPad|Android/ui', $_SERVER['HTTP_USER_AGENT'])) { ?>
<button type="button" id="copy" class="button2"><i class="fas fa-clipboard"></i></button>
<span id="alert">&nbsp;</span>
<?php } ?>
</div>
</form>
<?php } elseif($parts == 'Mode') { //表示モード切替（0:閲覧 1:選択 2:編集 3:選択＆編集） ?>
<?php
$csvfile = '';
$lines = glob($csvpath . '{*.csv}', GLOB_BRACE);
foreach($lines as $line) {
	if(is_file($line)) {
		$csvfile .= '<option value="' . $line . '"';
		if($_POST['csvfile'] == $line) { $csvfile .= ' selected'; }
		$csvfile .= '>' . str_replace($csvpath, '', $line) . '</option>' . "\n";
	}
}
?>
<br>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="POST">
<div class="border3 none">
<div class="box">
<i class="fas fa-th-list fa-fw"></i>ツリーCSVファイル［name="csvfile"］<br>
<select name="csvfile" class="line3">
<?php echo $csvfile ?>
</select>
</div>
<div class="box">
<i class="fas fa-images fa-fw"></i>表示モード［name="mode"］<br>
<div class="radio">
<label for="view" class="item"><input type="radio" name="mode" value="0" class="margin line3" id="view"<?php if(empty($_POST['mode'])) echo ' checked' ?>><i class="fas fa-tablet-alt fa-fw"></i>閲覧</label>&nbsp;
<label for="select" class="item"><input type="radio" name="mode" value="1" class="margin line3" id="select"<?php if(isset($_POST['mode']) && $_POST['mode'] == 1) echo ' checked' ?>><i class="far fa-hand-point-up fa-fw"></i>選択</label>&nbsp;
<label for="edit" class="item"><input type="radio" name="mode" value="2" class="margin line3" id="edit"<?php if(isset($_POST['mode']) && $_POST['mode'] == 2) echo ' checked' ?>><i class="fas fa-pencil-alt fa-fw"></i>編集</label>&nbsp;
<label for="multi" class="item"><input type="radio" name="mode" value="3" class="margin line3" id="multi"<?php if(isset($_POST['mode']) && $_POST['mode'] == 3) echo ' checked' ?>><i class="fas fa-wrench fa-fw"></i>選択&thinsp;&&thinsp;編集</label>&nbsp;&nbsp;
<label for="temp" class="item"><input type="checkbox" name="temp" value="1" class="margin line3" id="temp"<?php if(isset($_POST['temp'])) echo ' checked' ?>><i class="fas fa-microchip fa-fw"></i>$csv[]関連配列</label>
</div>
</div>
<br>
<button class="button3"><i class="fas fa-exchange-alt"></i> 表示を切替</button>
</div>
</form>
<?php } elseif($parts == 'Result') { //結果参照 ?>
<br>
<?php
	function preformatted($caption, $color, $echo, $print) {
		echo '<div class="box box2">' . "\n" . $caption . "\n" . '<pre';
		if($color) { echo ' style="background-color:' . $color . '"'; }
		echo '>' . "\n";

		if($echo) { echo $echo; }
		if($print) { print_r($print); }
		echo '</pre>' . "\n" . '</div>' . "\n";
	}

	if(isset($_POST['temp'])) {
		preformatted('$csvTree[] <font>キー名:親 / 配列:value値</font>', '#f6fff6', '', $csvTree);
		preformatted('$csvRowA[] <font>キー名:value値 / 配列:カテゴリー</font>', '#f6fff6', '', $csvRowA);
		preformatted('$csvRowC[] <font>キー名:value値 / 配列:親カテゴリー</font>', '#f6fff6', '', $csvRowC);
		preformatted('$csvRowD[] <font>キー名:value値 / 配列:項目名</font>', '#f6fff6', '', $csvRowD);
		preformatted('$csvRowE[] <font>キー名:value値 / 配列:関連付け</font>', '#f6fff6', '', $csvRowE);
	}
	if($select) { preformatted('$select <font>選択されたカテゴリー</font>', '#f6f6ff', $select, ''); }
	if(isset($selectAll)) { preformatted('$selectAll[] <font>選択されたカテゴリー配列</font>', '#f6f6ff', '', $selectAll); }
	if(isset($selectALLtree)) { preformatted('$selectALLtree[] <font>選択されたカテゴリー連想配列</font>', '#f6f6ff', '', $selectALLtree); }
	if(isset($selectFolder)) { preformatted('$selectFolder[] <font>選択されたフォルダー配列</font>', '#f6f6ff', '', $selectFolder); }
	if(isset($selectItem)) { preformatted('$selectItem[] <font>選択された項目配列</font>', '#f6f6ff', '', $selectItem); }

	if(isset($_POST['category'])) {
		preformatted('$_POST[\'category\'] <font>POSTされたカテゴリー選択配列</font>', '', '', $_POST['category']);
		preformatted('$check[] <font>$_POST[\'category\']から分割し配列に格納</font>', '', '', $check);
	}
	if(isset($_POST['save'])) { preformatted('$_POST[\'save\'] <font>POSTされたCSV保存用配列</font>', '#f6fff6', '', $_POST['save']); }
	if(isset($_POST['string'])) { preformatted('$check[] <font>$_POST[\'string\']から分割し配列に格納</font>', '', '', $check); }
	if(isset($_POST['search'])) { preformatted('$_POST[\'search\'] <font>POSTされたカテゴリー照合配列</font>', '', '', $_POST['search']); }
?>
<?php } elseif($parts == 'Source') { //ソースコード表示 ?>
<script>elements=document.getElementsByName('source');function radioCheck(num){if(elements[num].checked){elements[num].checked=false;}else{elements[num].checked=true;}}</script>
<script src="<?php echo $path ?>items/prettify/run_prettify.js?skin=choka&lang=css&lang=php" defer></script>
<div class="border4 none">
<?php
	$category1[''] = str_replace('</span>', '</span>' . "\n", $category1['']);

	$source = array(
		array('生成リスト', $category1[''], '', 'fas fa-list-alt', '', 'button4'),
		array('CSVファイル', @file_get_contents($_POST['csvfile']), $_POST['csvfile'], 'fas fa-table', '', 'button4'),
		array('JSファイル', @file_get_contents($path . 'category.js'), '', 'fab fa-js-square', '', 'button4'),
		array('計算PHP', @file_get_contents($path . 'catecalc.php'), '', 'fas fa-file-code', 'lang-php', 'button5'),
		array('生成PHP', @file_get_contents($path . 'catetree.php'), '', 'fas fa-file-code', 'lang-php', 'button5'),
		array('生成CSS', @file_get_contents($path . 'category.css'), '', 'fab fa-css3-alt', 'lang-css', 'button5'),
		array('開発用PHP', @file_get_contents($path . 'Develop.php'), '', 'fas fa-file-code', 'lang-php', 'button6'),
		array('開発用CSS', @file_get_contents($path . 'Develop.css'), '', 'fab fa-css3-alt', 'lang-css', 'button6')
	);

	foreach($source as $key => $value) {
		echo '<button type="button" onclick="radioCheck(' . $key . ')" class="' . $value[5] . '"><i class="' . $value[3] . '"></i> ' . $value[0] . '</button>' . "\n";
	}

	foreach($source as $value) {
		echo '<input type="radio" name="source" class="toggle">' . "\n" . '<div class="panel">' . "\n";

		echo '<i class="' . $value[3] . ' fa-fw"></i>' . $value[0] . '（' . mb_detect_encoding($value[1], 'UTF-8, JIS, EUC-JP, SJIS') . '）';
		if($value[2]) { echo ' - <i class="fas fa-download fa-fw"></i><a href="' . $value[2] . '" class="alert">ダウンロード</a>'; }

		echo "\n" . '<pre class="prettyprint linenums select" contenteditable="true" spellcheck="false">' . "\n";
		echo '<code';
		if($value[4]) { echo ' class="' . $value[4] . '"'; }
		echo '>' . htmlspecialchars(rtrim(mb_convert_encoding($value[1], 'UTF-8', 'UTF-8, JIS, eucJP-win, SJIS-win'))) . '</code>' . "\n";
		echo '</pre>' . "\n" . '</div>' . "\n";
	}
?>
</div>
<p class="agent"><?php echo $_SERVER['HTTP_USER_AGENT'] ?></p>
<?php } elseif($parts == 'QRCode') { //QRコード＆ショートURL表示 ?>
<?php
	function get_short_url($long_url) {
		$api_url = 'https://www.googleapis.com/urlshortener/v1/url';
		$api_key = 'AIzaSyA10Bu6r6YYPK_crmDIbleKXssLia78riQ';
		$curl = curl_init("$api_url?key=$api_key");
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, '{"longUrl":"' . $long_url . '"}');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);

		$short_url = json_decode($response) -> id;
		return $short_url;
	}

	$url = get_short_url((empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);
?>
<script src="<?php echo $path ?>items/jquery.qrcode.min.js" defer></script>
<script>$(function() { $('#qrcode').qrcode({ text:'<?php echo $url ?>', size:200, fill:'#333', background:null, quiet:0, render:'canvas', ecLevel:'M' }); });</script>
<br>
<span id="qrcode"></span>
<span class="short"><?php echo str_replace('https://','',$url) ?></span>
<?php } ?>
