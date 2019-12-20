<?php
require_once '../config.php';

class MessageDetailRepository extends PdoBase
{

    public function findMessageDetailAll($data)
    {
        $sql = '
            SELECT
                message_detail_id
              , message_id
              , message
              , send_user_level_id
              , send_user_id
              , post_date
              , enable
            FROM
                tbl_message_detail
            WHERE
                message_id = :message_id
            ORDER BY
                message_id ASC
              , post_date ASC
        ';

        return $this->fetchAll($sql, array(
            ':message_id' => $data['message_id']
        ));
    }

    public function findMessageDetailMax()
    {
        $sql = '
            SELECT
                max(message_detail_id) as max
            FROM
                tbl_message_detail
        ';

        return $this->fetch($sql);
    }

    public function findMessageDetailCount($data)
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_message_detail
            WHERE
                message_id = :message_id
        ';

        return $this->fetch($sql, array(
            ':message_id' => $data['message_id']
        ));
    }

    public function findMessageDetailOffset($data)
    {
        $sql = '
            SELECT
                message_detail_id
              , message_id
              , message
              , send_user_level_id
              , send_user_id
              , post_date
              , enable
            FROM
                tbl_message_detail
            WHERE
                message_id = :message_id
            ORDER BY
                message_id ASC
              , post_date ASC
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

    public function findMessageDetailId($data)
    {
        $sql = '
            SELECT
                message_detail_id
              , message_id
              , message
              , send_user_level_id
              , send_user_id
              , post_date
              , enable
            FROM
                tbl_message_detail
            WHERE
                message_detail_id = :message_detail_id
            ORDER BY
                message_id ASC
        ';

        return $this->fetch($sql, array(
            ':message_detail_id' => $data['message_detail_id']
        ));
    }

    public function updateMessageDetailId($data)
    {
        $sql = '
            UPDATE
                tbl_message_detail
            SET
                message_id = :message_id
              , message = :message
              , send_user_level_id = :send_user_level_id
              , send_user_id = :send_user_id
              , post_date = :post_date
              , enable = :enable
            WHERE
                message_detail_id = :message_detail_id
        ';

        return $this->execute($sql, array(
            ':message_detail_id' => $data['message_detail_id']
          , ':message_id' => $data['message_id']
          , ':message' => $data['message']
          , ':send_user_level_id' => $data['send_user_level_id']
          , ':send_user_id' => $data['send_user_id']
          , ':post_date' => $data['post_date']
          , ':enable' => $data['enable']
        ));
    }

    public function updateMessageDetailDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_message_detail
            SET
                display_order = :display_order
            WHERE
                message_detail_id = :message_detail_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':message_detail_id' => $data['message_detail_id']
        ));
    }

    public function insertMessageDetail($data)
    {
        $sql = '
            INSERT INTO
                tbl_message_detail (
                    message_id
                  , message
                  , send_user_level_id
                  , send_user_id
                  , post_date
                  , enable
                ) VALUES (
                    :message_id
                  , :message
                  , :send_user_level_id
                  , :send_user_id
                  , NOW()
                  , :enable
                )
        ';

        return $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':message' => $data['message']
          , ':send_user_level_id' => $data['send_user_level_id']
          , ':send_user_id' => $data['send_user_id']
          , ':enable' => 1
        ));
    }
}
?>
