<?php
require_once '../config.php';

class QuestionnaireQueryRepository extends PdoBase
{

    public function findQuestionnaireQueryAll()
    {
        $sql = '
            SELECT
                query_id
              , questionnaire_id
              , query
              , query_type
              , flg_query_must
              , enable
              , display_order
            FROM
                tbl_questionnaire_query
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findQuestionnaireQueryMax()
    {
        $sql = '
            SELECT
                max(query_id) as max
            FROM
                tbl_questionnaire_query
        ';

        return $this->fetch($sql);
    }

    public function findQuestionnaireQueryEnable()
    {
        $sql = '
            SELECT
                query_id
            FROM
                tbl_questionnaire_query
            WHERE
                enable = 1
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findQuestionnaireQueryId($data)
    {
        $sql = '
            SELECT
                query_id
              , questionnaire_id
              , query
              , query_type
              , flg_query_must
              , enable
              , display_order
            FROM
                tbl_questionnaire_query
            WHERE
                query_id = :query_id
        ';

        return $this->fetch($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    public function findQuestionnaireQueryChoice($data)
    {
        $sql = '
            SELECT
                choices_id
              , query_id
              , text
            FROM
                tbl_questionnaire_query_choices
            WHERE
                query_id = :query_id
        ';

        return $this->fetchAll($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    public function findQuestionnaireQueryNumber($data)
    {
        $sql = '
            SELECT
                query_length_id
              , query_id
              , min_label
              , max_label
              , min_limit
              , max_limit
              , step
            FROM
                tbl_questionnaire_query_length
            WHERE
                query_id = :query_id
        ';

        return $this->fetch($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    public function findQuestionnaireQueryType() {
        $sql = '
            SELECT
                query_type_id
              , type
              , type_jp
            FROM 
                mst_query_type
        ';

        return $this->fetchAll($sql);
    }

    public function updateQuestionnaireQueryId($data)
    {
        $sql = '
            UPDATE
                tbl_questionnaire_query
            SET
                questionnaire_id = :questionnaire_id
              , query = :query
              , query_type = :query_type
              , flg_query_must = :flg_query_must
              , enable = :enable
              , display_order = :display_order
            WHERE
                query_id = :query_id
        ';

        return $this->execute($sql, array(
            ':query_id' => $data['query_id']
          , ':questionnaire_id' => $data['questionnaire_id']
          , ':query' => $data['query']
          , ':query_type' => $data['query_type']
          , ':flg_query_must' => $data['flg_query_must']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
        ));
    }

    public function updateQuestionnaireQueryDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_questionnaire_query
            SET
                display_order = :display_order
            WHERE
                query_id = :query_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':query_id' => $data['query_id']
        ));
    }

    public function deleteQuestionnaireQueryChoice($data)
    {
        $sql = '
            DELETE FROM
                tbl_questionnaire_query_choices
            WHERE
                query_id = :query_id
        ';

        return $this->execute($sql, array(
                ':query_id' => $data['query_id']
        ));
    }

    public function deleteQuestionnaireQueryNumber($data)
    {
        $sql = '
            DELETE FROM
                tbl_questionnaire_query_length
            WHERE
                query_id = :query_id
        ';

        return $this->execute($sql, array(
            ':query_id' => $data['query_id']
        ));
    }

    public function insertQuestionnaireQuery($data)
    {
        $sql = '
            INSERT INTO
                tbl_questionnaire_query (
                    questionnaire_id
                  , query
                  , query_type
                  , flg_query_must
                  , enable
                  , display_order
                ) VALUES (
                    :questionnaire_id
                  , :query
                  , :query_type
                  , :flg_query_must
                  , :enable
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':query' => $data['query']
          , ':query_type' => $data['query_type']
          , ':flg_query_must' => $data['flg_query_must']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }

    public function insertQuestionnaireQueryChoice($data)
    {
        $sql = '
            INSERT INTO
                tbl_questionnaire_query_choices (
                    query_id
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

    public function insertQuestionnaireQueryNumber($data)
    {
        $sql = '
            INSERT INTO
                tbl_questionnaire_query_length (
                    query_id
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
}
?>
