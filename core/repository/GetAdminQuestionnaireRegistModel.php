<?php
require_once '../config.php';

class GetAdminQuestionnaireRegistModel extends PdoBase
{
    public function getSubject($data)
    {
        $sql = 'SELECT a.subject_genre_id
                     , a.subject_genre_name
                  FROM tbl_subject_genre as a
             LEFT JOIN tbl_subject_group as b
                 USING(subject_group_id)
                 WHERE b.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getSubjectSection($data)
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                  FROM tbl_subject_section as a
                 WHERE a.subject_genre_id = :subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

    public function setQuestionnaire($data)
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
                      ,(SELECT MAX(a.questionnaire_id + 1)
                          FROM tbl_questionnaire as a
                       )
                      , :bit_classroom
                    )
        ';

        $this->execute($sql, array(
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

    public function getQuestionnaireMaxId()
    {
        $sql = 'SELECT MAX(a.questionnaire_id) as max_questionnaire_id
                  FROM tbl_questionnaire a
        ';

        return $this->fetch($sql, array());
    }

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
                      ,(SELECT MAX(a.questionnaire_id + 1)
                          FROM tbl_questionnaire as a
                       )
                      , :enable
                    )
        ';

        return $this->execute($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':query' => $data['query']
          , ':query_type' => $data['query_type']
          , ':flg_query_must' => $data['flg_query_must']
          , ':enable' => $data['enable']
        ));
    }

    public function getQuestionnaireQueryMaxId()
    {
        $sql = 'SELECT MAX(a.query_id) as max_query_id
                  FROM tbl_questionnaire_query as a
        ';

        return $this->fetch($sql, array());
    }

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

    public function setFunctionList($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_list
                    (   type
                      , primary_id
                      , function_group_id
                      , subject_genre_id
                      , subject_section_id
                      , display_order
                    ) VALUES (
                        :type
                      ,(SELECT MAX(questionnaire_id)
                          FROM tbl_questionnaire
                       )
                      , :function_group_id
                      ,(SELECT subject_genre_id
                          FROM tbl_subject_section
                         WHERE subject_section_id = :subject_section_id)
                      , :subject_section_id
                      ,(SELECT display_order
                          FROM
                           (SELECT (MAX(display_order) + 1) as display_order
                              FROM tbl_function_list) as display_order
                           )
                       )
        ';

        return $this->execute($sql, array(
            ':type' => $data['type']
          , ':function_group_id' => $data['function_group_id']
          , ':subject_section_id' => $data['subject_section_id']
        ));
    }
}
