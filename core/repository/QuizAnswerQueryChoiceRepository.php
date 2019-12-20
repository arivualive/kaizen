<?php
require_once '../config.php';

class QuizAnswerQueryChoiceRepository extends PdoBase
{
    public function insertAnswerChoice($data)
    {
        $sql = 'INSERT INTO tbl_quiz_answer_query_choice (
                      answer_query_id
                    , selection_id
                ) VALUES (
                      :answer_query_id
                    , :selection_id
                )';

        return $this->execute($sql,
            array(
                  ':answer_query_id' => $data['answer_query_id']
                , ':selection_id' => $data['selection_id']
            )
        );
    }

    public function deleteAnswerChoice($data)
    {
        $sql = 'DELETE FROM tbl_quiz_answer_query_choice
                 WHERE answer_query_id = :answer_query_id';

        return $this->exec($sql,
            array(
                ':answer_query_id' => $data['answer_query_id']
            )
        );
    }

    public function updateAnswerChoice($data)
    {
        $sql = 'UPDATE tbl_quiz_answer_query_choice
                   SET selection_id = :selection_id
                 WHERE answer_query_id = :answer_query_id';

        return $this->exec($sql,
            array(
                ':selection_id' => $data['selection_id']
                , ':answer_query_id' => $data['answer_query_id']
            )
        );
    }

    public function findAnswerChoice($data)
    {
        $sql = 'SELECT answer_query_id
                     , selection_id
                  FROM tbl_quiz_answer_query_choice
                 WHERE answer_query_id = :answer_query_id';

        return $this->fetchAll($sql,
            array(':answer_query_id' => $data['answer_query_id'])
        );
    }
   
    public function findAnswerQueryChoice($data)
    {
        $sql = 'SELECT answer_query_id
                     , selection_id
                     , answer_id
                     , query_id
                     , flg_right
                     , flg_no_answer
                  FROM tbl_quiz_answer_query
            INNER JOIN tbl_quiz_answer_query_choice USING (answer_query_id)
                 WHERE query_id = :query_id
                   AND answer_id = :answer_id';
    
        return $this->fetchAll($sql, array(
                    ':query_id' => $data['query_id'],
                    ':answer_id' => $data['answer_id']
                    )
                );
    }
}
