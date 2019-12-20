<?php
require_once '../config.php';

class GetStudentQuestionnaireAnswerModel extends PdoBase
{

    public function getQuestionnaire($data)
    {
        $sql = ' SELECT a.questionnaire_id
                      , a.school_id
                      , a.title
                      , a.description
                      , a.finished_message
                      , a.type
                   FROM tbl_questionnaire as a
                  WHERE a.questionnaire_id = :questionnaire_id
                    AND a.enable = 1
        ';

        return $this->fetch($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

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

    public function setQuestionnaireAnswer($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer (
                        questionnaire_id
                      , student_id
                      , answer_datetime
                      , enable
                  ) VALUES (
                        :questionnaire_id
                      , :student_id
                      , NOW()
                      , 1
                  )
        ';

        $result = $this->exec($sql,
                array(
                        ':questionnaire_id' => $data['questionnaire_id']
                      , ':student_id' => $data['student_id']
                )
        );

        if ($result >= 1) {
            return $this->db->lastInsertId();
        }

        return null;
    }

    public function setQuestionnaireAnswerQuery($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer_query (
                        answer_id
                      , query_id
                      , query_type
                ) VALUES (
                        :answer_id
                      , :query_id
                      , :query_type
                )
        ';

        $result = $this->exec($sql,
                array(
                        ':answer_id' => $data['answer_id']
                      , ':query_id' => $data['query_id']
                      , ':query_type' => $data['query_type']
                )
        );

        if ($result >= 1) {
            return $this->db->lastInsertId();
        }

        return null;
    }

    public function setQuestionnaireAnswerQuerySingleChoice($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer_query_single_choice (
                        answer_query_id
                      , choice_id
                ) VALUES (
                        :answer_query_id
                      , :choice_id
                )
        ';

        return $this->exec($sql,
                array(
                        ':answer_query_id' => $data['answer_query_id']
                      , ':choice_id' => $data['choice_id']
                )
        );
    }

    public function setQuestionnaireAnswerQueryMultipleChoice($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer_query_multiple_choice (
                        answer_query_id
                      , choices_id
                ) VALUES 
                      ' . $data['sql_string'] . '
        ';

        return $this->exec($sql,
                array(
                        ':sql_string' => $data['sql_string']
                )
        );
    }

    public function setQuestionnaireAnswerQueryWord($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer_query_word (
                        answer_query_id
                      , word
                ) VALUES (
                        :answer_query_id
                      , :word
                )
        ';

        return $this->exec($sql,
                array(
                        ':answer_query_id' => $data['answer_query_id']
                      , ':word' => $data['word']
                )
        );
    }

    public function setQuestionnaireAnswerQueryLength($data)
    {
        $sql = ' INSERT INTO tbl_questionnaire_answer_query_length (
                        answer_query_id
                      , length
                ) VALUES (
                        :answer_query_id
                      , :length
                )
        ';

        return $this->exec($sql,
                 array(
                        ':answer_query_id' => $data['answer_query_id']
                      , ':length' => $data['length']
                 )
        );
    }
}
