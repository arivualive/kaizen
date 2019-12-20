<?php
/**
 * クイズの答を保存するクラス
 *
 */
class QuizAnswerSave
{
    protected $quiz_id;
    protected $query_id;
    protected $answer_id;
    protected $answer_query_id;
    protected $selection_id;
    protected $Curl;

    public function __construct($quiz_id, Curl $curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Curl = $curl;
    }

    /**
     * 質問番号
     *
     * @params $_POST['quiz_query_id']
     */
    public function setQueryId($query_id)
    {
        $this->query_id = $query_id;
    }

    /**
     * 回答番号
     *
     * @params $_POST['quiz_answer_id']
     */
    public function setAnswerId($answer_id)
    {
        $this->answer_id = $answer_id;
    }

    /**
     * 回答番号
     *
     * @params $_POST['quiz_answer_id']
     */
    public function setAnswerQueryId($answer_query_id)
    {
        $this->answer_query_id = $answer_query_id;
    }

    /**
     * 回答選択肢番号
     *
     * @params $_POST['selection_id']
     */
    public function setSelectionId($selection_id)
    {
        $this->selection_id = $selection_id;
    }

    /**
     * tbl_quiz_answerの最大ID
     * オートインクリメントに変更の為、不使用
     *
     * @return max_row 最大ID
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
     * Query id のデータ
     *
     * @return array tbl_quiz_queryのデータ
     */
    public function getQueryId()
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryId',
            'params' => array('quiz_query_id' => $this->query_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * 既に回答した問題があるか？
     *
     * @return array | boolean 回答データ
     */
    public function findQuizAnswerId()
    {
        $data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findAnswerQueryId',
            'params' => array(
                 'query_id' => $this->query_id
               , 'answer_id' => $this->answer_id
            )
        );

        return $this->Curl->send($data);
    }

    /**
     * 既に回答した問題があるか？
     *
     * @return array | boolean 回答データ
     *
    public function isAnswer()
    {
        return $this->findQuizAnswerId();
    }
    */

    /**
     * insert or update
     * 回答の保存
     *
     * @return void
     */
    public function saveAnswer()
    {
        $result = $this->findQuizAnswerId();
        //debug($result);

        if ($result) {
            $this->setAnswerQueryId($result['answer_query_id']);
            $this->updateAnswerQuery();
        }

        $this->deleteAnswerChoice();
        $this->insertAnswerChoice();
    }

    /**
     * insert or update
     * 回答の保存 削除予定
     * クイズスタートの時にinsertしておくのでupdateのみ
     *
     * @return void
     *
    public function saveAnswer()
    {
        $result = $this->findQuizAnswerId();
        //debug($result);

        if ($result) {
            $this->setAnswerQueryId($result['answer_query_id']);
            $this->updateAnswerQuery();
        } else {
            $lastId = $this->insertAnswerQuery();
            if (! $lastId) die("DB Insert Error. " . __METHOD__) ;
            $this->setAnswerQueryId($lastId);
        }

        $this->deleteAnswerChoice();
        $this->insertAnswerChoice();
    }
     */

    /**
     * tbl_quiz_answerのinsert
     *  QuizAnswer.php に移動
     * @param $data 保存するデータ
     *
    public function quizAnswerInsert()
    {
        $student_data['quiz_id'] = $this->quiz_id;
        $student_data['student_id'] = $_SESSION['auth']['student_id'];
        $student_data['school_id'] = $_SESSION['auth']['school_id'];

        $data = array(
            'repository' => 'QuizAnswerRepository',
            'method' => 'quizAnswerInsert',
            'params' => $student_data
        );

        return $this->Curl->send($data);

        #return $this->maxRow();
    }
    */

    /**
     * クイズの答
     *
     * @return array
     */
    public function findCorrectAnswer()
    {
        $data = array(
            'repository' => 'QuizCorrectAnswerRepository',
            'method' => 'findCorrectAnswer',
            'params' => array('query_id' => $this->query_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * 正解の判定フラグ
     *
     * @return integer 1: 正解 0: 不正解
     */
    public function correctRight()
    {
        $flg_right = array();

        $correct = $this->findCorrectAnswer();
        // 2019/6/03 count関数対策
        $cnt_correct = 0;
        if(is_countable($correct)){
          $cnt_correct = count($correct);
        }
        //$cnt_correct = count($correct);

        // 2019/6/03 count関数対策
        $cnt_post = 0;
        if(is_countable($this->selection_id)){
          $cnt_post = count($this->selection_id);
        }
        //$cnt_post = count($this->selection_id);

        if ($cnt_correct != $cnt_post) {
            $flg_right = 0;
            return $flg_right;
        }

        foreach ($this->selection_id as $key => $value) {
            foreach ($correct as $item) {
                if ($item['selection_id'] == $value) {
                    $flg_right[] = 1;
                } else {
                    $flg_right[] = 0;
                }
            }
        }

        $temp = 0;
        foreach ($flg_right as $flg) {
            $temp += $flg;
            $flg_right = $temp;
        }

        if ($flg_right == $cnt_correct) {
            $flg_right = 1;
        } else {
            $flg_right = 0;
        }

        return $flg_right;
    }

    /**
     * 無回答フラグ
     *
     * @return integer 1: 無回答
     */
    public function noAnswerFlg()
    {
        $no_answer_flg = 0;

        if (! isset($this->selection_id)) {
            $no_answer_flg = 1;
        }

        return $no_answer_flg;
    }

    /**
     * 回答の更新
     *
     * @return void
     */
    public function updateAnswerQuery()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'updateQuizAnswerQuery',
            'params' => array(
                  'answer_query_id' => $this->answer_query_id
                , 'flg_right' => $this->correctRight()
                , 'flg_no_answer' => $this->noAnswerFlg()
            )
        );

        return $this->Curl->send($curl_data);
    }

    /**
     * 選択肢の更新
     *
     * @param array $data
     */
    public function updateAnswerChoice($data)
    {
        $post_data = $this->postData();

        if (! isset($data['answer_query_id']) || empty($data['answer_query_id'])) {
            return;
        }

        $curl_data = array(
            'repository' => 'QuizAnswerQueryChoiceRepository',
            'method' => 'updateAnswerChoice',
            'params' => array(
                'answer_query_id' => $data['answer_query_id']
                , 'selection_id' => $this->selection_id
            )
        );

        return $this->Curl->send($curl_data);
        #return $curl_data;
    }

    /**
     * 回答の保存
     *   insertAnswerChoice()のプライマリキー
     *
     * @return void
     * @link insertAnswerChoice()
     */
    public function insertAnswerQuery()
    {
        $data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'insertQuizAnswerQuery',
            'params' => array(
                  'answer_id' => $this->answer_id
                , 'query_id' => $this->query_id
                , 'quiz_id' => $this->quiz_id
                , 'flg_right' => $this->correctRight()
                , 'flg_no_answer' => $this->noAnswerFlg()
            )
        );

        return $this->Curl->send($data);
    }

    /**
     * 選択肢の更新(削除)
     *
     * @param array $data
     */
    public function deleteAnswerChoice()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryChoiceRepository',
            'method' => 'deleteAnswerChoice',
            'params' => array(
                'answer_query_id' => $this->answer_query_id
            )
        );

        return $this->Curl->send($curl_data);
        //debug($curl_data);
    }

    /**
     * 選択肢の保存
     *
     * @param array $data
     */
    public function insertAnswerChoice()
    {
        if ( ! $this->selection_id) {
            return;
        }

        foreach ((array) $this->selection_id as $value) {
            $data = array(
                'repository' => 'QuizAnswerQueryChoiceRepository',
                'method' => 'insertAnswerChoice',
                'params' => array(
                    'answer_query_id' => $this->answer_query_id,
                    'selection_id' => $value
                )
            );

            //debug($data);
            $this->Curl->send($data);
        }
    }

}
