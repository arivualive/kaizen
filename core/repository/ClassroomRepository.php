<?php
require_once '../config.php';

class ClassroomRepository extends PdoBase
{

    public function findClassroomAll()
    {
        $sql = '
            SELECT
                * 
            FROM
                tbl_classroom 
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findClassroomEnable()
    {
        $sql = 'SELECT * 
                  FROM tbl_classroom 
                 WHERE enable = 1
              ORDER BY display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findClassroomSchool($data)
    {
        $sql = '
            SELECT
                * 
            FROM
                tbl_classroom 
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

    public function findClassroomId($data)
    {
        $sql = '
            SELECT
                * 
            FROM
                tbl_classroom where classroom_id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    public function updateClassroomId($data)
    {
        $sql = '
            UPDATE
                tbl_classroom
            SET
                school_id = :school_id
              , classroom_name = :classroom_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                classroom_id = :classroom_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':classroom_name' => $data['classroom_name']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':classroom_id' => $data['classroom_id']
        ));
    }

    public function updateClassroomDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_classroom
            SET
                display_order = :display_order
            WHERE
                classroom_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':id' => $data['id']
        ));
    }

    public function insertClassroom($data)
    {
        $sql = '
            INSERT INTO
                tbl_classroom (
                    school_id
                  , classroom_name
                  , enable
                  , display_order
                ) VALUES (
                    :school_id
                  , :classroom_name
                  , :enable 
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':classroom_name' => $data['classroom_name']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }
}
