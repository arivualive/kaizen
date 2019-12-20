<?php
require_once '../config.php';

class QuizAnswerRepository extends PdoBase
{
    /*
    public function findQueryId($data)
    {
        $sql = 'SELECT query_id
                            , quiz_id
                            , query_text
                            , description
                            , score
                            , image_file_name
                            , sound_file_name
                            , query_type
                            , display_order
                  FROM tbl_quiz_query
                 WHERE quiz_query_number = :quiz_query_id
                   AND enable = 1';

        return $this->fetch($sql, array(':quiz_query_id' => $data['quiz_query_id']));
    }
*/

    public function findQuizAnswer($data)
    {
        $sql = 'SELECT answer_id
                            , quiz_id
                            , school_id
                            , student_id
                            , register_datetime
                            , answer_time
                            , total_score
                            , correct_answer_rate
                  FROM tbl_quiz_answer
                 WHERE answer_id = :answer_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(':answer_id' => $data['answer_id']));
    }

    public function findQuizAnswerQuizId($data)
    {
        $sql = 'SELECT answer_id
                     , quiz_id
                     , school_id
                     , student_id
                     , register_datetime
                     , answer_time
                     , total_score
                     , correct_answer_rate
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerDistinctStudentId($data)
    {
        $sql = 'SELECT distinct student_id
                     , quiz_id
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerMaxScore($data)
    {
        $sql = 'SELECT answer_id
                     , quiz_id
                     , school_id
                     , student_id
                     , register_datetime
                     , answer_time
                     , total_score
                     , correct_answer_rate
                  FROM tbl_quiz_answer
                 WHERE student_id = :student_id
                   AND quiz_id = :quiz_id
              ORDER BY total_score DESC
                     , register_datetime DESC
                       LIMIT 1';

        return $this->fetch($sql, array(
                         ':student_id' => $data['student_id']
                       , ':quiz_id' => $data['quiz_id']
                    )
                );
    }

/*
    public function findQuizAnswerMaxScore($data)
    {
        $sql = 'SELECT a.answer_id
                     , a.quiz_id
                     , a.student_id
                     , a.register_datetime
                     , a.answer_time
                     , a.total_score
                     , a.correct_answer_rate
                 FROM tbl_quiz_answer as a
           INNER JOIN (
                        SELECT
                          student_id
                        , max(total_score) as max_score
                        FROM tbl_quiz_answer
               GROUP BY student_id) AS b
                   ON a.student_id = b.student_id
                  AND a.total_score = b.max_score
                  AND a.quiz_id = :quiz_id
--             ORDER BY register_datetime DESC
--                LIMIT 1
';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }
*/
    public function findQuizAnswerQuizIdFirst($data)
    {
        $sql = 'SELECT answer_id,
                quiz_id,
                student_id,
                count(student_id) as cnt,
                correct_answer_rate,
                total_score,
                register_datetime,
                answer_time,
                    (SELECT COUNT(*)+1 FROM tbl_quiz_answer AS t2
                     WHERE t2.total_score > t1.total_score
                     AND quiz_id = :quiz_id
                     AND answer_time > 0
                    ) AS rank
                FROM tbl_quiz_answer AS t1
                WHERE quiz_id = :quiz_id
                  AND answer_time > 0
                GROUP BY student_id
                ORDER BY rank ASC, register_datetime ASC';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerScore($data)
    {
        $sql = 'SELECT total_score
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerCorrectSum($data)
    {
        $sql = 'SELECT sum(correct_answer_rate) as correct_sum
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerSuccessful($data)
    {
        $sql = 'SELECT count(answer_id) as success_count
                  FROM tbl_quiz_answer
                 WHERE total_score >= :qualifying_score
                   AND quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(
              ':quiz_id' => $data['quiz_id']
            , ':qualifying_score' => $data['qualifying_score']
            )
        );
    }

    public function findQuizAnswerDistinct($data)
    {
        $sql = 'SELECT count(DISTINCT student_id) as student_count
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';
        /*
        $sql = 'SELECT count(student_id) as answer_rows
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';
         */

        return $this->fetch($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQuizAnswerCount($data)
    {
        /*
        $sql = 'SELECT count(DISTINCT student_number) as student_count
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id';
         */
        $sql = 'SELECT count(student_id) as answer_rows
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(':quiz_id' => $data['quiz_id']));
    }

    /**
     * 合計を算出
     *
     * @param unknown $data
     * @return mixed
     */
    public function sumQuizAnswerTotalScore($data)
    {
        $sql = 'SELECT sum(total_score) as sum_score
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(':quiz_id' => $data['quiz_id']));
    }

    /**
     * 合計を抽出
     * sumQuizAnswerTotalScoreに切り替る
     *
     * @param unknown $data
     * @return array
     *
    public function findQuizAnswerTotalScore($data)
    {
        $sql = 'SELECT DISTINCT total_score
                  FROM quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0
              ORDER BY total_score DESC';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }
    */

/*
    public function findQuizAnswerRanking($data)
    {
        $sql = 'SELECT quiz_id
                     , answer_id
                     , student_id
                     , register_datetime
                     , total_score
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_id = :answer_id
              ORDER BY total_score DESC';

        return $this->fetchAll($sql,
            array(
                  ':quiz_id' => $data['quiz_id']
                , ':answer_id' => $data['answer_id']
            )
        );
    }
    */

    public function findQuizAnswerRank($data)
    {
        $sql = 'SELECT quiz_id,
                       answer_id,
                       student_id,
                       total_score,
                        (SELECT COUNT(*)
                           FROM tbl_quiz_answer AS t2
                          WHERE t2.total_score > t1.total_score
                            AND quiz_id = :quiz_id
                        ) +1 AS rank
                  FROM tbl_quiz_answer AS t1
                 WHERE quiz_id = :quiz_id
                   AND answer_id = :answer_id
                   AND answer_time > 0
              ORDER BY rank ASC';

        return $this->fetch($sql, array(
                  ':quiz_id' => $data['quiz_id']
                , ':answer_id' => $data['answer_id']
             )
        );
    }

    public function findQuizAnswerRankStudent($data)
    {
        $sql = 'SELECT quiz_id,
                       answer_id,
                       student_id,
                       total_score,
                        (SELECT COUNT(*)
                           FROM tbl_quiz_answer AS t2
                          WHERE t2.total_score > t1.total_score
                            AND quiz_id = :quiz_id
                        ) +1 AS rank
                  FROM tbl_quiz_answer AS t1
                 WHERE quiz_id = :quiz_id
                   AND student_id = :student_id
                   AND answer_time > 0
              ORDER BY rank ASC';

        return $this->fetchAll($sql, array(
                  ':quiz_id' => $data['quiz_id']
                , ':student_id' => $data['student_id']
             )
        );
    }

    public function findQuizAnswerAverage($data)
    {
        $sql = 'SELECT AVG(total_score) as avg
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql,
            array(
                  ':quiz_id' => $data['quiz_id']
            )
        );
    }

    public function findQuizAnswerTimeAverage($data)
    {
        $sql = 'SELECT AVG(answer_time) as avg
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND answer_time > 0';

        return $this->fetch($sql,
            array(
                  ':quiz_id' => $data['quiz_id']
            )
        );
    }

    public function findQuizAnswerStudent($data)
    {
        $sql = 'SELECT answer_id
                     , quiz_id
                     , school_id
                     , student_id
                     , register_datetime
                     , answer_time
                     , total_score
                     , correct_answer_rate
                  FROM tbl_quiz_answer
                 WHERE quiz_id = :quiz_id
                   AND student_id = :student_id
                   AND answer_time > 0
                 ORDER BY register_datetime ASC';

        return $this->fetchAll($sql,
            array(
                 ':quiz_id' => $data['quiz_id']
               , ':student_id' => $data['student_id']
            )
        );
    }

    public function findTotalScore($data)
    {
        $sql = 'SELECT sum(score) as total_score
                  FROM tbl_quiz_answer_query
             LEFT JOIN tbl_quiz_query USING(query_id)
                 WHERE answer_id = :answer_id
                   AND flg_right = 1';

        return $this->fetch($sql, array(':answer_id' => $data['answer_id']));
    }

    /**
     * 正解のレコードを得る
     *  QuizAnswerQueryRepository.phpに移動
     * @return array
     *
    public function findFlgRight($data)
    {
        $sql = 'SELECT quiz_answer_id as quiz_answer_id
                     , quiz_query_number as quiz_query_id
                     , flg_right
                  FROM tbl_quiz_answer_query
                 WHERE quiz_answer_id = :quiz_answer_id
                   AND flg_right = 1';

        return $this->fetchAll($sql, array(':quiz_answer_id' => $data['quiz_answer_id']));
    }
    */

    public function findAnswerTime($data)
    {
        $sql = 'SELECT answer_time
                  FROM tbl_quiz_answer
                 WHERE answer_id = :answer_id
                   AND answer_time > 0';

        return $this->fetch($sql, array(':answer_id' => $data['answer_id']));
    }

    public function quizAnswerInsert($data)
    {
        $sql = 'insert into tbl_quiz_answer (
                      quiz_id
                    , school_id
                    , student_id
                ) values (
                      :quiz_id
                    , :school_id
                    , :student_id
                )';

        $this->execute($sql,
            array(
                ':quiz_id' => $data['quiz_id']
              , ':school_id' => $data['school_id']
              , ':student_id' => $data['student_id']
            )
        );

        return $this->lastInsertId();
    }

    public function updateQuizAnswer($data)
    {
        $sql = 'UPDATE tbl_quiz_answer
                   SET register_datetime = :register_datetime
                     , answer_time = :answer_time
                     , total_score = :total_score
                     , correct_answer_rate = :correct_answer_rate
                 WHERE answer_id = :answer_id';

        return $this->exec($sql,
            array(
                  ':register_datetime' => $data['register_datetime']
                , ':answer_time' => $data['answer_time']
                , ':total_score' => $data['total_score']
                , ':correct_answer_rate' => $data['correct_answer_rate']
                , ':answer_id' => $data['answer_id']
            )
        );
    }

}
