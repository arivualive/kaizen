<?php
// 指定した位置のパーミッションフラグが立っているかの検証
function isPermissionFlagOn($permissionString, $targetString)
{
    // 検証するビットの位置と値
    list($targetIndex, $targetBit) = explode("-", $targetString);
    --$targetIndex;
    $targetBit = hexdec($targetBit);
    
    // 管理者が持っているパーミッションビット群の文字列(データベースに入っている値)を
    // 整数値の配列に変換
    $permissionBitsArray = explode("-", $permissionString);
    foreach ($permissionBitsArray as $i => $bits) {
        $permissionBitsArray[$i] = hexdec($bits);
    }

    // 検証するビットの位置がパーミッションビット群の範囲外(当然フラグは立っていない)
    if (count($permissionBitsArray) <= $targetIndex) {
        return false;
    }

    // ビットが立っているかの検証
    if ( ($permissionBitsArray[$targetIndex] & $targetBit) != 0) {
        return true;
    }

    return false;
}

// isPermissionFlagOn関数の検査対象を可変長引数を用いて複数指定できるようにしたもの
// 指定したフラグ群のいずれかが立っていればtrue
function isPermissionFlagOnArray($permissionString, ...$targets)
{
    foreach ($targets as $targetBit) {
        if (isPermissionFlagOn($permissionString, $targetBit)) {
            return true;
        }
    }

    return false;
}

// 指定したパーミッションフラグが立っていれば"checked"を出力
function setPermissionCheck($permissionString, $targetString)
{
    if ( isPermissionFlagOn($permissionString, $targetString) ) {
        echo "checked";
    }
}
