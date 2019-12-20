<?php
require_once '../config.php';

class GetStudentAccessModel extends PdoBase
{

    public function getGradeId($data)
    {
        $sql = 'SELECT grade_id
                  FROM tbl_student_grade
                 WHERE student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function getClassroomId($data)
    {
        $sql = '
            SELECT
                classroom_id
            FROM
                tbl_student_classroom
            WHERE
                student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function getCourseId($data)
    {
        $sql = '
            SELECT
                course_id
            FROM
                tbl_student_course
            WHERE
                student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function getGradeSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_grade_subject_section 
                 WHERE grade_id ' . $data['grade_id'] . '
                    OR grade_id IS NULL
              GROUP BY subject_section_id';

        return $this->fetchAll($sql);
    }

    public function getClassroomSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_classroom_subject_section 
                 WHERE classroom_id ' . $data['classroom_id'] . '
                    OR classroom_id IS NULL
              GROUP BY subject_section_id';

        return $this->fetchAll($sql);
    }

    public function getCourseSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_course_subject_section 
                 WHERE course_id ' . $data['course_id'] . '
                    OR course_id IS NULL
              GROUP BY subject_section_id';

        return $this->fetchAll($sql);
    }

    public function getSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_subject_section
                 WHERE subject_section_id ' . $data['grade_id'] . '
                   AND subject_section_id ' . $data['classroom_id'] . '
                   AND subject_section_id ' . $data['course_id'] . '
              ORDER BY display_order ASC';

        return $this->fetchAll($sql);
    }

    public function getContentsCount($data)
    {
        $sql = 'SELECT count(*) as count
                  FROM tbl_contents
                 WHERE subject_section_id ' . $data['subject_section_id'] . '
                   AND enable = 1
        ';

        return $this->fetch($sql);
    }

    public function getContentsOffset($data)
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
             LEFT JOIN mst_contents_category b
                 USING (contents_category_id)
             LEFT JOIN tbl_subject_section c
                 USING (subject_section_id)
                 WHERE c.subject_section_id ' . $data['subject_section_id'] . '
                   AND a.enable = 1
              ORDER BY display_order ASC
                 LIMIT :limit
                OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getQuestionnaireCount($data)
    {
        $sql = 'SELECT count(*) as count
                  FROM tbl_questionnaire as a
             LEFT JOIN tbl_questionnaire_target_access_restriction as b
                 USING (questionnaire_id)
                 WHERE(b.grade_id ' . $data['grade_id'] . '
                    OR b.grade_id = 0)
                   AND(b.classroom_id ' . $data['classroom_id'] . '
                    OR b.classroom_id = 0)
                   AND(b.course_id ' . $data['course_id'] . '
                    OR b.course_id = 0)
                   AND a.school_id = :school_id
                   AND a.enable = 1
                   AND a.type = :type
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
          , ':type' => $data['type']
        ));
    }

    public function getQuestionnaireOffset($data)
    {
        $sql = 'SELECT a.questionnaire_id
                     , a.school_id
                     , c.school_name
                     , a.title
                     , a.description
                     , a.finished_message
                     , a.enable
                     , a.display_order
                     , a.start_day
                     , a.last_day
                     , a.user_level_id
                     , a.register_user_id
                     , a.register_datetime
                     , a.type
                  FROM tbl_questionnaire as a
             LEFT JOIN tbl_questionnaire_target_access_restriction as b
                 USING (questionnaire_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
                 WHERE(b.grade_id ' . $data['grade_id'] . '
                    OR b.grade_id = 0)
                   AND(b.classroom_id ' . $data['classroom_id'] . '
                    OR b.classroom_id = 0)
                   AND(b.course_id ' . $data['course_id'] . '
                    OR b.course_id = 0)
                   AND a.school_id = :school_id
                   AND a.enable = 1
                   AND a.type = :type
              GROUP BY a.questionnaire_id
              ORDER BY display_order ASC
                 LIMIT :limit
                OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':school_id', (int) $data['school_id'], PDO::PARAM_INT);
        $stmt->bindValue(':type', (int) $data['type'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getMessageCount($data)
    {
        $sql = 'SELECT count(*) as count
                  FROM tbl_message as a
             LEFT JOIN tbl_message_target as b
                 USING (message_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
                 WHERE(a.auther_user_level_id = 3
                   AND a.auther_user_id = 591)
                    OR(b.receive_user_level_id = 3
                   AND b.receive_user_id = 591)
                    OR(b.grade_id IN (21)
                   AND b.classroom_id IN (13,14,19,20,22,23,24,25,26,27,28,29,30,31)
                   AND b.course_id IN (21))
                   AND a.school_id = :school_id
                   AND a.enable = 1
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getMessageOffset($data)
    {
        $sql = 'SELECT a.message_id
                     , a.school_id
                     , c.school_name
                     , a.title
                     , a.auther_user_level_id
                     , a.auther_user_id
                     , a.post_date
                     , a.type
                     , a.enable
                  FROM tbl_message as a
             LEFT JOIN tbl_message_target as b
                 USING (message_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
                 WHERE(a.auther_user_level_id = 3
                   AND a.auther_user_id = 591)
                    OR(b.receive_user_level_id = 3
                   AND b.receive_user_id = 591)
                    OR(b.grade_id IN (21)
                   AND b.classroom_id IN (13,14,19,20,22,23,24,25,26,27,28,29,30,31)
                   AND b.course_id IN (21))
                   AND a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.message_id
                 LIMIT :limit
                OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':school_id', (int) $data['school_id'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

}
