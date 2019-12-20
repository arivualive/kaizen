<?php
require_once '../config.php';

class MessageTargetRepository extends PdoBase
{

    public function findMessageTargetAll($data)
    {
        $sql = '
            SELECT
                message_target_id
              , message_id
              , grade_id
              , classroom_id
              , course_id
              , receive_user_level_id
              , receive_user_id
            FROM
                tbl_message_target
            WHERE
                message_id = :message_id
            ORDER BY
                message_id ASC
        ';

        return $this->fetchAll($sql, array(
            ':message_id' => $data['message_id']
        ));
    }

    public function findMessageTargetMax()
    {
        $sql = '
            SELECT
                max(message_target_id) as max
            FROM
                tbl_message_target
        ';

        return $this->fetch($sql);
    }

    public function findMessageTargetCount($data)
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_message_target
            WHERE
                message_id = :message_id
        ';

        return $this->fetch($sql, array(
            ':message_id' => $data['message_id']
        ));
    }

    public function findMessageTargetOffset($data)
    {
        $sql = '
            SELECT
                message_target_id
              , message_id
              , grade_id
              , classroom_id
              , course_id
              , receive_user_level_id
              , receive_user_id
            FROM
                tbl_message_target
            WHERE
                message_id = :message_id
            ORDER BY
                message_id ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':message_id', (int) $data['message_id'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();

        // return $this->fetchAll($sql, array(':limit' => (int)$data['limit'], ':offset' => (int)$data['offset']));
    }

    public function findMessageTargetId($data)
    {
        $sql = '
            SELECT
                message_target_id
              , message_id
              , grade_id
              , classroom_id
              , course_id
              , receive_user_level_id
              , receive_user_id
            FROM
                tbl_message_target
            WHERE
                message_target_id = :message_target_id
            ORDER BY
                message_id ASC
        ';

        return $this->fetch($sql, array(
            ':message_target_id' => $data['message_target_id']
        ));
    }

    public function updateMessageTargetId($data)
    {
        $sql = '
            UPDATE
                tbl_message_target
            SET
                message_id = :message_id
              , grade_id = :grade_id
              , classroom_id = :classroom_id
              , course_id = :course_id
              , receive_user_level_id = :receive_user_level_id
              , receive_user_id = :receive_user_id
            WHERE
                message_target_id = :message_target_id
        ';

        return $this->execute($sql, array(
            ':message_target_id' => $data['message_target_id']
          , ':message_id' => $data['message_id']
          , ':grade_id' => $data['grade_id']
          , ':classroom_id' => $data['classroom_id']
          , ':course_id' => $data['course_id']
          , ':receive_user_level_id' => $data['receive_user_level_id']
          , ':receive_user_id' => $data['receive_user_id']
        ));
    }

    public function updateMessageTargetDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_message_target
            SET
                display_order = :display_order
            WHERE
                message_target_id = :message_target_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':message_target_id' => $data['message_target_id']
        ));
    }

    public function insertMessageTarget($data)
    {
        $sql = '
            INSERT INTO
                tbl_message_target (
                    message_id
                  , grade_id
                  , classroom_id
                  , course_id
                  , receive_user_level_id
                  , receive_user_id
                ) VALUES (
                    :message_id
                  , :grade_id
                  , :classroom_id
                  , :course_id
                  , :receive_user_level_id
                  , :receive_user_id
                )
        ';

        return $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':grade_id' => $data['grade_id']
          , ':classroom_id' => $data['classroom_id']
          , ':course_id' => $data['course_id']
          , ':receive_user_level_id' => $data['receive_user_level_id']
          , ':receive_user_id' => $data['receive_user_id']
        ));
    }
}
?>
