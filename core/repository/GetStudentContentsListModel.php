<?php
require_once '../config.php';

class GetStudentContentsListModel extends PdoBase
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
              ORDER BY display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function getContents($data)
    {
        $sql = 'SELECT 0 as type
                     , a.contents_id as primary_key
                     , a.contents_name as title
                     , a.register_datetime
                     , a.contents_extension_id
                     , a.subject_section_id
                     , COUNT( f.history_id ) as watch_count
                     , COUNT( DISTINCT g.contents_id ) as attach_flg
                     , MAX(f.proportion) as proportion
                     , (CASE WHEN f.play_start_datetime is null THEN \'0000-00-00 00:00:00\'
                       WHEN f.play_start_datetime is not null THEN MAX(f.play_start_datetime) END) as play_start_datetime
                     , a.first_day
                     , a.last_day
                     , a.bit_classroom
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b
                 USING (contents_category_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
             LEFT JOIN log_contents_history_student f
                    ON f.school_contents_id = a.contents_id
                   AND f.student_id = :student_id
             LEFT JOIN tbl_contents_attachment g
                    ON g.contents_id = a.contents_id
                 WHERE a.bit_classroom = :bit_classroom
                   AND a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.contents_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':student_id' => $data['student_id']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    public function getContentsAttachment($data)
    {
        $sql = 'SELECT b.contents_attachment_id
                     , b.file_name
                  FROM tbl_contents a
            INNER JOIN tbl_contents_attachment b
                    ON b.contents_id = a.contents_id
                 WHERE a.contents_id = :contents_id
        ';

        return $this->fetchAll($sql, array(
            ':contents_id' => $data['contents_id']
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
                     , a.last_day
                     , d.subject_section_id
                     , CASE
                       WHEN e.student_id = :student_id THEN
                         true
                       ELSE
                         false
                       END as answer_flg
                     , a.start_day
                     , a.last_day
                     , a.bit_classroom
                  FROM tbl_questionnaire as a
             LEFT JOIN tbl_questionnaire_target_access_restriction as b
                 USING (questionnaire_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
             LEFT JOIN tbl_questionnaire_target_range_school as d
                 USING (questionnaire_id)
             LEFT JOIN tbl_questionnaire_answer as e
                    ON e.questionnaire_id = a.questionnaire_id
                   AND e.student_id = :student_id
                   AND e.enable = 1
                 WHERE a.bit_classroom = :bit_classroom
                   AND a.school_id = :school_id
                   AND a.enable = 1
                   AND a.type = :type
                   AND NOW() <= a.last_day
              GROUP BY a.questionnaire_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':student_id' => $data['student_id']
          , ':type' => $data['type']
          , ':bit_classroom' => $data['bit_classroom']
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
                     , a.start_day
                     , a.last_day
                     , a.subject_section_id
                     , a.repeat_challenge
                     , count(d.quiz_id) as answer_count
                     , a.qualifying_score
                     , 0 as last_answer_id
                     , a.bit_classroom
                  FROM tbl_quiz as a
             LEFT JOIN tbl_quiz_target_access_restriction as b
                 USING (quiz_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
             LEFT JOIN tbl_quiz_answer as d
                    ON a.quiz_id = d.quiz_id
                   AND d.student_id = :student_id
                 WHERE a.bit_classroom = :bit_classroom
                   AND a.school_id = :school_id
                   AND a.enable = 1
                   AND NOW() <= a.last_day
              GROUP BY a.quiz_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':bit_classroom' => $data['bit_classroom']
          , ':student_id' => $data['student_id']
        ));
    }

    public function getQuizAnswer($data)
    {
        $sql = 'SELECT a.quiz_id
                     , b.answer_id
                     , a.qualifying_score
                     , b.total_score
                     , CASE
                       WHEN a.qualifying_score = 0 THEN
                         "判定無し"
                       WHEN a.qualifying_score < b.total_score THEN
                         "合格"
                       WHEN a.qualifying_score > b.total_score THEN
                         "不合格"
                       END as result
                     , CASE
                       WHEN b.register_datetime = 0 THEN
                         0
                       WHEN b.register_datetime != 0 THEN
                         1
                       END as end_flag
                  FROM tbl_quiz a
            INNER JOIN tbl_quiz_answer b
                    ON b.quiz_id = a.quiz_id
                 WHERE b.student_id = :student_id
                   AND a.quiz_id = :quiz_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':quiz_id' => $data['quiz_id']
        ));
    }

    //public function getMessage($data)
    //{
    //    $sql = 'SELECT 4 as type
    //                 , b.message_id as primary_key
    //                 , b.title
    //                 , MAX(a.register_datetime) as register_datetime
    //              FROM tbl_message_detail as a
    //         LEFT JOIN tbl_message as b
    //                ON a.message_id = b.message_id
    //         LEFT JOIN tbl_message_target as c
    //             USING (message_detail_id)
    //         LEFT JOIN tbl_school d
    //             USING (school_id)
    //             WHERE(
    //                  (a.send_user_level_id = 3
    //               AND a.send_user_id = :student_id)
    //                OR(b.auther_user_level_id = 3
    //               AND b.auther_user_id = :student_id)
    //                OR(c.receive_user_level_id = 3
    //               AND c.receive_user_id = :student_id)
    //                  )
    //               AND(c.grade_id ' . $data['grade_id'] . '
    //                OR c.grade_id = 0)
    //               AND(c.classroom_id ' . $data['classroom_id'] . '
    //                OR c.classroom_id = 0)
    //               AND(c.course_id ' . $data['course_id'] . '
    //                OR c.course_id = 0)
    //               AND b.school_id = :school_id
    //               AND b.enable = 1
    //               AND(NOW() - INTERVAL 1 WEEK) < a.register_datetime
    //          GROUP BY b.message_id
    //    ';

    //    return $this->fetchAll($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':student_id' => $data['student_id']
    //    ));
    //}

    public function getSubject($data)
    {
        $sql = 'SELECT a.subject_genre_id
                     , a.subject_genre_name
                  FROM tbl_subject_genre as a
             LEFT JOIN tbl_subject_section as b
                 USING(subject_genre_id)
                 WHERE b.subject_section_id ' . $data['grade_id'] . '
                   AND b.subject_section_id ' . $data['classroom_id'] . '
                   AND b.subject_section_id ' . $data['course_id'] . '
              GROUP BY a.subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql);
    }

    public function getSubjectSection($data)
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                  FROM tbl_subject_section as a
                 WHERE a.subject_section_id ' . $data['grade_id'] . '
                   AND a.subject_section_id ' . $data['classroom_id'] . '
                   AND a.subject_section_id ' . $data['course_id'] . '
                   AND a.subject_genre_id = :subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

}
