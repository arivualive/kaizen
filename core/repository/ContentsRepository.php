<?php
require_once '../config.php';

class ContentsRepository extends PdoBase
{

    public function findContentsAll()
    {
        $sql = 'SELECT contents_id
                     , a.contents_category_id
                     , b.category_name
                     , a.subject_section_id
                     , c.subject_section_name
                     , a.contents_name
                     , a.enable
                     , a.display_order
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b USING (contents_category_id)
             LEFT JOIN tbl_subject_section c USING (subject_section_id)
              ORDER BY a.display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findContentsId($data)
    {
        $sql = 'SELECT contents_id
                     , a.contents_category_id
                     , b.contents_category_name
                     , a.subject_section_id
                     , c.subject_section_name
                     , a.contents_name
                     , a.comment
                     , a.first_day
                     , a.last_day
                     , a.file_name
                     , a.user_level_id
                     , a.register_id
                     , a.contents_extension_id
                     , a.size
                     , a.enable
                     , a.display_order
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b USING (contents_category_id)
             LEFT JOIN tbl_subject_section c USING (subject_section_id)
                 WHERE contents_id = :contents_id
              ORDER BY a.display_order ASC';

        return $this->fetch($sql, array(
            ':contents_id' => $data['contents_id']
        ));
    }

    public function findContentsEnable()
    {
        $sql = 'SELECT a.contents_id
                     , a.subject_section_id
                     , b.subject_section_name
                     , a.contents_name
                  FROM tbl_contents a
             LEFT JOIN tbl_subject_section b USING (subject_section_id)
                 WHERE a.enable = 1
              ORDER BY b.subject_section_id ASC
                     , a.display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findContentsCount()
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_contents
        ';

        return $this->fetch($sql);
    }

    public function findContentsOffset($data)
    {
        $sql = '
            SELECT
                contents_id
              , a.contents_category_id
              , b.contents_category_name
              , a.subject_section_id
              , c.subject_section_name
              , a.contents_name
              , a.enable
              , a.display_order
            FROM
                tbl_contents a
            LEFT JOIN
                mst_contents_category b
            USING
                (contents_category_id)
            LEFT JOIN
                tbl_subject_section c
            USING
                (subject_section_id)
            ORDER BY
                display_order ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findContentsSubjectSectionId($data)
    {
        $sql = 'SELECT a.contents_id
                     , a.contents_name
                     , a.subject_section_id
                     , b.subject_section_name
                  FROM tbl_contents a
             LEFT JOIN tbl_subject_section b USING (subject_section_id)';

        return $this->fetch($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function findContentsOrderById($data)
    {
        $sql = '
            SELECT
                a.contents_id
              , a.contents_category_id
              , b.category_name
              , a.subject_section_id
              , c.subject_section_name
              , a.contents_name
              , a.enable
              , a.display_order
            FROM
                tbl_contents a
            LEFT JOIN
                mst_contents_category b
            USING
                (contents_category_id)
            LEFT JOIN
                tbl_subject_section c
            USING
                (subject_section_id)
            ORDER BY
                contents_id ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findContentsOrderByCategoryName($data)
    {
        $sql = '
            SELECT
                contents_id
              , a.contents_category_id
              , b.category_name
              , a.subject_section_id
              , c.subject_section_name
              , a.contents_name
              , a.enable
              , a.display_order
            FROM
                tbl_contents a
            LEFT JOIN
                mst_contents_category b
            USING
                (contents_category_id)
            LEFT JOIN
                tbl_subject_section c
            USING
                (subject_section_id)
            ORDER BY
                category_name ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findContentsOrderBySubjectSectionName($data)
    {
        $sql = '
            SELECT
                contents_id
              , a.contents_category_id
              , b.category_name
              , a.subject_section_id
              , c.subject_section_name
              , a.contents_name
              , a.enable
              , a.display_order
            FROM
                tbl_contents a
            LEFT JOIN
                mst_contents_category b
            USING
                (contents_category_id)
            LEFT JOIN
                tbl_subject_section c
            USING
                (subject_section_id)
            ORDER BY
                subject_section_name ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findContentsOrderByContentsName($data)
    {
        $sql = '
            SELECT
                contents_id
              , a.contents_category_id
              , b.category_name
              , a.subject_section_id
              , c.subject_section_name
              , a.contents_name
              , a.enable
              , a.display_order
            FROM
                tbl_contents a
            LEFT JOIN
                mst_contents_category b
            USING
                (contents_category_id)
            LEFT JOIN
                tbl_subject_section c
            USING
                (subject_section_id)
            ORDER BY
                contents_name ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findSubjectSection($data)
    {
        $sql = 'SELECT contents_id
                     , a.contents_category_id
                     , b.contents_category_name
                     , a.subject_section_id
                     , c.subject_section_name
                     , a.contents_name
                     , a.enable
                     , a.display_order
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b USING (contents_category_id)
             LEFT JOIN tbl_subject_section c USING (subject_section_id)
                 WHERE c.subject_section_id ' . $data['subject_section_id'] . '
              ORDER BY a.display_order ASC';

        return $this->fetchAll($sql,
            array(
                ':subject_section_id' => $data['subject_section_id']
            )
        );
    }

    public function findSubjectSectionEnable($data)
    {
        $sql = 'SELECT contents_id
                     , a.contents_category_id
                     , b.contents_category_name
                     , a.subject_section_id
                     , c.subject_section_name
                     , a.contents_name
                     , a.enable
                     , a.display_order
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b USING (contents_category_id)
             LEFT JOIN tbl_subject_section c USING (subject_section_id)
                 WHERE c.subject_section_id = :subject_section_id
                   AND a.enable = 1
              ORDER BY a.display_order ASC';

        return $this->fetchAll($sql,
            array(
                ':subject_section_id' => $data['subject_section_id']
            )
        );
    }

    public function updateContentsDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_contents
            SET
                display_order = :display_order
            WHERE
                contents_id = :contents_id
        ';

        return $this->execute($sql,
            array(
                ':display_order' => $data['display_order']
              , ':contents_id' => $data['contents_id']
            ));
    }

    public function updateContents($data)
    {
        $sql = '
            UPDATE
                tbl_contents
            SET
                contents_category_id = :contents_category_id
              , subject_section_id = :subject_section_id
              , contents_name = :contents_name
              , comment = :comment
              , first_day = :first_day
              , last_day = :last_day
              , enable = :enable
              , display_order = :display_order
            WHERE
                contents_id = :contents_id
        ';

        return $this->execute($sql,
            array(
                ':contents_category_id' => $data['contents_category_id']
              , ':subject_section_id' => $data['subject_section_id']
              , ':contents_name' => $data['contents_name']
              , ':comment' => $data['comment']
              , ':first_day' => $data['first_day']
              , ':last_day' => $data['last_day']
              , ':enable' => $data['enable']
              , ':display_order' => $data['display_order']
              , ':contents_id' => $data['contents_id']
            ));
    }

    public function insertContents($data)
    {
        $sql = '
            INSERT INTO
                tbl_contents (
                    contents_category_id
                  , subject_section_id
                  , contents_name
                  , comment
                  , first_day
                  , last_day
                  , file_name
                  , user_level_id
                  , register_id
                  , register_datetime
                  , contents_extension_id
                  , size
                  , enable
                  , display_order
                ) VALUES (
                    :contents_category_id
                  , :subject_section_id
                  , :contents_name
                  , :comment
                  , :first_day
                  , :last_day
                  , :file_name
                  , :user_level_id
                  , :register_id
                  , NOW()
                  , :contents_extension_id
                  , :size
                  , :enable
                  , :display_order
                )
        ';

        return $this->execute($sql,
            array(
                ':contents_category_id' => $data['contents_category_id']
              , ':subject_section_id' => $data['subject_section_id']
              , ':contents_name' => $data['contents_name']
              , ':comment' => $data['comment']
              , ':first_day' => $data['first_day']
              , ':last_day' => $data['last_day']
              , ':file_name' => $data['file_name']
              , ':user_level_id' => $data['user_level_id']
              , ':register_id' => $data['register_id']
              , ':contents_extension_id' => $data['contents_extension_id']
              , ':size' => $data['size']
              , ':enable' => $data['enable']
              , ':display_order' => $data['display_order']
            ));
    }


}
