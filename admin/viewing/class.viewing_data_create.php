<?php

class ViewingDataCreate {

	// 秒数への変換
	public function milliSec2Time ( $ms ) {

		$milli_sec = 0;
		$milli_sec = $ms % 1000;
		$ms = ( $ms - $milli_sec ) / 1000;
		return $ms;
	}

	public function milliSec2Time2 ( $ms ){
			/* ミリ秒を時分秒へ変換 引数msはミリ秒 */
			//var hour, minute, sec;
			$hour      = 0;
			$minute    = 0;
			$sec       = 0;
			$milli_sec = 0;

			$milli_sec = $ms % 1000;
			$ms        = ( $ms - $milli_sec ) / 1000;
			$sec       = $ms % 60;
			$ms        = ( $ms - $sec ) / 60;
			$minute    = $ms % 60;
			$hour      = ( $ms - $minute ) / 60;
			// 文字列として連結
			return $hour .":" . (( $minute < 10) ? "0" : ""). $minute .":".(( $sec < 10) ? "0" : "") . $sec;

	}

	// speed_id color-code 判定
	public function speedValueNumber ( $speed_id ) {

		switch ( $speed_id ) {

			case 0: {
				return  "#444444";
			}
				break;
			case 1: {
				return  "#03A9F4";
			}
				break;
			case 2: {
				return  "#29B269";
			}
				break;
			case 4: {
				return  "#FF4081";
			}
				break;
			case '10': {
				return  "#03A9F4";
			}
				break;
			case 11: {
				return  "#07AAE6";
			}
				break;
			case 12: {
				return  "#0BABD8";
			}
				break;
			case 13: {
				return  "#0EACCA";
			}
				break;
			case 14: {
				return  "#12ADBC";
			}
				break;
			case 15: {
				return  "#16AEAF";
			}
				break;
			case 16: {
				return  "#1AAEA1";
			}
				break;
			case 17: {
				return  "#1EAF93";
			}
				break;
			case 18: {
				return  "#21B085";
			}
				break;
			case 19: {
				return  "#25B177";
			}
				break;
			case 20: {
				return  "#29B269";
			}
				break;
			case 21: {
				return  "#3EB565";
			}
				break;
			case 22: {
				return  "#54B761";
			}
				break;
			case 23: {
				return  "#69BA5D";
			}
				break;
			case 24: {
				return  "#7FBD59";
			}
				break;
			case 25: {
				return  "#94C055";
			}
				break;
			case 26: {
				return  "#A9C250";
			}
				break;
			case 27: {
				return  "#BFC54C";
			}
				break;
			case 28: {
				return  "#D4C848";
			}
				break;
			case 29: {
				return  "#EACA44";
			}
				break;
			case 30: {
				return  "#FFCD40";
			}
				break;
			case 31: {
				return  "#FFBF47";
			}
				break;
			case 32: {
				return  "#FFB14D";
			}
				break;
			case 33: {
				return  "#FFA354";
			}
				break;
			case 34: {
				return  "#FF955A";
			}
				break;
			case 35: {
				return  "#FF8761";
			}
				break;
			case 36: {
				return  "#FF7867";
			}
				break;
			case 37: {
				return  "#FF6A6E";
			}
				break;
			case 38: {
				return  "#FF5C74";
			}
				break;
			case 39: {
				return  "#FF4E7B";
			}
				break;
			case 40: {
				return  "#FF4081";
			}
				break;

		}
		//return $reason_value;
	}



}

?>
