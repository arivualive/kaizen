<?php
require_once '../config.php';

class FollowContentsRepository extends PdoBase
{

    public function findFollowContentsQueryId($data)
    {
        $sql = 'SELECT * 
                  FROM tbl_quiz_query_follow_contents 
                 WHERE quiz_query_id = :quiz_query_id
                   AND contents_category_id = :contents_category_id';

        return $this->fetchAll($sql,
            array(
                ':quiz_query_id' => $data['quiz_query_id'],
                ':contents_category_id' => $data['contents_category_id']
            )
        );
    }

    public function findFollowContentsContentsId($data)
    {
        $sql = 'SELECT * 
                  FROM tbl_quiz_query_follow_contents 
                 WHERE contents_id = :contents_id';

        return $this->fetch($sql,
            array(
                ':contents_id' => $data['contents_id']
            )
        );
    }

    public function deleteFollowContents($data)
    {
        $sql = 'DELETE FROM tbl_quiz_query_follow_contents WHERE quiz_query_id = :quiz_query_id and :contents_category_id';

        return $this->execute($sql, 
            array(
                ':quiz_query_id' => $data['quiz_query_id'],
                ':contents_category_id' => $data['contents_category_id']
            )
        );
    }

    public function saveFollowContents($data)
    {
       # $this->deleteFollowContents($data);

        $sql = 'INSERT INTO tbl_quiz_query_follow_contents (
                      quiz_query_id
                    , contents_id
                    , contents_category_id
                ) VALUES (
                      :quiz_query_id
                    , :contents_id
                    , :contents_category_id
                )';

        return $this->execute($sql, array(
            ':quiz_query_id' => $data['quiz_query_id']
          , ':contents_id' => $data['contents_id']
          , ':contents_category_id' => $data['contents_category_id']
        ));
    }
}
