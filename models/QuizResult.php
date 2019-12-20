<?php

class QuizResult
{
    protected $quiz_id;
    protected $student_id;
    protected $Quiz;
    protected $Query;
    protected $Selection;
    protected $Answer;
    protected $Choice;
    protected $Admin;

    public function __construct($quiz_id, $Curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Quiz = new Quiz($quiz_id, $Curl);
        $this->Query = new Query($quiz_id, $Curl);
        $this->Selection = new QuerySelection($quiz_id, $Curl);
        $this->Answer = new QuizAnswer($quiz_id, $Curl);
        $this->Choice = new QuizAnswerChoice($quiz_id, $Curl);
    }

    public function setStudentId($student_id)
    {
        $this->student_id = $student_id;
    }

    public function setAdmin(Admin $Admin)
    {
        $this->Admin = $Admin;
    }

    /**
     * アンサーIDのセット
     *
     */
    public function setAnswerId($answer_id)
    {
        $this->Answer->setAnswerId($answer_id);
    }

    /**
     * アンサーIDのゲット
     *
     */
    public function getAnswerId()
    {
        return $this->Answer->getAnswerId();
    }

    /**
     * 同一クイズ番号でのstudent_idのRanking
     *
     * @return array
     */
    public function answerRank()
    {
        return $this->Answer->findQuizAnswerRank();
    }

    /**
     * クイズの基本データを得る
     *
     * @retrn array
     */
    public function getQuiz()
    {
        return $this->Quiz->getQuiz();
    }

    /**
     * answer_idのテストの合計点
     *
     * @param $asnwer_id
     * @return array
     */
    public function totalScoreAnswerId()
    {
        $data = $this->Answer->findQuizAnswer();

        return $data['total_score'];
    }

    /**
     * answer_idのテストの合計点
     *
     * @param $asnwer_id
     * @return array
     */
    public function findTotalScore()
    {
        return $this->Answer->findTotalScore();
    }

    /**
     * 合格点
     *
     * @return string qualifying_score
     */
    public function qualifyingScore()
    {
        $quiz = $this->Quiz->getQuiz();

        if ($quiz['qualifying_score'] > 0) {
            return sprintf('%spoint', $quiz['qualifying_score']);
        }

        return '―';
    }

    /**
     * 回答経過時間
     *
     * @return answer_time
     */
    public function answerTime()
    {
        $data = $this->Answer->findQuizAnswer();

        if (! $data) return;

        $date = new DateTime('@'. $data['answer_time'], new DateTimeZone('Asia/Tokyo'));

        return $date->format('H:i:s');
    }

    /**
     * 制限時間
     *
     * @return strings limitTime 単位は分
     */
     /*
    public function limitTime()
    {
        $data = $this->Quiz->getQuiz();

        $minute = $data['limit_time'];

        return $minute;
    }
    */

    public function limitTime()
    {
        $data = $this->Quiz->getQuiz();
        $minute = $data['limit_time'];
        $seconds = round($minute * 60, 2);

        return $this->Answer->timeFormat($seconds);
    }

    /**
     * Quiz_idのテストの合計点
     *
     * @return array
     */
    public function sumTotalScore()
    {
        return $this->Answer->sumTotalScore();
    }


    /**
     * 受験者数(重複なし)
     *
     * @return string
     */
    public function examineesNumberDist()
    {
        $data = $this->Answer->findQuizAnswerDistinct();

        return $data;
    }

    /**
     * 受験者数
     *
     * @return string
     */
    public function examineesNumber()
    {
        $data = $this->Answer->findQuizAnswerCount();

        return $data['answer_rows'];
    }

    /**
     * 得点の平均値
     *
     * @return integer avg
     */
    public function answerAvg()
    {
        $data = $this->Answer->findQuizAnswerAverage();

        return sprintf('%.1f', round($data['avg'], 1));
    }

    /**
     * 正解率
     *
     * @return array
     */
    public function correctRate()
    {
        return round($this->Answer->correctAnswerRate(), 1);
    }

    /**
     * 全体の正解率
     *
     * @return array
     */
    public function correctRateAll()
    {
        $data = $this->Answer->correctSum();
        $correct_sum = $data['correct_sum'];

        $examinee = $this->examineesNumber();

        if ($correct_sum) {
            return round($correct_sum / $examinee , 1);
        }

        return;
    }

    /**
     * 偏差値
     *
     * @return double deviation
     */
    public function deviation()
    {
        # 偏差値＝50＋(自分の得点－平均点) ÷  2
        # https://tyugaku.net/seiseki/hensati.html

        $total_score = $this->totalScoreAnswerId();
        $deviation = 50 + ($total_score - $this->answerAvg()) / 2;

        return sprintf('%.1f', round($deviation, 1));
    }

    /**
     * 標準偏差
     *
     * @return standard_deviation()
     */
    public function standardDeviation()
    {
        $score_values = $this->Answer->findQuizAnswerScore();
        $avg = $this->answerAvg();

        if (! $avg) {
            return;
        }

        $variance = 0.0;
        foreach ((array) $score_values as $value) {
            $variance += pow($value['total_score'] - $avg, 2);
        }

        // 標本抽出の場合は count($values) ⇒ count($values) - 1
        if ($variance > 0) {
            // 2019/6/03 count関数対策
            if(is_countable($score_values)){
              $variance = (float) ($variance / count($score_values));
            }
            //$variance = (float) ($variance / count($score_values));
        }

        return sprintf('%.1f', round((float) sqrt($variance), 1));
    }

    /**
     * 合格者数
     *
     * @return array
     */
    public function answerSuccessful()
    {
        $quiz_data = $this->Quiz->getQuiz();
        $qualifying_score = $quiz_data['qualifying_score'];

        if (! $qualifying_score) {
            return '―';
        }

        $data = $this->Answer->answerSuccessful($qualifying_score);

        return $data['success_count'];
    }

    /**
     * 経過時間の平均値
     *
     * @return integer avg
     */
    public function answerTimeAvg()
    {
        $data = $this->Answer->findQuizAnswerTimeAverage();

        if (! $data['avg']) {
            return;
        }

        return $this->Answer->timeFormat($data['avg']);

        # $date = new DateTime('@'. $data['avg'], new DateTimeZone('Asia/Tokyo'));
        # return $date->format('H:i:s');
    }

    public function findQuizAnswerMaxScore()
    {
        return $this->Answer->findQuizAnswerMaxScore();
    }

    /**
     * クイズ参加者(student)が最初に受けたテストのみ抽出
     *
     * @return array
     */
    public function findQuizAnswerQuizIdFirst()
    {
        return $this->Answer->findQuizAnswerQuizIdFirst();
    }

    /**
     * 出題数の取得
     *
     * @return integer 問題数
     */
    public function countQuery()
    {
        return $this->Query->countQuery();
    }

    /**
     * 問題のタイトルと選択肢
     *
     * @return string
     */
    public function getQueryOfSelection()
    {
        /*
        foreach ((array) $this->getQuery() as $key => $value) {
            $data[$key]['query'] = $value;
            $data[$key]['selection'] = $this->getSelection($value['query_id']);
        }
        */

        return $this->Query->getQuerySelection();
    }

    /**
     * 問題の抽出
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->Query->getQuery();
    }

    /**
     * 選択肢
     *
     * @return array
     */
    public function getSelection()
    {
        #return $this->Query->getSelection($query_id);
        foreach ((array) $this->Selection->getSelectionQuizId() as $key => $value) {
            $data[$key] = $value;
            $temp = $this->selectionAnswerCount($value['selection_id']);
            $data[$key]['count'] = $temp['answer_count'];
        }

        return $data;
    }

    /**
     * 選択肢
     *
     * @return array
     */
    public function getSelectionQuizId()
    {
        return $this->Selection->getSelectionQuizId();
        #return $this->Query->getSelection($query_id);
    }

    /**
     * 選択した回答数
     *
     * @return array
     */
    public function selectionAnswerCount($selection_id)
    {
        return $this->Selection->answerCount($selection_id);
    }

    /**
     * 無回答数
     *
     * @return array
     */
    public function sumNoAnswer()
    {
        return $this->Choice->sumNoAnswer();
    }

    /**
     * 問題毎の全体の正解率
     *
     * @return array
     */
    public function correctRateQuery()
    {
        # answer_idを取得
        $answer_data = $this->Answer->findQuizAnswerQuizId();

        # カウント数
        // 2019/6/03 count関数対策
        $answer_count = 0;
        if(is_countable($answer_data)){
          $answer_count = count($answer_data);
        }
        //$answer_count = count($answer_data);

        $rate = array();

        # flg_rightとanswer_idの正解数を抽出
        $query_data = $this->Query->getQuery();

        foreach ($query_data as $key => $value) {
            $corrct = $this->Answer->findFlgRightQuery($value['query_id']);

            if ($corrct['cnt'] > 0 && $answer_count > 0) {
                $rate[$key]['query_rate'] = round($corrct['cnt'] / $answer_count * 100, 1);
            }
        }

        return $rate;
    }

    /**
     * クイズに参加者した学生
     *
     * @return array
     */
    public function findQuizAnswerStudent()
    {
        return $this->Answer->findQuizAnswerStudent($this->student_id);
    }

    /**
     * 回答形式
     *
     * @return string
     */
    public function queryType()
    {
        return $this->Query->queryTypeAll();
    }

    /**
     * 回答
     *
     * @return array
     */
    public function answerQuery()
    {
        return $this->Answer->findAnswerQueryAll();
    }

    /**
     * 選択した解答群
     *
     * @return array
     */
    public function queryChoice($query_id)
    {
        $data['answer_id'] = $this->getAnswerId();
        $data['query_id'] = $query_id;

        return $this->Choice->findAnswerQueryChoice($data);
    }

    /**
     * quiz_idのすべての問題の取得
     *
     * @return array 問題
     */
    public function findQueryId($query_id)
    {
        return $this->Query->showQueryId($query_id);
    }

    /**
     * quiz_idのすべての問題の取得
     *
     * @return array 問題
     */
    public function findQueryQuizId()
    {
        return $this->Query->showQueryQuizId();
    }

    public function findAnswerChoice($data)
    {
        return $this->Choice->findAnswerChoice($data);
    }

    /**
     * 合否
     *
     * @return string
     */
    public function isSuccess($total_score)
    {
        $quiz = $this->getQuiz();

        if ($quiz['qualifying_score'] == 0  && $quiz['success_flg'] == 0) {
            return '―';
        }

        if ($quiz['qualifying_score'] <= $total_score) {
            return 'Pass';
        }

        return 'Failure';
    }

    /**
     * query_id毎の選択肢の取得
     *
     * @return array 選択肢
     */
    public function getSelectionQueryId()
    {
        $query = $this->getQuery();

        foreach ((array) $query as $key => $value) {
            $selection[$key] = $this->Selection->getSelection($value['query_id']);
        }

        return $selection;
    }

    /**
     * 選択した解答群
     *
     * @param answer_id, query_id
     * @return array
     */
    public function choice($answer_id, $query_id)
    {
        $data['answer_id'] = $answer_id;
        $data['query_id'] = $query_id;

        return $this->Choice->findAnswerChoice($data);
    }
}
