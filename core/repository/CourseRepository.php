<?php
require_once '../config.php';

class CourseRepository extends PdoBase
{
    public function findCourseAll() {
        $sql = '
            SELECT
                * 
            FROM
                tbl_course 
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findCourseEnable() {
        $sql = 'SELECT * 
                  FROM tbl_course 
              ORDER BY display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findCourseSchool($data) {
        $sql = '
            SELECT
                * 
            FROM
                tbl_course 
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

    public function findCourseId($data) {
        $sql = '
            SELECT
                * 
            FROM
                tbl_course where course_id = :id
        ';

        return $this->fetch($sql, array(':id' => $data['id']));
    }

    public function updateCourseDisplayOrder($data) {
        $sql = '
            UPDATE
                tbl_course
            SET
                display_order = :display_order
            WHERE
                course_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':id' => $data['id']
        ));
    }

    public function updateCourseId($data)
    {
        $sql = '
            UPDATE
                tbl_course
            SET
                school_id = :school_id
              , course_name = :course_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                course_id = :course_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':course_name' => $data['course_name']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':course_id' => $data['course_id']
        ));
    }

    public function insertCourse($data)
    {
        $sql = '
        insert into tbl_course (
            school_id
          , course_name
          , enable
          , display_order
            ) values (
            :school_id
          , :course_name
          , :enable
          , :display_order
        )';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':course_name' => $data['course_name']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }
}
