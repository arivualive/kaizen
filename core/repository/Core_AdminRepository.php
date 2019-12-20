<?php
require_once '../config.php';

class Core_AdminRepository extends PdoBase
{
    // 管理者の取得(一覧)
    public function selectAdminList($data)
    {
        $sql = ' SELECT admin_id
                      , admin_name
                      , enable
                      , display_order
                      , manage
                      , permission
                      , bit_subject
                   FROM tbl_admin
                  WHERE school_id = :school_id
                    AND (manage=0 OR admin_id=:own_id)
               ORDER BY display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':own_id' => $data['own_id'],
            ':school_id' => $data['school_id']
        ));
    }

    // 管理者の取得(一覧)
    public function selectAdminListAll($data)
    {
        $sql = ' SELECT admin_id
                      , admin_name
                      , enable
                      , display_order
                      , manage
                      , permission
                      , bit_subject
                   FROM tbl_admin
                  WHERE school_id = :school_id
               ORDER BY display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    // 管理者の取得(詳細)
    public function selectAdminPerson($data)
    {
        $sql = ' SELECT admin_id
                      , admin_name
                      , school_id
                      , id
                      , password
                      , enable
                      , display_order
                      , manage
                      , permission
                      , bit_subject
                   FROM tbl_admin
                  WHERE admin_id = :admin_id
                    AND school_id = :school_id
        ';

        return $this->fetch($sql, array(
            ':admin_id' => $data['admin_id']
          , ':school_id' => $data['school_id']
        ));
    }

    // 管理者の取得(レコード総数)
    public function selectAdminCount($data)
    {
        $sql = ' SELECT count(*) as count
                   FROM tbl_admin
                  WHERE manage=0 OR admin_id=:own_id
        ';

        return $this->fetch($sql, array(
            ':own_id' => $data['own_id']
        ));
    }

    // 管理者の取得(レコード総数)
    public function selectAdminCountAll($data)
    {
        $sql = ' SELECT count(*) as count
                   FROM tbl_admin
        ';

        return $this->fetch($sql);
    }

    // 管理者の登録
    public function insertAdmin($data)
    { //$data[ 'return_testdesuyo'] = 'return_testdesuyo!!!!';
      //return $data;
        $sql = ' INSERT INTO
              tbl_admin (
                        admin_name
                      , school_id
                      , id
                      , password
                      , enable
                      , display_order
                      , manage
                      , permission
                      , bit_subject
                      ) VALUES (
                        :admin_name
                      , :school_id
                      , :id
                      , :password
                      , :enable
                      , :display_order
                      , :manage
                      , :permission
                      , :bit_subject
                      )
        ';

        return $this->execute($sql, array(
            ':admin_name' => $data['admin_name']
          , ':school_id' => $data['school_id']
          , ':id' => $data['id']
          , ':password' => $data['password']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':manage' => $data['manage']
          , ':permission' => $data['permission']
          , ':bit_subject' => $data['bit_subject']
        ));
    }

    // 管理者編集
    public function updateAdmin($data)
    {
        $sql = ' UPDATE tbl_admin
                    SET admin_name = :admin_name
                      , school_id = :school_id
                      , id = :id
                      , password = :password
                      , enable = :enable
                      , display_order = :display_order
                      , manage = :manage
                      , permission = :permission
                      , bit_subject = :bit_subject
                  WHERE admin_id = :admin_id
                    AND school_id = :school_id
        ';

        return $this->execute($sql, array(
            ':admin_id' => $data['admin_id']
          , ':admin_name' => $data['admin_name']
          , ':school_id' => $data['school_id']
          , ':id' => $data['id']
          , ':password' => $data['password']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':manage' => $data['manage']
          , ':permission' => $data['permission']
          , ':bit_subject' => $data['bit_subject']
        ));
    }

    //管理者のログインIDの重複チェック
    public function checkAdminLoginId($data)
    {
        $sql = 'SELECT count(*) as check_flg
                     , admin_id
                  FROM tbl_admin as a
                 WHERE id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    //TOP
    public function getAdminDisplayOrderSortTop($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 1
                   AND (manage=0 OR admin_id=:own_id)
                   AND a.display_order >= :display_order
              ORDER BY a.display_order ASC
        ';

        return $this->fetchAll($sql,array(
            ':own_id' => $data['own_id'],
            ':display_order' => $data['display_order']
        ));
    }

    //UP
    public function getAdminDisplayOrderSortUp($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 1
                   AND (manage=0 OR admin_id=:own_id)
                   AND a.display_order >= :display_order
              ORDER BY a.display_order ASC
                 LIMIT 2
        ';

        return $this->fetchAll($sql,array(
            ':own_id' => $data['own_id'],
            ':display_order' => $data['display_order']
        ));
    }

    //DOWN
    public function getAdminDisplayOrderSortDown($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 1
                   AND (manage=0 OR admin_id=:own_id)
                   AND a.display_order <= :display_order
              ORDER BY a.display_order DESC
                 LIMIT 2
        ';

        return $this->fetchAll($sql,array(
            ':own_id' => $data['own_id'],
            ':display_order' => $data['display_order']
        ));
    }

    //BOTTOM
    public function getAdminDisplayOrderSortBottom($data)
    {
        $sql = 'SELECT a.admin_id
                     , a.admin_name
                     , a.display_order
                  FROM tbl_admin as a
                 WHERE a.school_id = 1
                   AND (manage=0 OR admin_id=:own_id)
                   AND a.display_order <= :display_order
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql,array(
            ':own_id' => $data['own_id'],
            ':display_order' => $data['display_order']
        ));
    }

    public function setAdminDisplayOrder($data)
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
}
