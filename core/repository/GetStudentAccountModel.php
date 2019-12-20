<?php
require_once '../config.php';

class GetStudentAccountModel extends PdoBase
{

    public function getStudent($data)
    {
        $sql = "SELECT student_id
                  FROM tbl_student
                 WHERE student_id = " . $data['student_id'] . "
                   AND password = '" . $data['password'] . "'
        ";

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':password' => $data['password']
        ));
    }

    public function setStudent($data)
    {
        $sql = "UPDATE tbl_student
                   SET password = :password
                 WHERE student_id = :student_id
        ";

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':password' => $data['password']
        ));
    }


}
