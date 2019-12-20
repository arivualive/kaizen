<?php
require_once '../config.php';

class QuestionnaireRepository extends PdoBase
{

    public function findQuestionnaireAll()
    {
        $sql = '
            SELECT
                questionnaire_id
              , school_id
              , title
              , description
              , finished_message
              , enable
              , display_order
              , start_day
              , last_day
              , user_level_id
              , register_user_id
              , register_datetime
              , type
            FROM
                tbl_questionnaire
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findQuestionnaireMax()
    {
        $sql = '
            SELECT
                max(questionnaire_id) as max
            FROM
                tbl_questionnaire
        ';

        return $this->fetch($sql);
    }

    public function findQuestionnaireCount()
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_questionnaire
        ';

        return $this->fetch($sql);
    }

    public function findQuestionnaireOffset($data)
    {
        $sql = '
            SELECT
                a.questionnaire_id
              , a.school_id
              , b.school_name
              , a.title
              , a.description
              , a.finished_message
              , a.enable
              , a.display_order
              , a.start_day
              , a.last_day
              , a.user_level_id
              , a.register_user_id
              , a.register_datetime
              , a.type
            FROM
                tbl_questionnaire a
            LEFT JOIN
                tbl_school b USING (school_id)
            ORDER BY
                display_order ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();

        // return $this->fetchAll($sql, array(':limit' => (int)$data['limit'], ':offset' => (int)$data['offset']));
    }

    public function findQuestionnaireTitle() {
        $sql = '
            SELECT
                questionnaire_id
              , title
            FROM
                tbl_questionnaire
        ';

        return $this->fetchAll($sql);
    }

    public function findQuestionnaireEnable()
    {
        $sql = '
            SELECT
                questionnaire_id
            FROM
                tbl_questionnaire
            WHERE
                enable = 1
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findQuestionnaireId($data)
    {
        $sql = '
            SELECT
                questionnaire_id
              , school_id
              , title
              , description
              , finished_message
              , enable
              , display_order
              , start_day
              , last_day
              , user_level_id
              , register_user_id
              , register_datetime
              , type
            FROM
                tbl_questionnaire
            WHERE
                questionnaire_id = :questionnaire_id
        ';

        return $this->fetch($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    public function updateQuestionnaireId($data)
    {
        $sql = '
            UPDATE
                tbl_questionnaire
            SET
                school_id = :school_id
              , title = :title
              , description = :description
              , finished_message = :finished_message
              , enable = :enable
              , display_order = :display_order
              , start_day = :start_day
              , last_day = :last_day
              , type = :type
            WHERE
                questionnaire_id = :questionnaire_id
        ';

        return $this->execute($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
          , ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':description' => $data['description']
          , ':finished_message' => $data['finished_message']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':start_day' => $data['start_day']
          , ':last_day' => $data['last_day']
          , ':type' => $data['type']
        ));
    }

    public function updateQuestionnaireDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_questionnaire
            SET
                display_order = :display_order
            WHERE
                questionnaire_id = :questionnaire_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':questionnaire_id' => $data['questionnaire_id']
        ));
    }

    public function insertQuestionnaire($data)
    {
        $sql = '
            INSERT INTO
                tbl_questionnaire (
                    school_id
                  , title
                  , description
                  , finished_message
                  , enable
                  , display_order
                  , start_day
                  , last_day
                  , user_level_id
                  , register_user_id
                  , register_datetime
                ) VALUES (
                    :school_id
                  , :title
                  , :description
                  , :finished_message
                  , :enable
                  , :display_order
                  , :start_day
                  , :last_day
                  , :user_level_id
                  , :register_user_id
                  , NOW()
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':description' => $data['description']
          , ':finished_message' => $data['finished_message']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
          , ':start_day' => $data['start_day']
          , ':last_day' => $data['last_day']
          , ':user_level_id' => $data['user_level_id']
          , ':register_user_id' => $data['register_user_id']
        ));
    }
}
?>
