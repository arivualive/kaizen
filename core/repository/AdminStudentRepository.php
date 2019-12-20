<?php
require_once '../config.php';

class AdminStudentRepository extends PdoBase
{
    // 学生の取得(一覧)
    public function getStudentList($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_code
                     , a.student_name
                     , a.joining
                     , a.id
                     , a.password
                     , a.bit_subject
                  FROM tbl_student a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              ORDER BY display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 学生の取得(個人)
    public function getStudentPerson($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_code
                     , a.student_name
                     , a.joining
                     , a.id
                     , a.password
                     , a.bit_subject
                     , a.enable
                     , a.display_order
                  FROM tbl_student a
                 WHERE a.student_id = :student_id
        ';

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
        ));
    }

    // 学生の取得(受講者ID)
    public function GetStudentAlready($data)
    {
        $sql = 'SELECT a.student_id
                     , a.id
                     , a.password
                     , count(*) as count
                  FROM tbl_student a
                 WHERE a.id = :id
                 AND enable = 1
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    // 学生人数の取得
    public function getStudentCount($data)
    {
        $sql = 'SELECT count(*) as count
                  FROM tbl_student as a
                 WHERE a.school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 学生の登録
    public function insertStudent($data)
    {
        /*
        $sql = ' INSERT INTO
                    tbl_student
                    (   school_id
                      , student_code
                      , student_name
                      , joining
                      , id
                      , password
                      , bit_subject
                      , enable
                      , display_order
                    ) VALUES (
                        :school_id
                      , :student_code
                      , :student_name
                      , :joining
                      , :id
                      , :password
                      , :bit_subject
                      , :enable
                      ,(SELECT MAX(a.student_id + 1)
                          FROM tbl_student as a
                       )
                    )
        ';
        */

        $sql = 'SHOW TABLE STATUS LIKE "tbl_student"';
        $result = $this->fetch($sql);
        $data['display_order'] = $result['Auto_increment'];

        $sql = ' INSERT INTO
                    tbl_student
                    (   school_id
                      , student_code
                      , student_name
                      , joining
                      , id
                      , password
                      , bit_subject
                      , enable
                      , display_order
                    ) VALUES (
                        :school_id
                      , :student_code
                      , :student_name
                      , :joining
                      , :id
                      , :password
                      , :bit_subject
                      , :enable
                      , :display_order
                    )
                ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':student_code' => $data['student_code']
          , ':student_name' => $data['student_name']
          , ':joining' => $data['joining']
          , ':id' => $data['id']
          , ':password' => $data['password']
          , ':bit_subject' => $data['bit_subject']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }

    // 学生の編集
    public function editStudent($data)
    {
        $sql = 'UPDATE tbl_student
                   SET student_code = :student_code
                     , student_name = :student_name
                     , joining = :joining
                     , id = :id
                     , password = :password
                     , bit_subject = :bit_subject
                 WHERE student_id = :student_id
                 AND enable = 1
        ';

        return $this->execute($sql, array(
                      ':student_id' => $data['student_id']
                    , ':student_code' => $data['student_code']
                    , ':student_name' => $data['student_name']
                    , ':joining' => $data['joining']
                    , ':id' => $data['id']
                    , ':password' => $data['password']
                    , ':bit_subject' => $data['bit_subject']
                 )
        );
    }

    // 学生の論理削除
    public function deleteStudent($data)
    {
        $sql = 'UPDATE tbl_student
                   SET enable = 0
                 WHERE student_id = :student_id
        ';

        return $this->execute($sql,
                 array(
                      ':student_id' => $data['student_id']
                 )
        );
    }

    //学生IDの重複チェック
    public function checkStudentId($data)
    {
        $sql = 'SELECT count(*) as check_flg
                     , student_id
                  FROM tbl_student as a
                 WHERE id = :id
                   AND enable = 1
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    // コールサインの取得
    public function getCallSign($data)
    {
        $sql = 'SELECT a.call_sign
                  FROM tbl_school a
                 WHERE a.school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }



    //------ ソート関連 ------//

    // 学生並び順の取得
    public function getStudentDisplayOrder($data)
    {
        $sql = 'SELECT a.display_order
                  FROM tbl_student as a
                 WHERE a.student_id = :student_id
        ';

        return $this->fetch($sql,array(
            ':student_id' => $data['student_id']
        ));
    }

    public function setStudentDisplayOrder($data)
    {
        $sql = 'UPDATE tbl_student as a
                   SET a.display_order = :display_order
                 WHERE student_id = :student_id
        ';

        return $this->execute($sql,array(
            ':student_id' => $data['student_id']
          , ':display_order' => $data['display_order']
        ));
    }

    //TOP
    public function getStudentDisplayOrderSortTop($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_name
                     , a.display_order
                  FROM tbl_student as a
                 WHERE a.school_id = 1
                   AND a.enable = 1
                   AND a.display_order >= :display_order
              ORDER BY a.display_order ASC
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }

    //UP
    public function getStudentDisplayOrderSortUp($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_name
                     , a.display_order
                  FROM tbl_student as a
                 WHERE a.school_id = 1
                   AND a.enable = 1
                   AND a.display_order >= :display_order
              ORDER BY a.display_order ASC
                 LIMIT 2
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }

    //DOWN
    public function getStudentDisplayOrderSortDown($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_name
                     , a.display_order
                  FROM tbl_student as a
                 WHERE a.school_id = 1
                   AND a.enable = 1
                   AND a.display_order <= :display_order
              ORDER BY a.display_order DESC
                 LIMIT 2
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }

    //BOTTOM
    public function getStudentDisplayOrderSortBottom($data)
    {
        $sql = 'SELECT a.student_id
                     , a.student_name
                     , a.display_order
                  FROM tbl_student as a
                 WHERE a.school_id = 1
                   AND a.enable = 1
                   AND a.display_order <= :display_order
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }
}
