<?php
require_once '../config.php';

class GetStudentInfoModel extends PdoBase
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
        $sql = 'SELECT classroom_id
                  FROM tbl_student_classroom
                 WHERE student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    public function getCourseId($data)
    {
        $sql = 'SELECT course_id
                  FROM tbl_student_course
                 WHERE student_id = :student_id
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
              GROUP BY subject_section_id
        ';

        return $this->fetchAll($sql);
    }

    public function getClassroomSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_classroom_subject_section 
                 WHERE classroom_id ' . $data['classroom_id'] . '
                    OR classroom_id IS NULL
              GROUP BY subject_section_id
        ';

        return $this->fetchAll($sql);
    }

    public function getCourseSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_course_subject_section 
                 WHERE course_id ' . $data['course_id'] . '
                    OR course_id IS NULL
              GROUP BY subject_section_id
        ';

        return $this->fetchAll($sql);
    }

    public function getSubjectSectionId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_subject_section
                 WHERE subject_section_id ' . $data['grade_id'] . '
                   AND subject_section_id ' . $data['classroom_id'] . '
                   AND subject_section_id ' . $data['course_id'] . '
                    OR subject_section_id IS NULL
              ORDER BY display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function getContents($data)
    {
        $sql = 'SELECT 0 as type
                     , contents_id as primary_key
                     , a.contents_name as title
                     , a.register_datetime
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b
                 USING (contents_category_id)
             LEFT JOIN tbl_subject_section c
                    ON a.subject_section_id = c.subject_section_id
             LEFT JOIN tbl_subject_genre d
                    ON c.subject_genre_id = d.subject_genre_id
             LEFT JOIN tbl_subject_group e
                    ON d.subject_group_id = e.subject_group_id
                 WHERE c.subject_section_id ' . $data['subject_section_id'] . '
                   AND(
                          (NOW() - INTERVAL 1 MONTH) < a.first_day
                       OR
                          (a.first_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
                   AND(
                           NOW() < a.last_day
                       OR
                          (a.last_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
                   AND a.enable = 1
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getQuestionnaire($data)
    {
        $sql = 'SELECT (1+:type) as type
                     , a.questionnaire_id as primary_key
                     , a.title
                     , CASE
                       WHEN a.register_datetime < a.start_day THEN
                         a.start_day
                       WHEN a.register_datetime > a.start_day THEN
                         a.register_datetime
                       END as register_datetime
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
                   AND(
                          (NOW() - INTERVAL 1 MONTH) < a.start_day
                       OR
                          (a.start_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
                   AND(
                           NOW() < a.last_day
                       OR
                          (a.last_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
              GROUP BY a.questionnaire_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':type' => $data['type']
        ));
    }

    public function getQuiz($data)
    {
        $sql = 'SELECT 3 as type
                     , a.quiz_id as primary_key
                     , a.title
                     , CASE
                       WHEN a.register_datetime < a.start_day THEN
                         a.start_day
                       WHEN a.register_datetime > a.start_day THEN
                         a.register_datetime
                       END as register_datetime
                  FROM tbl_quiz as a
             LEFT JOIN tbl_quiz_target_access_restriction as b
                 USING (quiz_id)
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
                   AND(
                          (NOW() - INTERVAL 1 MONTH) < a.start_day
                       OR
                          (a.start_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
                   AND(
                           NOW() < a.last_day
                       OR
                          (a.last_day IS NULL AND (NOW() - INTERVAL 1 MONTH) < a.register_datetime)
                      )
              GROUP BY a.quiz_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getMessage($data)
    {
        $sql = 'SELECT 4 as type
                     , a.type as message_type
                     , a.message_id as primary_key
                     , a.title
                     , MAX(b.register_datetime) as register_datetime
                     , a.message_level
                     , MAX(b.register_datetime) < d.check_date AND d.check_date IS NOT NULL as check_flag
                  FROM tbl_message as a
             LEFT JOIN tbl_message_detail as b
                    ON b.message_id = a.message_id
                   AND b.enable = 1
             LEFT JOIN tbl_message_target as c
                 USING (message_detail_id)
             LEFT JOIN tbl_message_check as d
                    ON a.message_id = d.message_id
                   AND d.user_level_id = 3
                   AND d.user_id = :student_id
                 WHERE (
                           (b.send_user_level_id = 3 AND b.send_user_id = :student_id) OR
                           (a.auther_user_level_id = 3 AND a.auther_user_id = :student_id) OR
                           (c.receive_user_level_id = 3 AND c.receive_user_id = :student_id)
                       OR
                           (c.grade_id ' . $data['grade_id'] . ' OR c.grade_id = 0 ) AND
                           (c.classroom_id ' . $data['classroom_id'] . ' OR c.classroom_id = 0) AND
                           (c.course_id ' . $data['course_id'] . ' OR c.course_id = 0) AND
                           (c.receive_user_level_id = 0 AND c.receive_user_id = 0 )
                       )
                   AND a.school_id = :school_id
                   AND b.enable = 1
                   AND a.enable = 1
                   AND (NOW() < a.limit_date OR (NOW() - INTERVAL 1 WEEK) < b.register_datetime)
              GROUP BY a.message_id
              ORDER BY message_level ASC
                     , register_datetime DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':student_id' => $data['student_id']
        ));
    }

}
