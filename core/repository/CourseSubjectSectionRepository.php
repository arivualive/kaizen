<?php
require_once '../config.php';

class CourseSubjectSectionRepository extends PdoBase
{

    public function findAllCourseId($data)
    {
        $sql = 'SELECT course_id
                     , subject_section_id
                  FROM tbl_course_subject_section 
                 WHERE course_id = :course_id';

        return $this->fetchAll($sql, array(':course_id' => $data['course_id']));
    }

    public function findAllSubjectSectionId($data)
    {
        $sql = 'SELECT course_id
                     , subject_section_id
                  FROM tbl_course_subject_section 
                 WHERE subject_section_id = :subject_section_id';

        return $this->fetchAll($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function findCourseId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_course_subject_section 
                 WHERE course_id = :course_id';

        return $this->fetchAll($sql, array(':course_id' => $data['course_id']));
    }

    public function deleteSubjectSection($data)
    {
        $sql = 'DELETE
                  FROM tbl_course_subject_section
                 WHERE subject_section_id = :subject_section_id';

        return $this->exec($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function insertSubjectSection($data)
    {
        $result = 0;

        $sql = 'INSERT 
                  INTO tbl_course_subject_section (course_id, subject_section_id)
                 VALUE (:course_id, :subject_section_id)';

        foreach ($data['course_id'] as $value) {
           $result += $this->exec($sql,
                array(
                    ':course_id' => $value,
                    ':subject_section_id' => $data['subject_section_id']
                )
            );
        }

        return $result;
    }
}
