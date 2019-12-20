<?php
require_once '../config.php';

class StudentClassroomRepository extends PdoBase
{

    public function findAll()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_classroom
            ORDER BY
                student_id ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findStudentId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_classroom
            WHERE
                student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function findClassroomId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_classroom
            WHERE
                classroom_id = :classroom_id
        ';

        return $this->fetch($sql, array(
            ':classroom_id' => $data['classroom_id']
        ));
    }

    public function deleteStudentClassroom($data)
    {
        $sql = 'DELETE FROM tbl_student_classroom WHERE student_id = :student_id';

        return $this->exec($sql, array(':student_id' => $data['student_id']));
    }

    public function updateStudentClassroomId($data)
    {
        $sql = '
            UPDATE
                tbl_student_classroom
            SET
                classroom_id = :classroom_id
            WHERE
                student_id = :student_id
        ';

        return $this->exec($sql, array(
            ':classroom_id' => $data['classroom_id']
          , ':student_id' => $data['student_id']
        ));
    }

    public function insertStudentClassroom($data)
    {
        $sql = '
            INSERT INTO
                tbl_student_classroom (
                    student_id
                  , classroom_id
                ) VALUES (
                    :student_id
                  , :classroom_id
                )
        ';

        return $this->exec($sql, array(
            ':student_id' => $data['student_id']
          , ':classroom_id' => $data['classroom_id']
        ));
    }
}
