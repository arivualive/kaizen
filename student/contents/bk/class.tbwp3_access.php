<?php

class Tbwp3Access {

	private $id;
	private $pw;
	private $logs;
	private $blocks;
	private $results;

	function __construct() {

		$this->id = 'kaizen2.net';
		$this->pw = 'uUoltCgN8boPCf1f';
		$this->logs = [];
		$this->results = [];
		$this->blocks = [];

	}

	// tbwp3サーバーへ送る為にjsonで加工
	public function jsonEncodeLogCode ( $code ) {

		$p3code = [
			'id' => $this->id,
			'password' => $this->pw,
			'codes' => [
					$code
			]
		];

		$json = json_encode( $p3code );
		$send_json = [ 'json' => $json ];

		return $send_json;

	}

	// tbwp3サーバーからログを受け取る
	public function returnedLogData ( $data ) {

		$headers = array(
			'Content-Type: application/x-www-form-urlencoded',
		);

		$returned_json = file_get_contents (
			'https://tbwp3.kaizen2.net/user-datas/logs',
			false,
			stream_context_create([
				'http' => [
					'method' => 'POST',
					'content' => http_build_query( $data ),
					'header' => implode("\r\n", $headers )
				]
			])
		);

		$returned_json = json_decode( $returned_json, true );
		return $returned_json;

	}

	// log 初期データ格納
	public function firstDataCreate ( $datas ) {
		// 単位をミリ秒にする為、*10
		$duration = $datas[ 'datas' ][ 0 ][ 'duration' ] * 10;

		foreach ( $datas[ 'datas' ] as $data ) {
			// 初期データを$tmpとして保存
			$tmp = [
				'speed_number'  => 0,
				'progress_time' => 0,
				'initial_frame' => $data[ 'initial_frame' ],
				'initial_time'  => $data[ 'initial_time' ],
				'position'      => $data[ 'initial_frame' ] * 10,
				'volume_number' => $data[ 'initial_volume' ],
				'duration'      => $duration,
				'last_time'     => $data[ 'last_time' ]
			];
			// log を取得
			foreach ( $data[ 'logs' ] as $log ) {
				array_push( $this->logs, $log );
			}
			// blockを取得
			foreach ( $data[ 'blocks' ] as $block ) {
				array_push( $this->blocks, $block );
			}

		}

		return(
			array(
				"tmp" => $tmp,
				"logs" => $this->logs,
				"bloks" => $this->blocks
			)
		);

	}

	// DB event table 格納用に
	public function secondDataCreate ( $returned_json ) {

		//$results = [];
		$duration = $returned_json[ 'datas' ][ 0 ][ 'duration' ] * 10;

		foreach ( $returned_json[ 'datas' ] as $data ) {

			$tmp = [
				'speed_number' => 0,
				'progress_time' => 0,
				'position' => $data[ 'initial_frame' ] * 10,
				'volume_number' => $data[ 'initial_volume' ]
			];

			$_logs = [];

			foreach ( $data[ 'logs' ] as $log ) {
				$_log = [];

				$_log[ 'progress_time' ] = $log[ 'time' ] - $data[ 'initial_time' ];

				switch ( $log[ 'type' ] ) {

					case 'SPEED' : {
						$ratio = $tmp[ 'speed_number' ] / 10;
						$elapsed = $_log[ 'progress_time' ] - $tmp[ 'progress_time' ];
						$addition = $elapsed * $ratio;
						$position = $tmp[ 'position' ] + $addition;
						$_log[ 'position' ] = (int)floor( $position / 10 ) * 10;

						if ( $_log[ 'position' ] >= $duration ) {
							$_log[ 'position' ] = $duration;
						}

						$tmp[ 'position' ] = $position;
						$_log[ 'action_number' ] = 2;
						$_log[ 'speed_number' ] = $log[ 'value' ];
						$_log[ 'volume_number' ] = $tmp[ 'volume_number' ];
						$tmp[ 'speed_number' ] = $log[ 'value' ];
					}
						break;

					case 'FRAME' : {
						$_log[ 'position' ] = $log[ 'value' ] * 10;

						if ( $_log[ 'position' ] >= $duration ) {
							$_log[ 'position' ] = $duration;
						}

						$tmp[ 'position' ] = $_log[ 'position'];
						$_log[ 'action_number' ] = 1;
						$_log[ 'speed_number' ] = $tmp[ 'speed_number' ];
						$_log[ 'volume_number' ] = $tmp[ 'volume_number' ];
					}
						break;

					case 'VOLUME' : {
						$ratio = $tmp[ 'speed_number' ] / 10;
						$elapsed = $_log[ 'progress_time' ] - $tmp[ 'progress_time' ];
						$addition = $elapsed * $ratio;
						$position = $tmp[ 'position' ] + $addition;
						$_log[ 'position' ] = (int)floor( $position / 10 ) * 10;

						if ( $_log[ 'position' ] >= $duration ) {
							$_log[ 'position' ] = $duration;
						}

						$tmp[ 'position' ] = $position;
						$_log[ 'action_number' ] = 3;
						$_log[ 'speed_number' ] = $tmp[ 'speed_number' ];
						$_log[ 'volume_number' ] = $log[ 'value' ];
						$tmp[ 'volume_number' ] = $_log[ 'volume_number' ];

					}
						break;
				}

				$tmp[ 'progress_time' ] = $_log[ 'progress_time' ];


				$r_count = count( $log[ 'reasons' ] );
				$r_count = $r_count - 1;

				$_log[ 'reasons' ] = [];

				for ( $r = 0; $r <= $r_count; $r++ ) {
					$reason_value = $this->reasonValueNumber( $log[ 'reasons' ][ $r ][ 'value' ] );
					$_log[ 'reasons' ][ $r ] = $reason_value;
				}

				array_push( $_logs, $_log );
			}
			//array_push( $results, $_logs );

		}
		return $_logs;
	}

	// block-data 格納用データ
	public function contentsBlocks ( $returned_json, $data ) {

		$blocks = [];
		$blocks[ 'contents_number' ] = $data[ 'contents_number' ];

		foreach ( $returned_json[ 'datas' ] as $key => $data ) {

			foreach ( $data[ 'blocks' ] as $key => $block ) {

				$blocks[ 'block' ][ $key ][ 'first_frame' ] = $block[ 'first_frame' ];
				$blocks[ 'block' ][ $key ][ 'final_frame' ] = $block[ 'final_frame' ];
			}
		}
		return $blocks;
	}

	// seek_bar event の判定
	public function seekBarEventJudge ( $data ) {

		$data_count = count( $data ) - 1;

		for ( $i = 0; $i < $data_count; $i++ ) {

			$r_count = count( $data[ $i ][ 'reasons' ] );

			for ( $r = 0; $r < $r_count; $r++ ) {
				if ( $data[ $i ][ 'reasons' ][ $r ] == 11 ) {

					$seek_position   = $data[ $i ][ 'position' ];
					$before_position = $data[ $i-1 ][ 'position' ];
					// seek_bar 前へ
					if ( $seek_position >= $before_position ) {
						$data[ $i ][ 'reasons' ][ $r ] = 24;
					// seek_bar 後ろへ
					} else {
						$data[ $i ][ 'reasons' ][ $r ] = 25;
					}

				}

			}

		}

		return $data;
	}

	// 新しい視聴割合ここから
	// contentsの長さを100分割
	public function frameLengthDivision ( $data ) {

		$devision = [];
		$duration = $data[ 'datas' ][ 0 ][ 'duration' ] * 10;

		$start_length = 0;

		$test = false;

		$frame_length = floor( $duration / 100 );

		for ( $l = 0; $l < 100; $l++ ) {

			$start_length = $start_length + $frame_length;
			//$devision[ 'proportion' ] = 100;
			$devision[ $l ][ 'devision' ] = $start_length;
			$devision[ $l ][ 'judge' ] = "true";


		}

		 return $devision;
	}

	// position だけをまとめる
	public function positionArray ( $log ) {

		$all_log_count = count( $log );
		$position_array = [];

		for ( $i = 0; $i < $all_log_count; $i++ ) {

			$log_count = count( $log[ $i ] );

			for ( $p = 0; $p < $log_count; $p++ ) {

				if ( $p > 0 ) {

					$position_array[ $i ][ $p - 1 ][ 'before_position' ] = $log[ $i ][ $p - 1 ][ 'position' ];
					$position_array[ $i ][ $p - 1 ][ 'position' ] = $log[ $i ][ $p ][ 'position' ];
					$position_array[ $i ][ $p - 1 ][ 'event_reason_id' ] = $log[ $i ][ $p ][ 'event_reason_id' ];
					$position_array[ $i ][ $p - 1 ][ 'reason' ] = $log[ $i ][ $p ][ 'reason' ];
					$position_array[ $i ][ $p - 1 ][ 'duration' ] = $log[ $i ][ $p ][ 'duration' ];

				}

			}

		}

		return $position_array;
	}

	// position の範囲判定
	public function positionRange ( $before_position, $position, $division ) {

		$division_count = count( $division );

		for ( $d = 0; $d < $division_count; $d++ ) {

				if ( $division[ $d ][ 'devision' ] >= $before_position && $division[ $d ][ 'devision' ] <= $position ) {
						$division[ $d ][ 'judge' ] = "false";
				}

		}
		return $division;
	}

	// 途中で止められた時の対応
	public function lastPositionJudge ( $last, $division ) {

		$division_count = count( $division );

		for ( $d = 0; $d < $division_count; $d++ ) {

				if ( $division[ $d ][ 'devision' ] > $last ) {
						$division[ $d ][ 'judge' ] = "false";
				}

		}
		return $division;

	}

	// 視聴したかのjudgeを結合する
	public function lastPuroportionJudge ( $division_judge ) {

		$all_judge_count = count( $division_judge );
		$all_judge = [];
		$return_judge = [];

		$return_judge = $division_judge[ 0 ];

		$count = 1;

		$division_count = count( $division_judge[ 0 ] ); // 100

		for ( $c = 0; $c < $division_count; $c++ ) {

			for ( $all = 0; $all < $all_judge_count; $all++ ) {

				if ( $division_judge[ $all ][ $c ][ 'judge' ] === "true" ) {

					$return_judge[ $c ] = "true";
					$return_judge[ 'proportion' ] = $count++;
					break;

				} else {

					$return_judge[ $c ] = $division_judge[ $all ][ $c ][ 'judge' ];

				}
			}
		}

		return $return_judge;
	}

	// 分割したコンテンツのpositionとログデータのpositionの比較
	public function newProportionJudgement ( $log, $division ) {

		$position_array = [];
		$division_judge = [];
		$return_judge   = [];
		$position_array = $this->positionArray( $log );

		$all_log = count( $position_array );
		$division_count = count( $division );

		$duration = $position_array[ 0 ][ 0 ][ 'duration' ] - 10;
		// 視聴回数分
		for ( $a = 0; $a < $all_log; $a++ ) {

			$division_judge[ $a ] = $division;

			$one_log = count( $position_array[ $a ] );
			// event 分
			for ( $p = 0; $p < $one_log; $p++ ) {

				$before_position = $position_array[ $a ][ $p ][ 'before_position' ];
				$position = $position_array[ $a ][ $p ][ 'position' ];

				$last_position = $position_array[ $a ][$one_log - 1][ 'position' ];

				if ( $position_array[ $a ][ $p ][ 'reason' ] === "NEXT_BUTTON" ) {

					$division_judge[ $a ] = $this->positionRange( $before_position, $position, $division_judge[ $a ] );

				} else if ( $position_array[ $a ][ $p ][ 'reason' ] === "BEFORE_SEEK" ) {

					$division_judge[ $a ]  = $this->positionRange( $before_position, $position, $division_judge[ $a ] );

				}
				// 途中で止められた場合、見てないposition を false へ
				if ( $last_position < $duration ) {
					$division_judge[ $a ] = $this->lastPositionJudge ( $last_position, $division_judge[ $a ] );
				}

			}

		}

		$return_judge = $this->lastPuroportionJudge( $division_judge );
		return $return_judge;

	}

	// reason_value 判定
	public function reasonValueNumber ( $reason ) {

		switch ( $reason ) {

			case 'INVISIBLE': {// ブラウザのページが見れない状態なので停止（タブが非アクティブな時やタブレットブラウザの非アクティブ時
				$reason_value = 1;
			}
				break;
			case 'WAIT_TOUCH_MEDIA': {// ユーザーからのタッチ（メディアダウンロードで必要）を待っている為停止）
				$reason_value = 2;
			}
				break;
			case 'PAUSE': {// ユーザーからのポーズボタンが押された
				$reason_value = 3;
			}
				break;
			case 'SEARCH': {// シークバーでサーチ中の為、停止
				$reason_value = 4;
			}
				break;
			case 'ALL_CLEAR': {// すべての停止要素がクリアーされたため再生再開
				$reason_value = 5;
			}
				break;
			case 'PLAY_BUTTON': {// プレイボタンが押された
				$reason_value = 6;
			}
				break;
			case 'PAUSE_BUTTON': {// ポーズボタンが押された
				$reason_value = 7;
			}
				break;
			case 'TOP_BUTTON': {// トップボタンが押された
				$reason_value = 8;
			}
				break;
			case 'PREVIOUS_BUTTON': {// 前ブロックボタンが押された
				$reason_value = 9;
			}
				break;
			case 'WAIT_MEDIA': {// メディア（音声、動画）の読み込待ちにより停止
				$reason_value = 10;
			}
				break;
			case 'SEEK_BAR': {// シークバーが操作された
				$reason_value = 11;
			}
				break;
			case 'AUTO_REPEAT': {// オートリピート機能が働き、0フレームへ移動
				$reason_value = 12;
			}
				break;
			case 'CHAPTER': {// チャプターボタンが押された
				$reason_value = 13;
			}
				break;
			case 'SPEED_BAR': {// スピードバーが操作された
				$reason_value = 14;
			}
				break;
			case 'WAIT_TOUCH_FULL_SCREEN': {// ユーザーからのタッチ（フルスクリーンで必要）を待っている為停止
				$reason_value = 15;
			}
				break;
			case 'BLOCK_HOLD': {// コンテンツにブロック最終フレーム保持機能を設定されている為停止
				$reason_value = 16;
			}
				break;
			case 'NEXT_BUTTON': {// 後ブロックボタンが押された
				$reason_value = 17;
			}
				break;
			case 'LOAD_IMAGE': {// イメージをダウンロード中
				$reason_value = 18;
			}
				break;
			case 'LOAD_PEN': {// ペンデータをダウンロード中
				$reason_value = 19;
			}
				break;
			case 'LOAD_AUDIO': {// 音声をダウンロード中
				$reason_value = 20;
			}
				break;
			case 'LOAD_VIDEO': {// 動画をダウンロード中
				$reason_value = 21;
			}
				break;
			case 'VOLUME_BAR': {// ボリュームバーが操作された
				$reason_value = 22;
			}
				break;
			case 'SWITCHING_SPEED': {// 視聴スピード切り替え
				$reason_value = 23;
			}
				break;
		}
		return $reason_value;
	}

	///////////////////////////////////////////////////////////////////////.mozilla
	// 視聴割合計算
	/*
	public function proportionJudgement ( $data ) {

		$return_sort_data = [];
		$position = [];
		// この生徒のこのコンテンツの視聴ログのpositionを昇順に並び替える
		foreach ( $data as $key => $value) {
			$position[ $key ] = $value[ 'position' ];
		}

		array_multisort( $position, SORT_ASC, $data );

		$log_count = count( $data );
		$plus_calculation  = 0;
		$minus_calculation = 0;
		$calculation = 0;

		for ( $r = 0; $r < $log_count; $r++ ) {
			// NEXT_BUTTON(10) event & SEEK_BAR(11) event & BEFORE_SEEK(24) 値はプラスになる（ 見ていない部分 ）
			if ( $data[ $r ][ 'event_reason_id' ] == 10 || $data[ $r ][ 'event_reason_id' ] == 11
		 		|| $data[ $r ][ 'event_reason_id' ] == 24 ) {

				$calculation = $data[ $r ][ 'position' ] - $data[ $r-1 ][ 'position' ] + $calculation;

			}
			// SEARCH(4) event & PAUSE(3) event & PREVIOUS_BUTTON(9) event
			// AFTER_SEEK(25) event 値はマイナスになる　（ 見た部分 ）
			if ( $data[ $r ][ 'event_reason_id' ] == 3 || $data[ $r ][ 'event_reason_id' ] == 4
				|| $data[ $r ][ 'event_reason_id' ] == 9 || $data[ $r ][ 'event_reason_id' ] == 25 )
			{

				$calculation = $data[ $r-1 ][ 'position' ] - $data[ $r ][ 'position' ] + $calculation;
			}
		}

		// position の最大値を取得
		$max_position = $data[ $log_count - 1 ][ 'position' ];

		//$judge = $plus_calculation + $minus_calculation;
		// 見た割合
		$position_judge = $max_position - $calculation;

		$judge = $position_judge / $data[ 0 ][ 'duration' ] * 100;
		//$judge = round( 100 - $judge );

		if ( $judge > 100 ) {
			$judge = 100;
		}

		return $judge;

	}
	*/

	// log 初期データ格納
	/*
	public function firstDataCreate ( $datas ) {
		// 単位をミリ秒にする為、*10
		$duration = $datas[ 'datas' ][ 0 ][ 'duration' ] * 10;

		foreach ( $datas[ 'datas' ] as $data ) {
			// 初期データを$tmpとして保存
			$tmp = [
				'speed_number'  => 0,
				'progress_time' => 0,
				'initial_frame' => $data[ 'initial_frame' ],
				'initial_time'  => $data[ 'initial_time' ],
				'position'      => $data[ 'initial_frame' ] * 10,
				'volume_number' => $data[ 'initial_volume' ],
				'duration'      => $duration,
				'last_time'     => $data[ 'last_time' ]
			];
			// log を取得
			foreach ( $data[ 'logs' ] as $log ) {
				array_push( $this->logs, $log );
			}
			// blockを取得
			foreach ( $data[ 'blocks' ] as $block ) {
				array_push( $this->blocks, $block );
			}

		}

		return(
			array(
				"tmp" => $tmp,
				"logs" => $this->logs,
				"bloks" => $this->blocks
			)
		);

	}
	*/
	/*
	public function secondDataCreate ( $tmp_and_logs ) {

		$_logs = [];

		foreach ( $tmp_and_logs[ 'logs' ] as $log ) {
			$_log = [];
			$_log[ 'progress_time' ] = $log[ 'time' ] - $tmp_and_logs[ 'tmp' ][ 'initial_time' ];

			switch ( $log[ 'type' ] ) {
				case 'SPEED': {
					$_log = $this -> typeSpeed ( $tmp_and_logs, $_log, $log );
				}
					break;
				case 'FRAME' : {
					$_log = $this -> typeFrame ( $tmp_and_logs, $_log, $log );
				}
					break;
				case 'VOLUME' : {
					$_log = $this -> typeVolume ( $tmp_and_logs, $_log, $log );
				}
					break;

			}

			$r_count = count( $log[ 'reasons' ] );
			$r_count = $r_count - 1;

			$_log[ 'log2' ][ 'reasons' ] = [];

			for ( $r = 0; $r <= $r_count; $r++ ) {
				$reason_value = $this->reasonValueNumber( $log[ 'reasons' ][ $r ][ 'value' ] );
				$_log[ 'log2' ][ 'reasons' ] = $reason_value;
			}

		//return $reason_value;

			$tmp_and_logs = $_log[ 'tmp' ];
			$tmp_and_logs[ 'progress_time' ] = $_log[ 'log2' ][ 'progress_time' ];
			array_push( $_logs, $_log[ 'log2' ] );
		}

		return $_logs;
	}
	*/



}

?>
