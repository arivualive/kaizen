<?php
require_once '../config.php';

class ContentsCategoryRepository extends PdoBase
{

    public function findContentsCategoryAll ()
    {
        $sql = 'SELECT * FROM mst_contents_category';

        return $this->fetchAll($sql);
    }

    public function findContentsCategoryName ()
    {
        $sql = '
            SELECT
                contents_category_id
              , contents_category_name
            FROM
                mst_contents_category
        ';

        return $this->fetchAll($sql);
    }

    public function findContentsCategoryId ($data)
    {
        $sql = '
            SELECT
                *
            FROM
                mst_contents_category
            WHERE
                contents_category_id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    public function updateContentsCategoryDisplayOrder ($data)
    {
        $sql = '
            UPDATE
                mst_contents_category
            SET
                display_order = :display_order
            WHERE
                contents_category_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':id' => $data['id']
        ));
    }

    public function insertContentsCategory ($data)
    {
        $sql = '
            INSERT INTO
                mst_contents_category (
                    contents_category_id
                  , contents_category_name
                  , enable
                ) VALUES (
                    :contents_category_id
                  , :contents_category_name
                  , :enable
                )
        ';

        return $this->execute($sql, array(
            ':contents_category_id' => $data['contents_category_id']
          , ':contents_category_name' => $data['contents_category_name']
          , ':enable' => 1
        ));
    }
}
