<?php
require_once '../config.php';

class QuizAnswerQueryRepository extends PdoBase
{
    /*
     * オートインクリメントに変更
     *
    public function maxQuizAnswerQueryId()
    {
        $sql = 'SELECT max(quiz_answer_query_number) as max_row FROM tbl_quiz_answer_query';

        return $this->fetch($sql);
    }
     */

    public function findAnswerQueryAll($data)
    {
        $sql = 'SELECT answer_query_id
                     , answer_id
                     , query_id
                     , quiz_id
                     , flg_right
                     , flg_no_answer
                  FROM tbl_quiz_answer_query
                 WHERE answer_id = :answer_id
              ORDER BY query_id ASC';

        return $this->fetchAll($sql, array(':answer_id' => $data['answer_id']));
    }

    public function findAnswerQuery($data)
    {
        $sql = 'SELECT answer_query_id
                     , answer_id
                     , query_id
                     , flg_right
                     , flg_no_answer
                  FROM tbl_quiz_answer_query
                 WHERE query_id = :query_id
                   AND answer_id = :answer_id';

        return $this->fetch($sql,
                array(
                      ':query_id' => $data['query_id']
                    , ':answer_id' => $data['answer_id']
                    )
                );
    }

    public function findAnswerQueryId($data)
    {
        $sql = 'SELECT answer_query_id
                  FROM tbl_quiz_answer_query
                 WHERE query_id = :query_id
                   AND answer_id = :answer_id';

        return $this->fetch($sql,
                array(
                      ':query_id' => $data['query_id']
                    , ':answer_id' => $data['answer_id']
                    )
                );
    }

    /**
     * 正解のレコードを得る
     *
     * @return array
     */
    public function findFlgRightQueryId($data)
    {
        $sql = 'SELECT answer_id
                     , query_id
                     , quiz_id
                     , flg_right
                  FROM tbl_quiz_answer_query
                 WHERE answer_id = :answer_id
                   AND query_id = :query_id';

        return $this->fetch($sql,
            array(
                 ':answer_id' => $data['answer_id']
               , ':query_id' => $data['query_id']
            )
        );
    }

    /**
     * 正解のレコードを得る
     *
     * @return array
     */
    public function findFlgRight($data)
    {
        $sql = 'SELECT *
                  FROM tbl_quiz_answer_query
                 WHERE answer_id = :answer_id
                   AND flg_right = 1';

        return $this->fetchAll($sql, array(
                ':answer_id' => $data['answer_id']
                )
        );
    }

    /**
     * findFlgRightQuery
     * 問題の正解数を得る
     *
     * @param mixed $data (quiz_id, query_id)
     * @access public
     * @return array
     */
    public function findFlgRightQuery($data)
    {
        $sql = 'SELECT count(flg_right) as cnt
                  FROM tbl_quiz_answer_query
                 WHERE query_id = :query_id
                   AND flg_right = 1';

        return $this->fetch($sql, array(':query_id' => $data['query_id']));
    }

    /**
     * quiz_id毎の回答数
     *
     * @return array
     */
    public function sumAnswer($data)
    {
        $sql = 'SELECT query_id, count(*) as answer_count
                     FROM tbl_quiz_answer_query
                   WHERE quiz_id = :quiz_id
               GROUP BY query_id
               ORDER BY query_id asc';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    /**
     * query_idの無回答
     *
     * @return array
     */
    public function findNoAnswer($data)
    {
        $sql = 'SELECT query_id, flg_no_answer
                     FROM tbl_quiz_answer_query
                   WHERE answer_id = :answer_id
                     AND query_id = :query_id';

        return $this->fetch($sql,
            array(
                 ':answer_id' => $data['answer_id']
               , ':query_id' => $data['query_id']
            )
        );
    }

    /**
     * quiz_id毎の無回答の合計数
     *
     * @return array
     */
    public function sumNoAnswer($data)
    {
        $sql = 'SELECT query_id
                     , sum(flg_no_answer) as no_answer_sum
                  FROM tbl_quiz_answer_query a
                  JOIN tbl_quiz_answer b USING (answer_id)
                 WHERE a.quiz_id = :quiz_id
                   AND answer_time > 0
              GROUP BY query_id
              ORDER BY query_id asc';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function insertQuizAnswerQuery($data)
    {
        if ($data['flg_right'] == '') {
            $data['flg_right'] = 0;
        }

        $sql = 'INSERT INTO tbl_quiz_answer_query (
                      answer_id
                    , query_id
                    , quiz_id
                    , flg_right
                    , flg_no_answer
                ) VALUES (
                      :answer_id
                    , :query_id
                    , :quiz_id
                    , :flg_right
                    , :flg_no_answer
                )';

        $this->execute($sql,
            array(
                  ':answer_id' => $data['answer_id']
                , ':query_id' => $data['query_id']
                , ':quiz_id' => $data['quiz_id']
                , ':flg_right' => $data['flg_right']
                , ':flg_no_answer' => $data['flg_no_answer']
            )
        );

        return $this->lastInsertId();
    }

    public function updateQuizAnswerQuery($data)
    {
        if ($data['flg_right'] == '') {
            $data['flg_right'] = 0;
        }

        $sql = 'UPDATE tbl_quiz_answer_query
                   SET flg_right = :flg_right
                     , flg_no_answer = :flg_no_answer
                 WHERE answer_query_id = :answer_query_id';

        return $this->execute($sql,
                 array(
                      ':flg_right' => $data['flg_right']
                    , ':flg_no_answer' => $data['flg_no_answer']
                    , ':answer_query_id' => $data['answer_query_id']
                 )
        );
    }
}
