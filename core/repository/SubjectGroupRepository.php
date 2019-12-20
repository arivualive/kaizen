<?php
require_once '../config.php';

class SubjectGroupRepository extends PdoBase
{

    public function findSubjectGroupAll()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_group
            ORDER BY
                contents_category_id ASC
              , display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findSubjectGroupEnable()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_group
            WHERE
                enable = 1
            ORDER BY
                contents_category_id ASC
              , display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findSubjectGroupId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_group
            WHERE
                subject_group_id = :subject_group_id
        ';

        return $this->fetch($sql, array(
            ':subject_group_id' => $data['subject_group_id']
        ));
    }

    public function updateSubjectGroupId($data)
    {
        $sql = '
            UPDATE
                tbl_subject_group
            SET
                school_id = :school_id
              , contents_category_id = :contents_category_id
              , subject_group_name = :subject_group_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                subject_group_id = :subject_group_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':contents_category_id' => $data['contents_category_id']
          , ':subject_group_name' => $data['subject_group_name']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':subject_group_id' => $data['subject_group_id']
        ));
    }

    public function updateSubjectGroupDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_subject_group
            SET
                display_order = :display_order
            WHERE
                subject_group_id = :subject_group_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':subject_group_id' => $data['subject_group_id']
        ));
    }

    public function insertSubjectGroup($data)
    {
        $sql = '
            INSERT INTO
                tbl_subject_group (
                    school_id
                  , contents_category_id
                  , subject_group_name
                  , display_order
                    enable
                ) VALUES (
                    :school_id
                  , :contents_category_id
                  , :subject_group_name
                  , :display_order
                  , :enable
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':contents_category_id' => $data['contents_category_id']
          , ':subject_group_name' => $data['subject_group_name']
          , ':display_order' => $data['display_order']
          , ':enable' => 1
        ));
    }
}
