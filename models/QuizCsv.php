<?php

class QuizCsv
{

    private $curl;
    private $quiz_id;

    public function __construct($quiz_id, Curl $curl)
    {
        $this->curl = $curl;
        $this->quiz_id = $quiz_id;
    }

    public function getQuiz()
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetch',
            'sql' => 'SELECT *
                        FROM tbl_quiz
                       WHERE enable = 1
                         AND quiz_id = :quiz_id
                    ORDER BY quiz_id ASC',
            'params' => array(':quiz_id' => $this->quiz_id)
        );

        return $this->curl->send($curl_data);
    }


    public function getQuizAnswer()
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
               'sql' => 'SELECT answer_id
                           , quiz_id
                           , school_id
                           , student_id
                           , register_datetime
                           , answer_time
                           , total_score
                           , correct_answer_rate
                        FROM tbl_quiz_answer a
                       WHERE answer_time > 0
                         AND quiz_id = :quiz_id
                    ORDER BY register_datetime ASC',
            'params' => array(':quiz_id' => $this->quiz_id)
        );

        return $this->curl->send($curl_data);
    }

    public function distinctStudent()
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
               'sql' => 'SELECT distinct student_id
                        FROM tbl_quiz_answer
                       WHERE answer_time > 0
                         AND quiz_id = :quiz_id',
            'params' => array(':quiz_id' => $this->quiz_id)
        );

        return $this->curl->send($curl_data);
    }

    public function getStudent($student_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetch',
               'sql' => 'SELECT student_id
                           , school_id
                           , student_code
                           , student_name
                           , id
                           , bit_subject
                           , enable
                           , display_order
                        FROM tbl_student
                       WHERE student_id = :student_id',
            'params' => array(':student_id' => $student_id)
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizAnswerQuery($answer_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT *
                        FROM tbl_quiz_answer_query
                       WHERE quiz_id = :quiz_id
                        AND answer_id = :answer_id
                    ORDER BY answer_query_id ASC',
            'params' => array(
                ':quiz_id' => $this->quiz_id,
                ':answer_id' => $answer_id,
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizAnswerQueryChoice($answer_query_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetch',
            'sql' => 'SELECT *
                        FROM tbl_quiz_answer_query_choice
                        JOIN tbl_quiz_query_selection USING (selection_id)
                       WHERE answer_query_id = :answer_query_id',
            'params' => array(
                ':answer_query_id' => $answer_query_id,
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizQuery()
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT a.query_id,a.quiz_id,query_text,description,score,type_jp,a.display_order,a.`enable`
                        FROM tbl_quiz_query as a
                        left join mst_query_type as b on a.query_type = b.query_type_id
                        WHERE a.enable = 1
                        AND quiz_id = :quiz_id
                        ORDER BY display_order ASC',
            'params' => array(
                ':quiz_id' => $this->quiz_id
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizQuerySelection($selection_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetch',
            'sql' => 'SELECT *
                        FROM tbl_quiz_query_selection
                       WHERE enable = 1
                         AND selection_id = :selection_id',
            'params' => array(
                ':selection_id' => $selection_id
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizQuerySelectionAll($query_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT *
                        FROM tbl_quiz_query_selection
                       WHERE enable = 1
                         AND query_id = :query_id
                    ORDER BY display_order ASC',
            'params' => array(
                ':query_id' => $query_id
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizQuerySelectionCorrect($query_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT *
                        FROM tbl_quiz_query_selection
                       WHERE enable = 1
                         AND query_id = :query_id
                         AND correct_flg = 1',
            'params' => array(
                ':query_id' => $query_id
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getQuizQueryChoice($answer_query_id)
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT *
                        FROM tbl_quiz_answer_query_choice
                        JOIN tbl_quiz_query_selection USING (selection_id)
                       WHERE answer_query_id = :answer_query_id',
            'params' => array(
                ':answer_query_id' => $answer_query_id
            )
        );

        return $this->curl->send($curl_data);
    }

    public function getMstQueryType()
    {
        $curl_data = array(
            'module' => 'sql',
            'method' => 'fetchAll',
            'sql' => 'SELECT * FROM mst_query_type',
            'params' => array()
        );

        return $this->curl->send($curl_data);
    }

}
