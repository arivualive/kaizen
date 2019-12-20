<?php
require_once '../config.php';

class Core_SchoolRepository extends PdoBase
{
    // 学校の取得(一覧)
    public function selectSchoolList($data)
    {
        $sql = ' SELECT school_id
                      , school_name
                      , enable
                   FROM tbl_school
        ';

        return $this->fetchAll($sql);
    }

    // 学校の取得(詳細)
    public function selectSchoolPerson($data)
    {
        $sql = ' SELECT school_id
                      , school_name
                      , call_sign
                      , max_id_of_admin
                      , max_id_of_teacher
                      , max_id_of_student
                      , max_school_contents_total_giga_byte
                      , enable
                      , display_order
                   FROM tbl_school
                  WHERE school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 学校の取得(一覧)
    public function selectSchoolCallSign($data)
    {
        $sql = ' SELECT call_sign
                   FROM tbl_school
                  WHERE school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 学校の登録
    public function insertSchool($data)
    {
        $sql = ' INSERT INTO
             tbl_school (
                        school_name
                      , call_sign
                      , max_id_of_admin
                      , max_id_of_teacher
                      , max_id_of_student
                      , max_school_contents_total_giga_byte
                      , enable
                      , display_order
                      ) VALUES (
                        :school_name
                      , :call_sign
                      , :max_id_of_admin
                      , :max_id_of_teacher
                      , :max_id_of_student
                      , :max_school_contents_total_giga_byte
                      , :enable
                      , :display_order
                      )
        ';

        return $this->execute($sql, array(
            ':school_name' => $data['school_name']
          , ':call_sign' => $data['call_sign']
          , ':max_id_of_admin' => $data['max_id_of_admin']
          , ':max_id_of_teacher' => $data['max_id_of_teacher']
          , ':max_id_of_student' => $data['max_id_of_student']
          , ':max_school_contents_total_giga_byte' => $data['max_school_contents_total_giga_byte']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
        ));
    }

    // 学校の編集
    public function updateSchool($data)
    {
        $sql = ' UPDATE tbl_school
                    SET school_name = :school_name
                      , call_sign = :call_sign
                      , max_id_of_admin = :max_id_of_admin
                      , max_id_of_teacher = :max_id_of_teacher
                      , max_id_of_student = :max_id_of_student
                      , max_school_contents_total_giga_byte = :max_school_contents_total_giga_byte
                      , enable = :enable
                      , display_order = :display_order
                  WHERE school_id = :school_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':school_name' => $data['school_name']
          , ':call_sign' => $data['call_sign']
          , ':max_id_of_admin' => $data['max_id_of_admin']
          , ':max_id_of_teacher' => $data['max_id_of_teacher']
          , ':max_id_of_student' => $data['max_id_of_student']
          , ':max_school_contents_total_giga_byte' => $data['max_school_contents_total_giga_byte']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
        ));
    }
}
