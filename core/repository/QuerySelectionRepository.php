<?php
require_once '../config.php';

class QuerySelectionRepository extends PdoBase
{
    public function updateSelection($data)
    {
        $sql = 'UPDATE tbl_quiz_query_selection
                   SET query_id = :query_id
                     , text = :text
                     , display_order = :display_order
                     , correct_flg = :correct_flg
                     , enable = :enable
                 WHERE selection_id = :selection_id';

        return $this->exec($sql,
                 array(
                      ':query_id' => $data['query_id']
                    , ':text' => $data['text']
                    , ':display_order' => $data['display_order']
                    , ':correct_flg' => $data['correct_flg']
                    , ':enable' => 1
                    , ':selection_id' => $data['selection_id']
                 )
        );
    }

    public function insertSelection($data)
    {
        $sql = 'INSERT INTO tbl_quiz_query_selection (
                      quiz_id
                    , query_id
                    , text
                    , display_order
                    , correct_flg
                    , enable
                ) VALUES (
                      :quiz_id
                    , :query_id
                    , :text
                    , :display_order
                    , :correct_flg
                    , :enable
                )';

        $this->exec($sql,
            array(
                  ':quiz_id' => $data['quiz_id']
                , ':query_id' => $data['query_id']
                , ':text' => $data['text']
                , ':display_order' => $data['display_order']
                , ':correct_flg' => $data['correct_flg']
                , ':enable' => 1
            )
        );

        return $this->db->lastInsertId();
    }

    public function deleteMark($data)
    {
        $sql = 'UPDATE tbl_quiz_query_selection
                   SET enable = :enable
                 WHERE query_id = :query_id';

        return $this->exec($sql,
            array(
                  ':enable' => $data['enable']
                , ':query_id' => $data['query_id']
            )
        );
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
              ORDER BY display_order ASC';

        return $this->fetchAll($sql, array(':query_id' => $data['query_id']));
    }

    public function updateCorrectFlg($data)
    {
        $sql = 'UPDATE tbl_quiz_query_selection
                   SET correct_flg = :correct_flg
                 WHERE selection_id = :selection_id
                   AND enable = :enable';

        return $this->exec($sql,
            array(
                  ':correct_flg' => 1
                , ':selection_id' => $data['selection_id']
                , ':enable' => 1
            )
        );
    }

    public function removeQuerySelection($data)
    {
        $sql = 'DELETE FROM tbl_quiz_query_selection
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                 ':query_id' => $data['query_id']
            )
        );
    }

    public function disableQuerySelection($data)
    {
        $sql = 'UPDATE tbl_quiz_query_selection
                   SET enable = :enable
                 WHERE query_id = :query_id';

        return $this->execute($sql,
            array(
                  ':enable' => 0
                , ':query_id' => $data['query_id']
            )
        );
    }

    public function countCorrect($data)
    {
        $sql = 'SELECT count(*) as count_correct
                  FROM tbl_quiz_query_selection
                 WHERE query_id = :query_id
                   AND correct_flg = 1
                   AND enable = 1';

        return $this->fetch($sql, array(':query_id' => $data['query_id']));
    }

    public function selectionCount($data)
    {
        $sql = 'SELECT selection_id, count(*) as answer_count
                  FROM tbl_quiz_answer_query_choice
                 WHERE selection_id = :selection_id';

        return $this->fetch($sql, array(':selection_id' => $data['selection_id']));
    }

}
