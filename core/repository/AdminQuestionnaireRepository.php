<?php
require_once '../config.php';

class AdminQuestionnaireRepository extends PdoBase
{
    //------ tbl_questionnaire ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire($data)
    {
        $sql = ' SELECT a.questionnaire_id
                      , a.school_id
                      , a.title
                      , a.description
                      , a.start_day
                      , a.last_day
                      , a.finished_message
                      , a.type
                      , CASE
                        WHEN a.user_level_id = 0 THEN
                            b.admin_name
                        WHEN a.user_level_id = 1 THEN
                            c.teacher_name
                        END as register_user_name
                      , register_datetime
                      , if(COUNT(d.questionnaire_id) > 0, 1, 0) as answer_flag
                   FROM tbl_questionnaire as a
              LEFT JOIN tbl_admin as b
                     ON a.user_level_id = 0
                    AND a.register_user_id = b.admin_id
              LEFT JOIN tbl_teacher as c
                     ON a.user_level_id = 1
                    AND a.register_user_id = c.teacher_id
              LEFT JOIN tbl_questionnaire_answer as d
                     ON a.questionnaire_id = d.questionnaire_id
                  WHERE a.questionnaire_id = :questionnaire_id
                    AND a.enable = 1
        ';

        return $this->fetch($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    // アンケートの挿入 (setter tbl_questionnaire)
    public function insertQuestionnaire($data)
    {
        $sql = ' INSERT INTO
                    tbl_questionnaire
                    (   school_id
                      , title
                      , description
                      , finished_message
                      , enable
                      , start_day
                      , last_day
                      , user_level_id
                      , register_user_id
                      , register_datetime
                      , type
                      , display_order
                      , bit_classroom
                    ) VALUES (
                        :school_id
                      , :title
                      , :description
                      , :finished_message
                      , :enable
                      , :start_day
                      , :last_day
                      , :user_level_id
                      , :register_user_id
                      , NOW()
                      , :type
                      ,(SELECT IFNULL(MAX(a.display_order) + 1, 1) as questionnaire_id
                          FROM tbl_questionnaire as a
                       )
                      , :bit_classroom
                    )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':description' => $data['description']
          , ':finished_message' => $data['finished_message']
          , ':enable' => $data['enable']
          , ':start_day' => $data['start_day']
          , ':last_day' => $data['last_day']
          , ':user_level_id' => $data['user_level_id']
          , ':register_user_id' => $data['register_user_id']
          , ':type' => $data['type']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // アンケートの編集 (setter tbl_questionnaire)
    public function editQuestionnaire($data)
    {
        $sql = 'UPDATE tbl_questionnaire
                   SET title = :title
                     , description = :description
                     , finished_message = :finished_message
                     , start_day = :start_day
                     , last_day = :last_day
                     , bit_classroom = :bit_classroom
                 WHERE questionnaire_id = :questionnaire_id
        ';

        return $this->exec($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':title' => $data['title']
          , ':description' => $data['description']
          , ':finished_message' => $data['finished_message']
          , ':start_day' => $data['start_day']
          , ':last_day' => $data['last_day']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // アンケートの論理削除 (setter tbl_questionnaire)
    public function deleteQuestionnaire($data)
    {
        $sql = 'UPDATE tbl_questionnaire
                   SET enable = 0
                 WHERE questionnaire_id = :questionnaire_id
        ';

        return $this->exec($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    // アンケート最大IDの取得 (getter tbl_questionnaire MAX_ID)
    public function getQuestionnaireMaxId()
    {
        $sql = 'SELECT IFNULL(MAX(a.questionnaire_id), 1) as max_questionnaire_id
                  FROM tbl_questionnaire as a
        ';

        return $this->fetch($sql, array());
    }
    //!!!!!! tbl_questionnaire !!!!!!//

    //------ tbl_questionnaire_query ------//
    // アンケートクエリ-の取得 (getter tbl_questionnaire_query)
    public function getQuestionnaireQuery($data)
    {
        $sql = ' SELECT a.query_id
                      , a.query
                      , a.query_type
                      , b.type_jp
                      , a.flg_query_must
                   FROM tbl_questionnaire_query as a
              LEFT JOIN mst_query_type as b
                     ON a.query_type = b.query_type_id
                  WHERE a.questionnaire_id = :questionnaire_id
                    AND a.enable = 1
        ';

        return $this->fetchAll($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    // アンケートクエリ-の登録 (setter tbl_questionnaire_query)
    public function setQuestionnaireQuery($data)
    {
        $sql = ' INSERT INTO
                    tbl_questionnaire_query
                    (   questionnaire_id
                      , query
                      , query_type
                      , flg_query_must
                      , display_order
                      , enable
                    ) VALUES (
                        :questionnaire_id
                      , :query
                      , :query_type
                      , :flg_query_must
                      ,(SELECT IFNULL(MAX(a.display_order) + 1, 1) as questionnaire_id
                          FROM tbl_questionnaire_query as a
                       )
                      , :enable
                    )
        ';

        return $this->execute($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':query' => $data['query']
          , ':query_type' => $data['query_type']
          , ':flg_query_must' => $data['flg_query_must']
          , ':enable' => 1
        ));
    }

    // アンケートクエリ-の物理削除 (delete tbl_questionnaire_query)
    public function deleteQuestionnaireQuery($data)
    {
        $sql = 'DELETE
                  FROM tbl_questionnaire_query
                 WHERE questionnaire_id = :questionnaire_id
        ';

        return $this->exec($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    // アンケートクエリ-最大IDの取得 (getter tbl_questionnaire_query MAX_ID)
    public function getQuestionnaireQueryMaxId()
    {
        $sql = 'SELECT IFNULL(MAX(a.query_id), 1) as max_query_id
                  FROM tbl_questionnaire_query as a
        ';

        return $this->fetch($sql, array());
    }
    //!!!!!! tbl_questionnaire_query !!!!!!//

    //------ tbl_questionnaire_query_choices ------//
    // アンケートクエリ-単一・複数選択形式の取得 (getter tbl_questionnaire_query_choices)
    public function getQuestionnaireQueryChoices($data)
    {
        $sql = ' SELECT a.choices_id
                      , a.query_id
                      , a.text
                   FROM tbl_questionnaire_query_choices as a
                  WHERE a.query_id = :query_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // アンケートクエリ-単一・複数選択形式の登録 (setter tbl_questionnaire_query_choices)
    public function setQuestionnaireQueryChoices($data)
    {
        $sql = ' INSERT INTO
                    tbl_questionnaire_query_choices
                    (   query_id
                      , text
                    ) VALUES (
                        :query_id
                      , :text
                    )
        ';

        return $this->execute($sql, array(
            ':query_id' => $data['query_id']
          , ':text' => $data['text']
        ));
    }

    // アンケートクエリ-単一・複数選択形式の物理削除 (delete tbl_questionnaire_query_choices)
    public function deleteQuestionnaireQueryChoices($data)
    {
        $sql = 'DELETE
                  FROM tbl_questionnaire_query_choices
                 WHERE query_id = :query_id
        ';

        return $this->exec($sql, array(
            ':query_id' => $data['query_id']
        ));
    }
    //!!!!!! tbl_questionnaire_query_choices !!!!!!//

    //------ tbl_questionnaire_query_length ------//
    // アンケートクエリ-数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireQueryLength($data)
    {
        $sql = ' SELECT a.length_id
                      , a.query_id
                      , a.min_label
                      , a.max_label
                      , a.min_limit
                      , a.max_limit
                      , a.step
                   FROM tbl_questionnaire_query_length as a
                  WHERE a.query_id = :query_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // アンケートクエリ-数値回答形式の登録 (setter tbl_questionnaire_query_length)
    public function setQuestionnaireQueryLength($data)
    {
        $sql = ' INSERT INTO
                    tbl_questionnaire_query_length
                    (   query_id
                      , min_label
                      , max_label
                      , min_limit
                      , max_limit
                      , step
                    ) VALUES (
                        :query_id
                      , :min_label
                      , :max_label
                      , :min_limit
                      , :max_limit
                      , :step
                    )
        ';

        return $this->execute($sql, array(
            ':query_id' => $data['query_id']
          , ':min_label' => $data['min_label']
          , ':max_label' => $data['max_label']
          , ':min_limit' => $data['min_limit']
          , ':max_limit' => $data['max_limit']
          , ':step' => $data['step']
        ));
    }

    // アンケートクエリ-数値回答形式の物理削除 (delete tbl_questionnaire_query_length)
    public function deleteQuestionnaireQueryLength($data)
    {
        $sql = 'DELETE
                  FROM tbl_questionnaire_query_length
                 WHERE query_id = :query_id
        ';

        return $this->exec($sql, array(
            ':query_id' => $data['query_id']
        ));
    }
    //!!!!!! tbl_questionnaire_query_length !!!!!!//

    //------ tbl_questionnaire_answer ------//
    public function deleteQuestionnaireAnswer($data)
    {
        $sql = 'UPDATE tbl_questionnaire_answer
                   SET enable = 0
                 WHERE answer_id = :answer_id
        ';

        return $this->exec($sql, array(
            ':answer_id' => $data['answer_id']
        ));
    }
    //!!!!!! tbl_questionnaire_answer !!!!!!//

    //------ tbl_questionnaire_answer_query ------//
    public function getQuestionnaireAnswerQuery($data)
    {
        $sql = 'SELECT b.query_id
                     , CASE
                       WHEN b.query_type = 0 THEN
                           c.answer_query_id
                       WHEN b.query_type = 1 THEN
                           d.answer_query_id
                       WHEN b.query_type = 2 THEN
                           e.answer_query_id
                       WHEN b.query_type = 3 THEN
                           f.answer_query_id
                       END as answer_query_id
                     , b.query_type as type
                     , CASE
                       WHEN b.query_type = 0 THEN
                           c.choice_id
                       WHEN b.query_type = 1 THEN
                           d.choices_id
                       WHEN b.query_type = 2 THEN
                           e.word
                       WHEN b.query_type = 3 THEN
                           f.length
                       END as value
                  FROM tbl_questionnaire_answer as a
             LEFT JOIN tbl_questionnaire_answer_query as b
                    ON a.answer_id = b.answer_id
             LEFT JOIN tbl_questionnaire_answer_query_single_choice as c
                    ON b.answer_query_id = c.answer_query_id
             LEFT JOIN tbl_questionnaire_answer_query_multiple_choice as d
                    ON b.answer_query_id = d.answer_query_id
             LEFT JOIN tbl_questionnaire_answer_query_word as e
                    ON b.answer_query_id = e.answer_query_id
             LEFT JOIN tbl_questionnaire_answer_query_length as f
                    ON b.answer_query_id = f.answer_query_id
                 WHERE a.questionnaire_id = :questionnaire_id
        ';

        return $this->exec($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }
    //!!!!!! tbl_questionnaire_answer_query !!!!!!//

    //------ アンケート・レポート集計画面用 ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire_AnswerCount($data)
    {
        $sql = ' SELECT IFNULL(count(b.answer_id), 0) as answer_count
                   FROM tbl_questionnaire as a
              LEFT JOIN tbl_questionnaire_answer as b
                     ON a.questionnaire_id = b.questionnaire_id
                  WHERE a.questionnaire_id = :questionnaire_id
                    AND a.enable = 1
                    AND b.enable = 1
               GROUP BY a.questionnaire_id
        ';

        return $this->fetch($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }
    
    // アンケートクエリ-の取得 (getter tbl_questionnaire_query)
    public function getQuestionnaireQuery_AnswerCount($data)
    {
        $sql = ' SELECT IFNULL(count(d.answer_query_id), 0) as answer_count
                   FROM tbl_questionnaire_query as a
              LEFT JOIN mst_query_type as b
                     ON a.query_type = b.query_type_id
              LEFT JOIN tbl_questionnaire_answer as c
                     ON a.questionnaire_id = c.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer_query as d
                     ON a.query_id = d.query_id
                    AND c.answer_id = d.answer_id
                  WHERE a.questionnaire_id = :questionnaire_id
                    AND a.enable = 1
                    AND c.enable = 1
               GROUP BY b.query_type_id
        ';

        return $this->fetchAll($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    // アンケートクエリ-単一選択形式の取得 (getter tbl_questionnaire_query_single_choices)
    public function getQuestionnaireQuerySingleChoices_Analysis($data)
    {
        $sql = ' SELECT a.choices_id
                      , a.query_id
                      , a.text
                      , count(e.choice_id) as answer_count
                   FROM tbl_questionnaire_query_choices as a
              LEFT JOIN tbl_questionnaire_query as b
                     ON a.query_id = b.query_id
              LEFT JOIN tbl_questionnaire_answer as c
                     ON b.questionnaire_id = c.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer_query as d
                     ON c.answer_id = d.answer_id
              LEFT JOIN tbl_questionnaire_answer_query_single_choice as e
                     ON d.answer_query_id = e.answer_query_id
                    AND a.choices_id = e.choice_id
                  WHERE a.query_id = :query_id
                    AND b.enable = 1
                    AND c.enable = 1
               GROUP BY a.choices_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // アンケートクエリ-複数選択形式の取得 (getter tbl_questionnaire_query_multiple_choices)
    public function getQuestionnaireQueryMultipleChoices_Analysis($data)
    {
        $sql = ' SELECT a.choices_id
                      , a.query_id
                      , a.text
                      , count(e.choices_id) as answer_count
                   FROM tbl_questionnaire_query_choices as a
              LEFT JOIN tbl_questionnaire_query as b
                     ON a.query_id = b.query_id
              LEFT JOIN tbl_questionnaire_answer as c
                     ON b.questionnaire_id = c.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer_query as d
                     ON c.answer_id = d.answer_id
              LEFT JOIN tbl_questionnaire_answer_query_multiple_choice as e
                     ON d.answer_query_id = e.answer_query_id
                    AND a.choices_id = e.choices_id
                  WHERE a.query_id = :query_id
                    AND b.enable = 1
                    AND c.enable = 1
               GROUP BY a.choices_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // アンケートクエリ-自由回答形式の取得 (getter tbl_questionnaire_query_word)
    public function getQuestionnaireQueryWord_Analysis($data)
    {
        $sql = ' SELECT b.answer_id
                      , e.student_code
                      , e.student_name
                      , e.id
                      , d.word as answer
                   FROM tbl_questionnaire_query as a
              LEFT JOIN tbl_questionnaire_answer as b
                     ON a.questionnaire_id = b.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer_query as c
                     ON b.answer_id = c.answer_id
              LEFT JOIN tbl_questionnaire_answer_query_word as d
                     ON c.answer_query_id = d.answer_query_id
              LEFT JOIN tbl_student as e
                     ON b.student_id = e.student_id
                  WHERE a.query_id = :query_id
                    AND a.enable = 1
                    AND b.enable = 1
               GROUP BY b.answer_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // アンケートクエリ-数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireQueryLength_Analysis($data)
    {
        $sql = ' SELECT e.length as value
                      , count(e.length) as count
                   FROM tbl_questionnaire_query_length as a
              LEFT JOIN tbl_questionnaire_query as b
                     ON a.query_id = b.query_id
              LEFT JOIN tbl_questionnaire_answer as c
                     ON b.questionnaire_id = c.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer_query as d
                     ON c.answer_id = d.answer_id
              LEFT JOIN tbl_questionnaire_answer_query_length as e
                     ON d.answer_query_id = e.answer_query_id
                  WHERE a.query_id = :query_id
                    AND b.enable = 1
                    AND c.enable = 1
                    AND length != ""
               GROUP BY e.length
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    // 学生回答情報-答案受講者一覧の取得 (getter tbl_questionnaire_query_single_choice)
    public function getStudent_Student($data)
    {
        $sql = ' SELECT b.answer_datetime
                      , a.student_code
                      , a.student_name
                      , a.id
                      , b.answer_id
                   FROM tbl_student as a
              LEFT JOIN tbl_questionnaire_answer as b
                     ON a.student_id = b.student_id
                  WHERE b.questionnaire_id = :questionnaire_id
                    AND a.school_id = :school_id
                    AND a.enable = 1
                    AND b.enable = 1
        ';

        return $this->fetchAll($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':school_id' => $data['school_id']
        ));
    }

    // 学生回答情報-質問回答情報の取得 (getter tbl_questionnaire_query_single_choice)
    public function getQuestionnaireAnswerQuery_Student($data)
    {
        $sql = ' SELECT(SELECT d.answer_id
                          FROM tbl_questionnaire_answer_query as d
                         WHERE d.answer_id = :answer_id
                      GROUP BY d.answer_id) as answer_id
                      ,(SELECT e.answer_query_id
                          FROM tbl_questionnaire_answer_query as e
                         WHERE e.answer_id = :answer_id
                           AND e.query_id = c.query_id
                      GROUP BY answer_query_id) as answer_query_id
                      , b.query_type
                   FROM tbl_questionnaire as a
                   JOIN tbl_questionnaire_query as b
                     ON a.questionnaire_id = b.questionnaire_id
                   JOIN tbl_questionnaire_answer_query as c
                     ON b.query_id = c.query_id
                  WHERE a.questionnaire_id = :questionnaire_id
               GROUP BY b.query_id
        ';

        return $this->fetchAll($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':answer_id' => $data['answer_id']
        ));
    }

    // 学生回答情報-単一選択形式の取得 (getter tbl_questionnaire_query_single_choice)
    public function getQuestionnaireAnswerQuerySingleChoice_Student($data)
    {
        $sql = ' SELECT b.text
                   FROM tbl_questionnaire_answer_query_single_choice as a
              LEFT JOIN tbl_questionnaire_query_choices as b
                     ON a.choice_id = b.choices_id
                  WHERE a.answer_query_id = :answer_query_id
        ';

        return $this->fetchAll($sql, array(
            ':answer_query_id' => $data['answer_query_id']
        ));
    }

    // 学生回答情報-複数選択形式の取得 (getter tbl_questionnaire_query_multiple_choice)
    public function getQuestionnaireAnswerQueryMultipleChoice_Student($data)
    {
        $sql = ' SELECT b.text
                   FROM tbl_questionnaire_answer_query_multiple_choice as a
              LEFT JOIN tbl_questionnaire_query_choices as b
                     ON a.choices_id = b.choices_id
                  WHERE a.answer_query_id = :answer_query_id
        ';

        return $this->fetchAll($sql, array(
            ':answer_query_id' => $data['answer_query_id']
        ));
    }

    // 学生回答情報-自由回答形式の取得 (getter tbl_questionnaire_query_word)
    public function getQuestionnaireAnswerQueryWord_Student($data)
    {
        $sql = ' SELECT a.word
                   FROM tbl_questionnaire_answer_query_word as a
                  WHERE a.answer_query_id = :answer_query_id
        ';

        return $this->fetchAll($sql, array(
            ':answer_query_id' => $data['answer_query_id']
        ));
    }

    // 学生回答情報-数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireAnswerQueryLength_Student($data)
    {
        $sql = ' SELECT a.length
                   FROM tbl_questionnaire_answer_query_length as a
                  WHERE a.answer_query_id = :answer_query_id
        ';

        return $this->fetchAll($sql, array(
            ':answer_query_id' => $data['answer_query_id']
        ));
    }

    //!!!!!! アンケート・レポート集計画面用 !!!!!!//

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
                      ,(SELECT IFNULL(MAX(questionnaire_id), 1)
                          FROM tbl_questionnaire
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
