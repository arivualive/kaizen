<?php
require_once '../config.php';

class QuizRepository extends PdoBase
{

    public function findQuiz()
    {
        $sql = 'SELECT * FROM tbl_quiz';

        return $this->fetchAll($sql);
    }

    public function findQuizOrderByRegisted()
    {
        $sql = 'SELECT * FROM tbl_quiz ORDER BY register_datetime DESC';

        return $this->fetchAll($sql);
    }

    public function findQuizAll()
    {
        $sql = 'SELECT quiz_id
                     , school_id
                     , title
                     , subject_section_id
                     , subject_section_name
                     , contents_name
                     , start_day
                     , a.last_day
                  FROM tbl_quiz a
             LEFT JOIN tbl_subject_section USING (subject_section_id)
             LEFT JOIN tbl_contents USING (subject_section_id)
                 WHERE a.enable = 1
              ORDER BY a.subject_section_id ASC';

        return $this->fetchAll($sql);
    }

    public function findQuizId($data)
    {
        $sql = 'SELECT *
                  FROM tbl_quiz
                 WHERE quiz_id = :quiz_id';

        return $this->fetch($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function insertQuiz($data)
    {
        $sql = 'INSERT INTO tbl_quiz (
                    school_id
                  , start_day
                  , last_day
                  , user_level_id
                  , register_id
                  , bit_classroom
                ) VALUES (
                    :school_id
                  , :start_day
                  , :last_day
                  , :user_level_id
                  , :register_id
                  , :bit_classroom
                )';

        $this->execute($sql,
            array(
                  ':school_id' => $data['school_id']
                , ':start_day' => $data['start_day']
                , ':last_day' => $data['last_day']
                , ':user_level_id' => $data['user_level_id']
                , ':register_id' => $data['register_id']
                , ':bit_classroom' => $data['bit_classroom']
            )
        );

        return $this->db->lastInsertId();
    }

    public function updateBaseQuizId($data)
    {
        $sql = 'UPDATE tbl_quiz
                   SET school_id = :school_id
                     , title = :title
                     , description = :description
                     , enable = :enable
                     , start_day = :start_day
                     , last_day = :last_day
                     , qualifying_score = :qualifying_score
                     , repeat_challenge = :repeat_challenge
                     , average_flg = :average_flg
                     , rank_flg = :rank_flg
                     , answer_rate_flg = :answer_rate_flg
                     , deviation_flg = :deviation_flg
                     , success_flg = :success_flg
                     , student_answer_flg = :student_answer_flg
                     , answer_flg = :answer_flg
                     , explain_flg = :explain_flg
                     , correct_flg = :correct_flg
                     , limit_time = :limit_time
                     , finished_message = :finished_message
                     , bit_classroom = :bit_classroom
                 WHERE quiz_id = :quiz_id';

        $params = array(
            ':school_id' => $data['school_id'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':enable' => $data['enable'],
            ':start_day' => $data['start_day'],
            ':last_day' => $data['last_day'],
            ':qualifying_score' => $data['qualifying_score'],
            ':repeat_challenge' => $data['repeat_challenge'],
            ':average_flg' => $data['average_flg'],
            ':rank_flg' => $data['rank_flg'],
            ':answer_rate_flg' => $data['answer_rate_flg'],
            ':deviation_flg' => $data['deviation_flg'],
            ':success_flg' => $data['success_flg'],
            ':student_answer_flg' => $data['student_answer_flg'],
            ':answer_flg' => $data['answer_flg'],
            ':explain_flg' => $data['explain_flg'],
            ':correct_flg' => $data['correct_flg'],
            ':limit_time' => $data['limit_time'],
            ':finished_message' => $data['finished_message'],
            ':bit_classroom' => $data['bit_classroom'],
            ':quiz_id' => $data['quiz_id']
        );

        return $this->exec($sql, $params);
    }

    public function updateQuizConfirm($data)
    {
        $sql = 'UPDATE tbl_quiz
                   SET register_datetime = :register_datetime
                     , enable = :enable
                 WHERE quiz_id = :quiz_id';


        return $this->execute($sql,
                array(
                    ':register_datetime' => date('Y-m-d H:i:s'),
                    ':enable' => 1,
                    ':quiz_id' => $data['quiz_id']
                    )
                );
    }

    public function updateSubjectSectionQuizId($data)
    {
        $sql = 'UPDATE tbl_quiz
                   SET subject_section_id = :subject_section_id
                     , relations_quiz_flg = :relations_quiz_flg
                 WHERE quiz_id = :quiz_id';

        return $this->exec($sql,
            array(
                ':subject_section_id' => $data['subject_section_id'],
                ':relations_quiz_flg' => $data['relations_quiz_flg'],
                ':quiz_id' => $data['quiz_id']
            )
        );
    }

    public function insertSubjectSection($data)
    {
        $sql = '
            INSERT INTO
                tbl_subject_section (
                    subject_section_name
                  , proportion
                  , editable
                  , enable
                  , display_order
                ) VALUES (
                    :subject_section_name
                  , :proportion
                  , :editable
                  , :enable
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':subject_section_name' => $data['subject_section_name']
          , ':proportion' => $data['proportion']
          , ':editable' => $data['editable']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }

    public function deleteQuizId($data)
    {
        $sql = 'UPDATE tbl_quiz SET enable = :enable  WHERE quiz_id = :quiz_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':enable', 0, PDO::PARAM_INT);
        $stmt->bindValue(':quiz_id', (int) $data['quiz_id'], PDO::PARAM_INT);

        return $stmt->execute();
        #return $this->execute($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function deleteRegisterDateIsNull($data)
    {
        $sql = 'DELETE FROM tbl_quiz
                            WHERE register_id = :register_id
                                AND register_datetime is null
                                AND `enable` = 0';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':register_id', (int) $data['register_id'], PDO::PARAM_INT);

        return $stmt->execute();
#        return $this->execute($sql, array(':register_id' => (int) $data['register_id']));
    }

    public function fetchRegisterDateIsNull($data)
    {
        $sql = 'SELECT quiz_id
                     FROM tbl_quiz
                   WHERE register_id = :register_id
                       AND register_datetime is null
                       AND `enable` = 0';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':register_id', (int) $data['register_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
#        return $this->execute($sql, array(':register_id' => (int) $data['register_id']));
    }

    //------ tbl_function_list ------//
    // 一覧情報の登録 (setter tbl_function_list)
    public function setFunctionList($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_list
                    (   type
                      , primary_id
                      , parent_function_group_id
                      , display_order
                    ) VALUES (
                        :type
                      ,(SELECT IFNULL(MAX(quiz_id), 1)
                          FROM tbl_quiz
                       )
                      , 0
                      ,(SELECT display_order
                          FROM
                           (SELECT IFNULL(MAX(a.display_order) + 1, 1) as display_order
                              FROM tbl_function_list as a
                           ) AS display_order
                       )
                    )
        ';

        return $this->execute($sql, array(
            ':type' => $data['type']
        ));
    }
    //!!!!!! tbl_function_list !!!!!!//
}
