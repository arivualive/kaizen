<?php

class QuizAnswerChoice
{
    protected $quiz_id;
    protected $answer_id;
    protected $curl;

    public function __construct($quiz_id, $curl)
    {
        $this->quiz_id = $quiz_id;
        $this->curl = $curl;
    }

    /**
     * Answer Numberの取得
     *
     * @return quiz_answer_id
     */
    public function getAnswerId()
    {
        return $this->answer_id;
    }

    public function setAnswerId($answer_id)
    {
        $this->answer_id = $answer_id;
    }

    /**
     * 回答した問題
     *
     * @param quiz_query_id
     * @param quiz_answer_id
     *
     * @return array
     */
    public function findAnswerQuery($data)
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findAnswerQuery',
            'params' => array(
                  'query_id' => $data['query_id']
                , 'answer_id' => $this->answer_id
            )
        );

        return $this->curl->send($curl_data);
    }

    /**
     * 回答した選択肢
     *
     * @param integer $answer_query_id 回答した問題のプライマリキー
     * @return array
     */
    public function findAnswerChoice($data)
    {
        $result = $this->findAnswerQuery($data);

        $curl_data = array(
            'repository' => 'QuizAnswerQueryChoiceRepository',
            'method' => 'findAnswerChoice',
            'params' => array('answer_query_id' => $result['answer_query_id'])
        );

        return $this->curl->send($curl_data);

    }

    /**
     * 回答した選択肢
     *
     * @param integer $answer_query_id 回答した問題のプライマリキー
     * @return array
     */
    public function findAnswerQueryChoice($data)
    {
       # //debug($data);
        $curl_data = array(
            'repository' => 'QuizAnswerQueryChoiceRepository',
            'method' => 'findAnswerQueryChoice',
            'params' => array(
                'answer_id' => $data['answer_id'],
                'query_id' => $data['query_id']
                )
        );

        return $this->curl->send($curl_data);

    }
    /**
     * クイズ番号毎の無回答数
     *
     * @return array
     */
    public function sumNoAnswer()
    {
        $curl_data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'sumNoAnswer',
            'params' => array(
                'quiz_id' => $this->quiz_id
            )
        );

        return $this->curl->send($curl_data);
    }
}
