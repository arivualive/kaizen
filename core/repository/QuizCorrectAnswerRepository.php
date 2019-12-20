<?php
require_once '../config.php';

class QuizCorrectAnswerRepository extends PdoBase
{

    /**
     * 正解のselection_idを得る
     * @param array $data['query_id']
     * @return array selection_id
     */
    public function findCorrectAnswer($data)
    {
        $sql = 'SELECT selection_id
                     , quiz_id
                     , query_id
                  FROM tbl_quiz_query_selection
                 WHERE query_id = :query_id
                   AND correct_flg = 1';

        return $this->fetchAll($sql, array(':query_id' => $data['query_id']));

    }
    /*
    public function findCorrectAnswer($data)
    {
        $sql = 'SELECT correct_answer_id
                     , selection_id
                     , quiz_id
                     , query_id
                  FROM tbl_quiz_query_correct_answer
                 WHERE query_id = :query_id';

        return $this->fetchAll($sql, array(':query_id' => $data['query_id']));

    }
    */

}
