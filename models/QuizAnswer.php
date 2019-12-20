<?php

/**
 * QuizAnswer
 *
 * @package
 * @version $id$
 * @copyright 2008-2018 KJS
 * @author Shinichiro Kondo <evah6809@gmail.com>
 * @license PHP Version 3.0 {@link https://www.php.net/license/3_0.txt}
 */
class QuizAnswer
{
    protected $quiz_id;
    protected $answer_id;
    protected $Curl;
    protected $answer_data;
    protected $Student;
    protected $limit_time;

    public function __construct($quiz_id, $Curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Curl = $Curl;

        #$this->setQuizAnswer();
    }

    /**
     * 最終のID
     *
     * @return max
     *
    public function maxRow()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'maxQuizAnswerId',
            'params' => array()
        );

        return $this->Curl->send($data);
    }
    */

    /**
     * setAnswerId
     *
     * @param mixed $answer_id
     * @access public
     * @return void
     */
    public function setAnswerId($answer_id)
    {
        $this->answer_id = $answer_id;
    }

    public function setStudent(Student $student)
    {
        $this->Student = $student;
    }

    public function setQuizLimitTime($limit_time)
    {
        $this->limit_time = $limit_time;
    }

    /*
    public function setQuiz(Quiz $quiz)
    {
        $this->Quiz = $quiz;
    }
     */

    /*
    public function setSchoolId($SchoolId)
    {
        $this->school_id = $school_id;
    }
    */

    /**
     * getAnswerId
     *
     * @access public
     * @return void
     */
    public function getAnswerId()
    {
        return $this->answer_id;
    }

    /**
     * findTotalScore
     *
     * 得点の合計を一覧表示
     *
     * @access public
     * @return array 得点の合計
     */
    public function findTotalScore()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findTotalScore',
            'params' => array('answer_id' => $this->answer_id)
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 回答の終了時間
     *
     * @return strings
     */
    public function finishTime()
    {
        date_default_timezone_set('Asia/Dhaka');
        $now = new DateTime();

        return $now->format('Y-m-d H:i:s');
    }

    /**
     * 正解フラグ
     *
     * @return array
     */
    public function findFlgRightQueryId($query_id)
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findFlgRightQueryId',
            'params' => array(
                     'answer_id' => $this->answer_id
                   , 'query_id' => $query_id
                )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 正解フラグ
     *
     * @return array
     */
    public function findFlgRightAnswerId()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findFlgRight',
            'params' => array(
                    'answer_id' => $this->answer_id
                )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 正解フラグ
     *
     * @return array
     */
    public function findFlgRight()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findFlgRight',
            'params' => array(
                    'answer_id' => $this->answer_id
                )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 正解フラグ
     * query_idで抽出
     * 配列の順番に注意
     *
     * @return array
     */
    public function findFlgRightQuery($query_id)
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findFlgRightQuery',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * クイズ参加者
     *
     * @return array
     */
    public function findQuizAnswerQuizId()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function findQuizAnswerDistinctStudentId($data)
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerDistinctStudentId',
            'params' => array(
                  'quiz_id' => $data['quiz_id']
                )
        );

        return $this->Curl->send($data);
    }

    public function findQuizAnswerMaxScore($data)
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerMaxScore',
            'params' => array(
                      'quiz_id' => $data['quiz_id']
                    , 'student_id' => $data['student_id']
                )
        );

        return $this->Curl->send($data);
    }

    /**
     * クイズ参加者(student)が最初に受けたテストのみ抽出
     *
     * @return array
     */
    public function findQuizAnswerQuizIdFirst()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerQuizIdFirst',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * quiz_numberの得点
     *
     * @return array
     */
    public function findQuizAnswerScore()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerScore',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * クイズ参加者数(重複なし)
     *
     * @return array
     */
    public function findQuizAnswerDistinct()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerDistinct',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * クイズ参加者数
     *
     * @return array
     */
    public function findQuizAnswerCount()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerCount',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * 参加者の順位
     *
     * @return array
     */
    public function findQuizAnswerRank()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerRank',
            'params' => array(
                  'quiz_id' => $this->quiz_id,
                  'answer_id' => $this->answer_id
            )
        );

        return $this->Curl->send($data);
    }

    public function findQuizAnswerRankStudent($student_id)
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerRankStudent',
            'params' => array(
                  'quiz_id' => $this->quiz_id,
                  'student_id' => $student_id
            )
        );

        return $this->Curl->send($data);

    }
    /**
     * 得点の平均
     *
     * @return integer avg
     */
    public function findQuizAnswerAverage()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerAverage',
            'params' => array(
                  'quiz_id' => $this->quiz_id
            )
        );

        return $this->Curl->send($data);
    }

    /**
     * 経過時間の平均
     *
     * @return limit_time
     */
    public function findQuizAnswerTimeAverage()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerTimeAverage',
            'params' => array(
                  'quiz_id' => $this->quiz_id
            )
        );

        return $this->Curl->send($data);
    }

    public function sumTotalScore()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'sumQuizAnswerTotalScore',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * studentの重複を除いたtotal_score 不完全
     *
     * @return array
     */
    public function distinctTotalScore()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerTotalScore',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * クイズに参加者した学生
     *
     * @return array
     *
    public function findQuizAnswerStudent()
    {
       # $student_id = $_SESSION['auth']['student_id'];

        $student_data = $this->Student->getStudent();

        if (! $student_data) die ('利用者番号が不明です');

        $student_id = $student_data['student_id'];

        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerStudent',
            'params' => array(
                  'quiz_id' => $this->quiz_id
                , 'student_id' => $student_id
            )
        );

        return $this->Curl->send($data);
    }
     */

    public function findQuizAnswerStudent($student_id)
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerStudent',
            'params' => array(
                  'quiz_id' => $this->quiz_id
                , 'student_id' => $student_id
            )
        );

        return $this->Curl->send($data);
    }

    /**
     * tbl_quiz_answer
     *
     * @param  $quiz_answer_id
     * @return quiz_answer_idのレコード
     */
    public function findQuizAnswer()
    {
        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswer',
            'params' => array('answer_id' => $this->answer_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * tbl_quiz_answerのデータをセット
     * 削除予定
     *
     * @return array $this->answer_data
     */
    public function setQuizAnswer()
    {
        $this->answer_data = $this->findQuizAnswer();
    }

    /**
     * tbl_quiz_answer_query
     *
     * @param  $quiz_answer_id
     * @return quiz_answer_idのレコード
     */
    public function findAnswerQueryAll()
    {
        $data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findAnswerQueryAll',
            'params' => array('answer_id' => $this->answer_id)
        );

        return $this->Curl->send($data);

    }

    public function findNoAnswer($query_id)
    {
        $data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findNoAnswer',
            'params' => array(
                  'answer_id' => $this->answer_id
                , 'query_id' => $query_id
            )
        );

        return $this->Curl->send($data);

    }

    public function setNoAnswer($data)
    {
        if ($data['answer_id'] == '') {
            return;
        }

        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'insertQuizAnswerQuery',
            'params' => array(
                  'answer_id' => $data['answer_id']
                , 'query_id' => $data['query_id']
                , 'quiz_id' => $data['quiz_id']
                , 'flg_right' => 0
                , 'flg_no_answer' => 1
            )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 正解率を計算
     *
     * @return 正解率
     */
    public function correctAnswerRate()
    {
        $calc = 0;
        $answer = $this->findFlgRight();
        // 2019/6/03 count関数対策
        $correct_answer = 0;
        if(is_countable($answer)){
          $correct_answer = count($answer);
        }
        //$correct_answer = count($answer);

        // 2019/6/03 count関数対策
        $total_query = 0;
        if(is_countable($this->findAnswerQueryAll())){
          $total_query = count($this->findAnswerQueryAll());
        }
        //$total_query = count($this->findAnswerQueryAll());

        if ($correct_answer > 0) {
            $calc = $correct_answer / $total_query * 100;
        }

        return $calc;
    }

    /**
     * 経過時間
     *
     * @return string
     */
    public function answerTime()
    {
        date_default_timezone_set('Asia/Dhaka');

        $start = new DateTime($_SESSION['student']['start_time']);
        $end = new DateTime();

        $interval = $end->getTimeStamp() - $start->getTimeStamp();

        if ($this->limit_time > 0) {
            $limit_time = $this->limit_time * 60;

            if ($interval > $limit_time) {
                $interval = $limit_time;
            }
        }

        return $interval;

        /*
        $date1 = new DateTime($_SESSION['student']['start_time']);
        $date2 = new DateTime();
        $date2->setTimezone(new DateTimeZone('Asia/Tokyo'));
        $diff = $date1->diff($date2);

        return $diff->s;

        */
        #echo $diff->format('%R %y年 %mヶ月 %d日 %h時間 %i分 %s秒 ズレ');
        #return $diff->format('%s');
    }

    /**
     * answer_dataの取得
     *
     * @return array answer_data
     *
    public function getQueryAnswerData()
    {
        #return $this->answer_data;

        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findAnswerTime',
            'params' => array(
                 'answer_id' => $this->answer_id
            )
        );

        return $this->Curl->send($curl_data);
    }
     */

    /**
     * 合格者数
     *
     * @return array
     */
    public function answerSuccessful($qualifying_score)
    {
        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerSuccessful',
            'params' => array(
                 'quiz_id' => $this->quiz_id
                ,'qualifying_score' => $qualifying_score)
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 全体の正解率
     *
     * @return array 全体の正解率
     */
    public function correctSum()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'findQuizAnswerCorrectSum',
            'params' => array(
                 'quiz_id' => $this->quiz_id
            )
        );

        return $this->Curl->send($curl_data);

        //debug($data);
    }

    /**
     * テストの履歴
     *
     * @return array $testHistory
     */
    public function testHistory()
    {
        return $this->findQuizAnswerStudent();
    }

    /**
     * 得点の更新
     * tbl_quiz_answer
     *
     * @param total 合計点数
     * @return void
     */
    public function updateQuizAnswer()
    {
        $data = $this->findTotalScore();

        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'updateQuizAnswer',
            'params' => array(
                 'answer_id' => $this->answer_id
                ,'register_datetime' => $this->finishTime()
                ,'answer_time' => $this->answerTime()
                ,'total_score' => $data['total_score']
                ,'correct_answer_rate' => $this->correctAnswerRate()
            )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * tbl_quiz_answerのinsert
     *
     * @param $data 保存するデータ
     */
    public function quizAnswerInsert()
    {
       # $student_data['student_id'] = $_SESSION['auth']['student_id'];
       # $student_data['school_id'] = $_SESSION['auth']['school_id'];

        $student_data = $this->Student->findStudentId();
        $student_data['quiz_id'] = $this->quiz_id;

        $curl_data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'quizAnswerInsert',
            'params' => $student_data
        );

        return $this->Curl->send($curl_data);

        #return $this->maxRow();
    }

    public function timeFormat($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor($seconds % 3600 / 60);
        $seconds = $seconds % 60;

        return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
    }
}
