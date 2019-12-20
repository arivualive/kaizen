<?php
require_once '../config.php';

class AdminTeacherRepository extends PdoBase
{
    // 教員の取得(一覧)
    public function getTeacherList($data)
    {
        $sql = 'SELECT a.admin_id as teacher_id
                     , a.admin_name as teacher_name
                     , a.id
                     , a.password
                     , a.bit_access
                  FROM tbl_admin a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
                   AND a.super_admin_flg = 0
              ORDER BY display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 教員の取得(個人)
    public function getTeacherPerson($data)
    {
        $sql = 'SELECT a.admin_id as teacher_id
                     , a.admin_name as teacher_name
                     , a.id
                     , a.password
                     , a.bit_access
                     , a.enable
                     , a.display_order
                  FROM tbl_admin a
                 WHERE a.admin_id = :admin_id
        ';

        return $this->fetchAll($sql, array(
            ':admin_id' => $data['admin_id']
        ));
    }

    // 教員の取得(受講者ID)
    public function GetTeacherAlready($data)
    {
        $sql = 'SELECT a.admin_id as teacher_id
                     , a.id
                     , a.password
                     , count(*) as count
                  FROM tbl_admin a
                 WHERE a.id = :id
                 AND enable = 1
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    // 教員人数の取得
    public function getTeacherCount($data)
    {
        $sql = 'SELECT count(*) as count
                  FROM tbl_admin as a
                 WHERE a.school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 教員の登録
    public function insertTeacher($data)
    {
        $sql = ' INSERT INTO
                    tbl_admin
                    (   school_id
                      , admin_name
                      , id
                      , password
                      , bit_access
                      , enable
                      , display_order
                    ) VALUES (
                        :school_id
                      , :admin_name
                      , :id
                      , :password
                      , :bit_access
                      , :enable
                      ,(SELECT MAX(a.admin_id + 1)
                          FROM tbl_admin as a
                       )
                    )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':admin_name' => $data['admin_name']
          , ':id' => $data['id']
          , ':password' => $data['password']
          , ':bit_access' => $data['bit_access']
          , ':enable' => 1
        ));
    }

    // 教員の編集
    public function editTeacher($data)
    {
        $sql = 'UPDATE tbl_admin
                   SET admin_name = :admin_name
                     , id = :id
                     , password = :password
                     , bit_access = :bit_access
                 WHERE admin_id = :admin_id
                 AND enable = 1
        ';

        return $this->execute($sql, array(
                      ':admin_id' => $data['admin_id']
                    , ':admin_name' => $data['admin_name']
                    , ':id' => $data['id']
                    , ':password' => $data['password']
                    , ':bit_access' => $data['bit_access']
                 )
        );
    }

    // 教員の論理削除
    public function deleteTeacher($data)
    {
        $sql = 'UPDATE tbl_admin
                   SET enable = 0
                 WHERE admin_id = :admin_id
        ';

        return $this->execute($sql,
                 array(
                      ':admin_id' => $data['admin_id']
                 )
        );
    }

    //教員IDの重複チェック
    public function checkTeacherId($data)
    {
        $sql = 'SELECT count(*) as check_flg
                     , admin_id
                  FROM tbl_admin as a
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

    // 教員並び順の取得
    public function getTeacherDisplayOrder($data)
    {
        $sql = 'SELECT a.display_order
                  FROM tbl_admin as a
                 WHERE a.admin_id = :admin_id
        ';

        return $this->fetch($sql,array(
            ':admin_id' => $data['admin_id']
        ));
    }

    public function setTeacherDisplayOrder($data)
    {
        $sql = 'UPDATE tbl_admin as a
                   SET a.display_order = :display_order
                 WHERE admin_id = :admin_id
        ';

        return $this->execute($sql,array(
            ':admin_id' => $data['admin_id']
          , ':display_order' => $data['display_order']
        ));
    }

    //TOP
    public function getTeacherDisplayOrderSortTop($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 2
                   AND a.enable = 1
                   AND a.display_order >= :display_order
              ORDER BY a.display_order ASC
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }

    //UP
    public function getTeacherDisplayOrderSortUp($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 2
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
    public function getTeacherDisplayOrderSortDown($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 2
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
    public function getTeacherDisplayOrderSortBottom($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 2
                   AND a.enable = 1
                   AND a.display_order <= :display_order
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql,array(
            ':display_order' => $data['display_order']
        ));
    }
}
