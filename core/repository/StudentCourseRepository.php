<?php
require_once '../config.php';

class StudentCourseRepository extends PdoBase
{

    public function findAll()
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_course
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
                tbl_student_course
            WHERE
                student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function findCourseId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_student_course
            WHERE
                course_id = :course_id
        ';

        return $this->fetch($sql, array(
            ':course_id' => $data['course_id']
        ));
    }

    public function deleteStudentCourse($data)
    {
        $sql = 'DELETE FROM tbl_student_course WHERE student_id = :student_id';

        return $this->exec($sql, array(':student_id' => $data['student_id']));
    }

    public function updateStudentCourseId($data)
    {
        $sql = '
            UPDATE
                tbl_student_course
            SET
                course_id = :course_id
            WHERE
                student_id = :student_id
        ';

        return $this->exec($sql, array(
            ':course_id' => $data['course_id']
          , ':student_id' => $data['student_id']
        ));
    }

    public function insertStudentCourse($data)
    {
        $sql = '
            INSERT INTO
                tbl_student_course (
                    student_id
                  , course_id
                ) VALUES (
                    :student_id
                  , :course_id
                )
        ';

        return $this->exec($sql, array(
            ':student_id' => $data['student_id']
          , ':course_id' => $data['course_id']
        ));
    }
}
