<?php
require_once '../config.php';

class SchoolRepository extends PdoBase
{
    public function findSchoolAll() {
        $sql = '
            SELECT
                *
            FROM
                tbl_school
        ';

        return $this->fetchAll($sql);
    }

    public function findSchoolName() {
        $sql = '
            SELECT
                school_id
              , school_name
            FROM
                tbl_school
        ';

        return $this->fetchAll($sql);
    }

    public function findSchoolId($data) {
        $sql = '
            SELECT
                *
            FROM
                tbl_school
            WHERE
                school_id = :id
        ';

        return $this->fetch($sql, array(':id' => $data['id']));
    }

    public function updateSchoolDisplayOrder($data) {
        $sql = '
            UPDATE
                tbl_school
            SET
                display_order = :display_order
            WHERE
                school_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':id' => $data['id']
        ));
    }

    public function insertSchool($data) {
        $sql = '
            INSERT INTO
                tbl_school (
                    school_id
                  , school_name
                  , enable
                ) VALUES (
                    :school_id
                  , :school_name
                  , :enable
                )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':school_name' => $data['school_name']
          , ':enable' => 1
        ));
    }
}
