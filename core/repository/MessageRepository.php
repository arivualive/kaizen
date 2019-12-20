<?php
require_once '../config.php';

class MessageRepository extends PdoBase
{

    public function findMessageAll()
    {
        $sql = '
             SELECT a.message_id
                  , a.school_id
                  , b.school_name
                  , a.title
                  , a.auther_user_level_id
                  , a.auther_user_id
                  , a.post_date
                  , a.type
                  , a.enable
               FROM tbl_message a
          LEFT JOIN tbl_school b USING (school_id)
           ORDER BY message_id ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findMessageCount()
    {
        $sql = '
             SELECT count(*) as count
               FROM tbl_message
        ';

        return $this->fetch($sql);
    }

    public function findMessageCountStudent()
    {
        $sql = '
             SELECT count(*) as count
               FROM tbl_message
        ';

        return $this->fetch($sql);
    }

    public function findMessageOffset($data)
    {
        $sql = '
             SELECT a.message_id
                  , a.school_id
                  , b.school_name
                  , a.title
                  , a.auther_user_level_id
                  , a.auther_user_id
                  , a.post_date
                  , a.type
                  , a.enable
               FROM tbl_message a
          LEFT JOIN tbl_school b USING (school_id)
           ORDER BY message_id ASC
                  , display_order ASC
                    LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();

        // return $this->fetchAll($sql, array(':limit' => (int)$data['limit'], ':offset' => (int)$data['offset']));
    }

    public function findMessageId($data)
    {
        $sql = '
             SELECT a.message_id
                  , a.school_id
                  , b.school_name
                  , a.title
                  , a.auther_user_level_id
                  , a.auther_user_id
                  , a.post_date
                  , a.type
                  , a.enable
               FROM tbl_message a
          LEFT JOIN tbl_school b USING (school_id)
              WHERE message_id = :message_id
           ORDER BY message_id ASC
        ';

        return $this->fetch($sql, array(
            ':message_id' => $data['message_id']
        ));
    }

    public function updateMessageId($data)
    {
        $sql = '
             UPDATE tbl_message
                SET school_id = :school_id
                  , title = :title
                  , auther_user_id = :auther_user_id
                  , auther_user_level_id = :auther_user_level_id
                  , post_date = :post_date
                  , type = :type
                  , enable = :enable
              WHERE message_id = :message_id
        ';

        return $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':auther_user_id' => $data['auther_user_id']
          , ':auther_user_level_id' => $data['auther_user_level_id']
          , ':post_date' => $data['post_date']
          , ':type' => $data['type']
          , ':enable' => $data['enable']
        ));
    }

    public function updateMessageDisplayOrder($data)
    {
        $sql = '
             UPDATE tbl_message
                SET display_order = :display_order
              WHERE message_id = :message_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':message_id' => $data['message_id']
        ));
    }

    public function insertMessage($data)
    {
        $sql = '
             INSERT INTO
                tbl_message (
                    school_id
                  , title
                  , auther_user_level_id
                  , auther_user_id
                  , register_datetime
                  , type
                  , enable
                ) VALUES (
                    :school_id
                  , :title
                  , :auther_user_level_id
                  , :auther_user_id
                  , NOW()
                  , :type
                  , :enable
                )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':auther_user_level_id' => $data['auther_user_level_id']
          , ':auther_user_id' => $data['auther_user_id']
          , ':type' => $data['type']
          , ':enable' => 1
        ));
    }

    public function deleteMessage($data)
    {
        $sql = '
             UPDATE tbl_message
                SET enable = 0
              WHERE school_id = :school_id
                AND message_id = :message_id
        ';

        return $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':school_id' => $data['school_id']
        ));
    }
}
?>
