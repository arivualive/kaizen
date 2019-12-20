<?php
require_once '../config.php';

class ClassroomSubjectSectionRepository extends PdoBase
{

    public function findAllClassroomId($data)
    {
        $sql = 'SELECT classroom_id
                     , subject_section_id
                  FROM tbl_classroom_subject_section 
                 WHERE classroom_id = :classroom_id';

        return $this->fetchAll($sql, array(':classroom_id' => $data['classroom_id']));
    }

    public function findAllSubjectSectionId($data)
    {
        $sql = 'SELECT classroom_id
                     , subject_section_id
                  FROM tbl_classroom_subject_section 
                 WHERE subject_section_id = :subject_section_id';

        return $this->fetchAll($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function findClassroomId($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_classroom_subject_section 
                 WHERE classroom_id = :classroom_id';

        return $this->fetchAll($sql, array(':classroom_id' => $data['classroom_id']));
    }

    public function deleteSubjectSection($data)
    {
        $sql = 'DELETE
                  FROM tbl_classroom_subject_section
                 WHERE subject_section_id = :subject_section_id';

        return $this->exec($sql, array(':subject_section_id' => $data['subject_section_id']));
    }

    public function insertSubjectSection($data)
    {
        $result = 0;

        $sql = 'INSERT 
                  INTO tbl_classroom_subject_section (classroom_id, subject_section_id)
                 VALUE (:classroom_id, :subject_section_id)';

        foreach ($data['classroom_id'] as $value) {
           $result += $this->exec($sql,
                array(
                    ':classroom_id' => $value,
                    ':subject_section_id' => $data['subject_section_id']
                )
            );
        }

        return $result;
    }
}
