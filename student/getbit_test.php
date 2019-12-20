<?php
    require_once "../config.php";
    //login_check('/student/auth/');
    $curl = new Curl($url);

    if (isset($_SESSION['auth']['student_id'])) {
        $student_id = $_SESSION['auth']['student_id'];
    }

    //getSubjectBit・setSubjectBit
    echo "
        <div>
            <h2>Writing / reading affiliation information</h2>
            <span>getSubjectBit・setSubjectBit</span>
        </div>
    ";
    $bit_strings = "abcdef0123456789"; //好きな16進数を入力

    $studentSubjectInfo = new GetBit($curl);
    $user_data = $studentSubjectInfo->setSubjectBit($student_id, 2, $bit_strings); //setterテスト
    $user_data = $studentSubjectInfo->getSubjectBit($student_id, 2); //getterテスト
    //debug($user_data);
?>
