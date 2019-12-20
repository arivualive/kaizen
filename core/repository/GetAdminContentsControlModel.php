<?php
require_once '../config.php';

class GetAdminContentsControlModel extends PdoBase
{
    public function getContents($data)
    {
        $sql = 'SELECT 0 as type
                     , a.contents_id as primary_key
                     , a.contents_name as title
                     , a.register_datetime
                     , a.contents_extension_id
                     , a.subject_section_id
                     , c.proportion as complate_proportion
                     , a.first_day
                     , a.last_day
                     , f.display_order
                     , a.bit_classroom
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b
                 USING (contents_category_id)
             LEFT JOIN tbl_subject_section c
                    ON a.subject_section_id = c.subject_section_id
             LEFT JOIN tbl_subject_genre d
                    ON c.subject_genre_id = d.subject_genre_id
             LEFT JOIN tbl_subject_group e
                    ON d.subject_group_id = e.subject_group_id
             LEFT JOIN tbl_function_list f
                    ON f.type = 0
                   AND f.primary_id = a.contents_id
                 WHERE e.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.contents_id
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
                     , b.subject_section_id
                     , a.start_day
                     , a.last_day
                     , c.display_order
                     , a.bit_classroom
                  FROM tbl_questionnaire as a
             LEFT JOIN tbl_questionnaire_target_range_school as b
                 USING (questionnaire_id)
             LEFT JOIN tbl_function_list c
                    ON c.type = (1+:type)
                   AND c.primary_id = a.questionnaire_id
                 WHERE a.school_id = :school_id
                   AND a.type = :type
                   AND a.enable = 1
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
                     , a.start_day
                     , a.last_day
                     , a.subject_section_id
                     , a.repeat_challenge
                     , a.qualifying_score
                     , e.display_order
                     , a.bit_classroom
                  FROM tbl_quiz as a
             LEFT JOIN tbl_quiz_target_access_restriction as b
                 USING (quiz_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
             LEFT JOIN tbl_quiz_answer as d
                    ON a.quiz_id = d.quiz_id
             LEFT JOIN tbl_function_list e
                    ON e.type = 3
                   AND e.primary_id = a.quiz_id
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.quiz_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getSubject($data)
    {
        $sql = 'SELECT a.subject_genre_id
                     , a.subject_genre_name
                  FROM tbl_subject_genre as a
             LEFT JOIN tbl_subject_group as b
                 USING(subject_group_id)
                 WHERE b.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getSubjectSection($data)
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                  FROM tbl_subject_section as a
                 WHERE a.subject_genre_id = :subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

}
