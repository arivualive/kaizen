<?php
require_once '../config.php';

class GetBitModel extends PdoBase
{
    public function getSubjectBit($data)
    {
        if($data['user_type'] == 0) {
            $sql = 'SELECT bit_subject
                      FROM tbl_admin as a
                     WHERE a.admin_id = :user_id
            ';
        } else if($data['user_type'] == 1) {
            $sql = 'SELECT bit_subject
                      FROM tbl_teacher as a
                     WHERE a.teacher_id = :user_id
            ';
        } else if($data['user_type'] == 2) {
            $sql = 'SELECT bit_subject
                      FROM tbl_student as a
                     WHERE a.student_id = :user_id
            ';
        }

        return $this->fetch($sql, array(
            ':user_id' => $data['user_id']
        ));
    }

    public function setSubjectBit($data)
    {
        if($data['user_type'] == 0) {
            $sql = 'UPDATE tbl_admin as a
                       SET bit_subject = :bit_strings
                     WHERE a.student_id = :user_id
            ';
        } else if($data['user_type'] == 1) {
            $sql = 'UPDATE tbl_teacher as a
                       SET bit_subject = :bit_strings
                     WHERE a.student_id = :user_id
            ';
        } else if($data['user_type'] == 2) {
            $sql = 'UPDATE tbl_student as a
                       SET bit_subject = :bit_strings
                     WHERE a.student_id = :user_id
            ';
        }

        return $this->fetch($sql, array(
            ':user_id' => $data['user_id']
          , ':bit_strings' => $data['bit_strings']
        ));
    }

}
