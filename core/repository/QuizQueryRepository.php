<?php
require_once '../config.php';

class QuizQueryRepository extends PdoBase
{

    public function findQueryQuizId($data)
    {
        $sql = 'SELECT query_id
                     , quiz_id
                     , query_text
                     , description
                     , query_type
                     , display_order
                     , score
                     , image_file_name
                     , sound_file_name
                     , query_type
                     , display_order
                  FROM tbl_quiz_query
                 WHERE quiz_id = :quiz_id
                   AND enable = 1
              ORDER BY display_order ASC';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findQueryId($data)
    {
        $sql = 'SELECT query_id
                     , quiz_id
                     , query_text
                     , description
                     , score
                     , image_file_name
                     , sound_file_name
                     , query_type
                     , display_order
                  FROM tbl_quiz_query
                 WHERE query_id = :query_id
                   AND enable = 1';

        return $this->fetch($sql, array(':query_id' => $data['query_id']));
    }

    public function findSelectionQuizId($data)
    {
        $sql = 'SELECT *
                  FROM tbl_quiz_query_selection
                 WHERE quiz_id = :quiz_id
                   AND enable = 1
              ORDER BY selection_id ASC';

        return $this->fetchAll($sql, array(':quiz_id' => $data['quiz_id']));
    }

    public function findSelectionQueryId($data)
    {
        $sql = 'SELECT *
                  FROM tbl_quiz_query_selection
                 WHERE query_id = :query_id
                   AND enable = 1
              ORDER BY selection_id ASC';

        return $this->fetchAll($sql, array(':query_id' => $data['query_id']));
    }

    // 従来のquiz_query_idがそのままorder byの値にっているの使えないかも
    /*
    public function updateQueryDisplayOrder($display_order)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET display_order = :display_order
                 WHERE quiz_query_id = :display_order';

        return $this->exec($sql, array(':display_order' => $display_order));
    }
    */

    public function updateQueryType($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET query_type = :query_type
                 WHERE query_id = :query_id';

        return $this->exec($sql,
            array(
                ':query_type' => $data['query_type'],
                ':query_id' => $data['query_id']
            )
        );
    }

    public function updateOrderQuery($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET display_order = :display_order
                 WHERE query_id = :query_id';

        return $this->exec($sql,
            array(
                ':display_order' => $data['display_order'],
                ':query_id' => $data['query_id']
            )
        );
    }

    public function updateImageFile($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET image_file_name = :image_file_name
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                ':query_id' => $data['query_id'],
                ':image_file_name' => $data['image_file_name']
            )
        );
    }

    public function updateSoundFile($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET sound_file_name = :sound_file_name
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                ':query_id' => $data['query_id'],
                ':sound_file_name' => $data['sound_file_name']
            )
        );
    }

    public function insertQuery($data)
    {
        $sql = 'INSERT INTO tbl_quiz_query (
                    quiz_id,
                    display_order,
                    enable
                ) VALUES (
                     :quiz_id,
                     :display_order,
                     :enable
                )';

        $this->execute($sql,
            array(
                ':quiz_id' => $data['quiz_id'],
                ':display_order' => $data['display_order'],
                ':enable' => 1
            )
        );

        return $this->db->lastInsertId();

        /*
        if ($result >= 1) {
            return $this->updateQueryDisplayOrder($this->db->lastInsertId());
        }

        return null;
         */
    }

    public function updateQuery($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET query_text = :query_text
                     , description = :description
                     , score = :score
                 WHERE query_id = :query_id
                   AND enable = :enable';

        return $this->execute($sql,
                 array(
                      ':query_text' => $data['query_text']
                    , ':description' => $data['description']
                    , ':score' => $data['score']
                    , ':query_id' => $data['query_id']
                    , ':enable' => 1
                 )
        );
    }

    public function getImage($data)
    {
        $result = array();

        $sql = 'SELECT quiz_id, query_id, image_file_name
                  FROM tbl_quiz_query
                 WHERE query_id = :query_id
                   AND enable = 1';

        $result =  $this->fetch($sql, array(':query_id' => $data['query_id']));
        $result['path'] = sprintf("%s_%s.deploy", $result['quiz_id'], $result['query_id']);

        return $result;
    }

    public function removeImageFile($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET image_file_name = null
                 WHERE query_id = :query_id';

        return $this->execute($sql, array(':query_id' => $data['query_id']));
    }

    public function removeSoundFile($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET sound_file_name = null
                 WHERE query_id = :query_id';

        return $this->execute($sql, array(':query_id' => $data['query_id']));
    }

    public function removeQuery($data)
    {
        $sql = 'DELETE FROM tbl_quiz_query
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                 ':query_id' => $data['query_id']
            )
        );
    }

    public function disableQuery($data)
    {
        $sql = 'UPDATE tbl_quiz_query
                   SET enable = :enable
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                  ':enable' => 0
                , ':query_id' => $data['query_id']
            )
        );
    }

    public function queryTypeAll()
    {
        $sql = 'SELECT query_type_id
                     , type
                     , type_jp
                  FROM mst_query_type
              ORDER BY query_type_id ASC';

        return $this->fetchAll($sql);
    }
}
