<?php
require_once '../config.php';

class StudentGradeRepository extends PdoBase
{

    public function findAll()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_grade
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
                tbl_student_grade
            WHERE
                student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function findGradeId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_grade
            WHERE
                grade_id = :grade_id
        ';

        return $this->fetch($sql, array(
            ':grade_id' => $data['grade_id']
        ));
    }

    public function deleteStudentGrade($data)
    {
        $sql = 'DELETE FROM tbl_student_grade WHERE student_id = :student_id';

        return $this->exec($sql, array(':student_id' => $data['student_id']));
    }

    public function updateStudentGradeId($data)
    {
        $sql = '
            UPDATE
                tbl_student_grade
            SET
                grade_id = :grade_id
            WHERE
                student_id = :student_id
        ';

        return $this->exec($sql, array(
            ':grade_id' => $data['grade_id']
          , ':student_id' => $data['student_id']
        ));
    }

    public function insertStudentGrade($data)
    {
        $sql = '
            INSERT INTO
                tbl_student_grade (
                    student_id
                  , grade_id
                ) VALUES (
                    :student_id
                  , :grade_id
                )
        ';

        return $this->exec($sql, array(
            ':student_id' => $data['student_id']
          , ':grade_id' => $data['grade_id']
        ));
    }
}
