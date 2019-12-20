<?php

$quiz_id = filter_input(INPUT_GET, "id");
if ($quiz_id == '') die ('I do not know the quiz number');

$student = array();

$curl = new Curl($url);
$crypt = new StringEncrypt;
$csv = new QuizCsv($quiz_id, $curl);
$result = new QuizResult($quiz_id, $curl);
$answer_obj = new QuizAnswer($quiz_id, $curl);
$query_obj = new Query($quiz_id, $curl);

// tbl_quiz_answerを基にcsvデータを作る
$answer = $csv->getQuizAnswer();
#fb($answer);

// 重複のないstudent_idの抽出
$data = $csv->distinctStudent();
#fb($data);

// idの復号化
foreach ((array) $data as $key => $value) {
    $student[$key] = $csv->getStudent($value['student_id']);
    $student[$key]['id'] = substr($crypt->decrypt($student[$key]['id']), 3);
}
#fb($student);

// answerデータにstudentデータをマージ
foreach ((array) $answer as $key => $value) {
    $skey = array_search($value['student_id'], array_column($student, 'student_id'));
    $answer[$key] += $student[$skey];
}

//fb($answer);

//echo '--answer<br>';
foreach ($answer as $key => $value) {
    //$ans[$key][] = $value['correct_answer_rate'];
    //$ans[$key][] = $value['answer_time'];
    $ans[$key][] = $value['register_datetime'];
    $ans[$key][] = $value['id'];
    $ans[$key][] = $value['student_name'];
    $ans[$key][] = $answer_obj->timeFormat($value['answer_time']);
    $ans[$key][] = $value['total_score'];
    $ans[$key][] = $value['correct_answer_rate'];

    $ans[$key][] = $result->isSuccess($value['total_score']);
    $answer_obj->setAnswerId($value['answer_id']);
    $rank = $answer_obj->findQuizAnswerRank();
    $ans[$key][] = $rank[ 'rank' ];

    $ans[$key][] = $result->deviation();          # 偏差値
    // 2019/6/03 count関数対策
    if(is_countable($answer_obj->findQuizAnswerStudent($value['student_id']))){
      $ans[$key][] = count( $answer_obj->findQuizAnswerStudent($value['student_id']));
    }
    //$ans[$key][] = count( $answer_obj->findQuizAnswerStudent($value['student_id']));
    //fb($data);
}

foreach ($ans as $key => $value) {
    $answer_student[$key] = '"';
    $answer_student[$key] .= implode('","', $value);
    $answer_student[$key] .= '",';
}

//fb($answer_student);

// 質問形式
$query_type_jp = $csv->getMstQueryType();

// クイズ番号からアンサー番号を得る
#fb($answer);
$answer_id = array();
foreach ($answer as $key => $value) {
    $answer_id[] = $value['answer_id'];
}

$query_correct_rate = $result->correctRateQuery();
#fb($query_correct_rate);

//echo '--query<br>';
// クイズ番号からクエリの取得
$query = $csv->getQuizQuery();
// 2019/6/03 count関数対策
$count_query = 0;
if(is_countable($query)){
  $count_query = count( $query );
}
//$count_query = count( $query );

//fb($query);
foreach ($query as $key => $value) {
   $query_data[$key] = sprintf('%s,', $value['query_text']);
   $query_data[$key] .= sprintf('%s,', $value['type_jp']);
   $query_data[$key] .= sprintf('%s,', $value['description']);
   $query_data[$key] .= sprintf('%s,', $query_correct_rate[$key]['query_rate']);
}

//fb($query_data);

//echo '--selection<br>';
// クイズ番号から選択肢の取得
foreach ($query as $key => $value) {
    $selection[$key] = $csv->getQuizQuerySelectionAll($value['query_id']);
}
#fb($selection);

foreach ($selection as $key => $value) {
    foreach ($value as $item) {
        $text[$key][] = $item['text'];
    }

    $select_text[] = implode('|', $text[$key]);
}

//fb($select_text);

//echo '--correct<br>';
// クエリ番号から選択肢の正解を取得
foreach ($query as $value) {
    $correct[] = $csv->getQuizQuerySelectionCorrect($value['query_id']);
}
#fb($correct);

foreach ($correct as $key => $value) {
    foreach ($value as $item) {
        $corct[$key][] = $item['text'];
    }

    $correct_text[] = implode('|', $corct[$key]);
}

//fb($correct_text);

//echo '--answer_id<br>';
//fb($answer_id);
// アンサーIDからクエリアンサーIDを得る
foreach ($answer_id as $key => $value) {
    $answer_query[$key] = $csv->getQuizAnswerQuery($value);
}

//echo '--answer_qyery<br>';
#fb($answer_query);

$mark = '';
foreach ($answer_query as $key => $value) {
    foreach ($value as $item) {
       #$flgright[$key][] = $item['flg_right'];

        if ($item['flg_right'] == 1) {
            $mark = '○';
        } else {
            $mark = '×';
        }

        $flgright[$key][] = $mark;
    }
}
//fb($flgright);

foreach ($answer_query as $key => $item) {
    foreach ($item as $value) {
        $choice[$key][] = $csv->getQuizAnswerQueryChoice($value['answer_query_id']);
    }
}

//echo '--answer_choice<br>';
#fb($choice);

$choice_text = array();
foreach ($choice as $key => $value) {
    foreach ($value as $i => $item) {
        $choice_text[$key][$i] = $item['text'];
    }
}

//fb($choice_text);

//echo "++ join<br>";
// 問題の結合
// 2019/6/03 count関数対策
$query_max = 0;
if(is_countable($query_data)){
  $query_max = count($query_data);
}
//$query_max = count($query_data);

// 質問
for ($i = 0; $query_max > $i; $i++) {
    $query_data[$i] .= sprintf('%s,', $select_text[$i]);
    //$query_data[$i] .= $select_text[$i];
}

// 正解
for ($i = 0; $query_max > $i; $i++) {
    $query_data[$i] .= sprintf('%s,', $correct_text[$i]);
    //$query_data[$i] .= $correct_text[$i];
}

// あなたの解答
$choice_data = array();
foreach ($choice_text as $key => $value) {
    foreach ($value as $i => $item) {
        $choice_data[$key][$i] = $query_data[$i];
        $choice_data[$key][$i] .= sprintf('%s,', $item);
    }
}

//fb($choice_data);

// 正誤
$correct_data = array();
foreach ($flgright as $key => $value) {
    foreach ($value as $i => $item) {
        $correct_data[$key][$i] = $choice_data[$key][$i];
        $correct_data[$key][$i] .= sprintf('%s,', $item);
        //$correct_data[$key][$i] .= $item;

        $correct_data[$key][$i] = rtrim( $correct_data[$key][$i], "," );
    }
}

//fb($correct_data);

$new_correct_data = [];

for ( $cl = 0; $cl < count( $correct_data ); $cl++ ) {
  for ( $cls = 0; $cls < count( $correct_data[ $cl ] ); $cls++ ) {
    $new_correct_data[ $cl ][] = explode( ',', $correct_data[ $cl ][ $cls ] );
  }
}

//fb($new_correct_data);

$new_correct_data_2 = [];

for ( $fl = 0; $fl < count($new_correct_data ); $fl++ ) {
  for( $fls = 0; $fls < count( $new_correct_data[ $fl ] ); $fls++ ) {
    for( $cls = 0; $cls < count( $new_correct_data[ $fl ][ $fls ] ); $cls++ ) {
      $new_correct_data_2[ $fl ][] = $new_correct_data[ $fl ][ $fls ][ $cls ];
    }

  }
}

//fb($new_correct_data_2);

$test = "test";

foreach ( $new_correct_data_2 as $key => $data1 ) {
  foreach ( $data1 as $key2 => $data2 ) {
    if ( $data2 == "" ) {
      //fb($test);
      $new_correct_data_2[ $key ][ $key2 ] = '-';
    }
  }
}

//fb($new_correct_data_2);

$join_data = array();
foreach ($correct_data as $key => $value) {
    foreach ($value as $item) {
        $join_data[$key] .= $item;
    }
}

//fb($join_data);
//fb($answer_student);
// 2019/6/03 count関数対策
$max_query = 0;
if(is_countable($query)){
  $max_query = count($query);
}
//$max_query = count($query);
for ($i = 0; $max_query >= $i; $i++) {
    $answer_student[$i] .= $join_data[$i];
}

//fb($answer_student);

$title = array(
        "Serial number",
        "Test title",
        "Release date",
        "End date",
        "Test description",
        "How many times you can take an exam",
        "Passing score",
        "The number of questions",
        "Standard deviation",
        "Average score",
        "Overall accuracy rate(%)",
        "Average response time",
        "Examination date",
        "Student ID",
        "Name",
        "Answer time",
        "Score",
        "Correct answer rate(%)",
        "Pass or fail",
        "Rank",
        "Deviation value",
        "Number of exams",
        );

//mb_convert_variables ("SJIS", "UTF-8", $title);

$title_csv = '"';
$title_csv .= implode('","', $title);
$title_csv .= '",';

// 問題のタイトルを整形
// 2019/6/03 count関数対策
$query_count = 0;
if(is_countable($answer)){
  $query_count = count($answer);
}
//$query_count = count($answer);
#fb($query_count);

$query_title = array(
      "Problem",
      "Selection format",
      "Commentary",
      "Correct answer rate(%)",
      "Selection item",
      "Correct answer",
      "Your answer",
      "Judgment",
      );

$query_csv = '"';
$query_csv .= implode('","', $query_title);
$query_csv .= '",';
$query_csv = str_repeat($query_csv, $query_count);
$query_csv = rtrim($query_csv, ",");
$query_csv .= "\n";

//echo $query_csv;

$quiz_csv = $title_csv . $query_csv;

//fb($query_title);

for ( $qc = 0; $qc < $count_query; $qc++ ) {
  for ( $qt = 0; $qt < count( $query_title ); $qt++ ) {
    $title[] = $query_title[ $qt ];
  }

}

$csv_header = [];
$csv_header = $title;

//
//fb($csv_header);

// タイトル
#echo $quiz_csv;

$quiz = $csv->getQuiz();
//fb($quiz);
// 2019/6/03 count関数対策
$ans_count = 0;
if(is_countable($ans)){
  $ans_count = count( $ans );
}
//$ans_count = count( $ans );

//for($i = 0; $max_query >= $i; $i++) {
for($i = 0; $i < $ans_count; $i++) {
    // 2019/6/03 count関数対策
    if(is_countable($query)){
      $csv_data[] = sprintf('%d,%s,%s,%s,%s,%d,%d,%d,%.1f,%.1f,%.1f,%s,',
          $i + 1,                       # 連番
          $quiz['title'],                 # テストタイトル
          $quiz['start_day'],             # 公開日
          $quiz['last_day'],              # 終了日
          $quiz['description'],           # テストの説明
          $quiz['repeat_challenge'],      # 受験できる回数
          $quiz['qualifying_score'],      # 合格点
          count($query),                   # 問題数
          $result->standardDeviation(),   # 標準偏差
          $result->answerAvg(),           # 平均点
          $result->correctRateAll(),      # 全体の正解率(%)
          $result->answerTimeAvg()       # 平均回答時間(分)
      );
    }
    /*
    $csv_data[] = sprintf('%d,%s,%s,%s,%s,%d,%d,%d,%.1f,%.1f,%.1f,%s,',
        $i + 1,                       # 連番305
        $quiz['title'],                 # テストタイトル
        $quiz['start_day'],             # 公開日
        $quiz['last_day'],              # 終了日
        $quiz['description'],           # テストの説明
        $quiz['repeat_challenge'],      # 受験できる回数
        $quiz['qualifying_score'],      # 合格点
        count($query),                   # 問題数
        $result->standardDeviation(),   # 標準偏差
        $result->answerAvg(),           # 平均点
        $result->correctRateAll(),      # 全体の正解率(%)
        $result->answerTimeAvg()       # 平均回答時間(分)
    );
    */
}

//fb($csv_data);

$csv_data2 = [];

foreach ( $csv_data as $key => $value) {
  $csv_data2[ $key ] = rtrim( $csv_data[ $key ], "," );
  $csv_data2[ $key ] = explode( ',', $csv_data2[ $key ] );

}

//fb($csv_data2);

for($i = 0; $max_query >= $i; $i++) {
    $csv_data[$i] .= rtrim ($answer_student[$i], ',');
    $csv_data[$i] .= "\n";
}

/*
for($i = 0; $max_query >= $i; $i++) {
    //echo $csv_data[$i] . "\n";
}
*/
/*
fb($csv_data2);
fb($ans);
fb($new_correct_data_2);
*/

for ( $cs = 0; $cs < count( $csv_data2 ); $cs++ ) {
  for ( $as = 0; $as < count( $ans[ $cs ]); $as++ ) {
    $csv_data2[ $cs ][] = $ans[ $cs ][ $as ];
  }

  for ( $ns = 0; $ns < count( $new_correct_data_2[ $cs ] ); $ns++ ) {
    $csv_data2[ $cs ][] = $new_correct_data_2[ $cs ][ $ns ];
  }

  //$csv_data2[ $cs ][] = $new_correct_data_2[ $cs ][ $as ];
}

$csv_data = [];
$csv_data[ 'csv_header' ] = $csv_header;
$csv_data[ 'csv_student_data' ] = $csv_data2;

//fb($csv_data);

echo json_encode( $csv_data );
exit();
