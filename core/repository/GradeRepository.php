<?php
require_once '../config.php';

class GradeRepository extends PdoBase
{

    public function findGradeAll()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_grade
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findGradeEnable()
    {
        $sql = 'SELECT *
                  FROM tbl_grade
                 WHERE enable = 1
              ORDER BY display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findGradeSchool($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_grade
            WHERE
                school_id = :school_id
            AND
                enable = 1
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function findGradeId($data)
    {
        $sql = '
            SELECT
                * 
            FROM
                tbl_grade 
            WHERE
                grade_id = :grade_id
        ';

        return $this->fetch($sql, array(
            ':grade_id' => $data['grade_id']
        ));
    }

    public function updateGradeId($data)
    {
        $sql = '
            UPDATE
                tbl_grade
            SET
                school_id = :school_id
              , grade_name = :grade_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                grade_id = :grade_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':grade_name' => $data['grade_name']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':grade_id' => $data['grade_id']
        ));
    }

    public function updateGradeDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_grade
            SET
                display_order = :display_order
            WHERE
                grade_id = :grade_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':grade_id' => $data['grade_id']
        ));
    }

    public function insertGrade($data)
    {
        $sql = '
            INSERT INTO tbl_grade (
                school_id
              , grade_name
              , enable
              , display_order
            ) VALUES (
                :school_id
              , :grade_name
              , :enable
              , :display_order
            )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':grade_name' => $data['grade_name']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }
}
