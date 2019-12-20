<?php
require_once '../config.php';

class GradeSubjectSectionRepository extends PdoBase
{

    public function findAllGradeId($data)
    {
        $sql = 'SELECT grade_id
                     , subject_section_id
                  FROM tbl_grade_subject_section 
                 WHERE grade_id = :grade_id';

        return $this->fetchAll($sql, array(':grade_id' => $data['grade_id']));
    }

    public function findAllSubjectSectionId($data)
    {
        $sql = 'SELECT grade_id
                     , subject_section_id
                  FROM tbl_grade_subject_section 
                 WHERE subject_section_id = :subject_section_id';

        return $this->fetchAll($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function findGradeId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_grade_subject_section 
                 WHERE grade_id = :grade_id';

        return $this->fetchAll($sql, array(':grade_id' => $data['grade_id']));
    }

    public function deleteSubjectSection($data)
    {
        $sql = 'DELETE
                  FROM tbl_grade_subject_section
                 WHERE subject_section_id = :subject_section_id';

        return $this->exec($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function insertSubjectSection($data)
    {
        $result = 0;

        $sql = 'INSERT 
                  INTO tbl_grade_subject_section (grade_id, subject_section_id)
                 VALUE (:grade_id, :subject_section_id)';

        foreach ($data['grade_id'] as $value) {
           $result += $this->exec($sql,
                array(
                    ':grade_id' => $value,
                    ':subject_section_id' => $data['subject_section_id']
                )
            );
        }

        return $result;
    }
}
