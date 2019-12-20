<?php
require_once '../config.php';

class AdminRepository extends PdoBase
{
    public function authCheck($data) {
        $sql = '
            SELECT
                admin_id
              , school_id
              , admin_name
              , enable
              , manage
              , permission
              , bit_subject
            FROM
                tbl_admin
            WHERE
                    id = :id
                AND
                    password = :pw
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
          , ':pw' => $data['pw']
        ));
    }

    public function findAdminAll() {
        $sql = '
            SELECT
                admin_id
              , school_id
              , admin_name
              , enable
              , display_order
            FROM
                tbl_admin
        ';

        return $this->fetchAll($sql);
    }

    public function findAdminId($data) {
        $sql = '
            SELECT
                admin_id
              , school_id
              , admin_name
              , enable
              , display_order
            FROM
                tbl_admin
            WHERE
                admin_id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    public function updateAdminDisplayOrder($data) {
        $sql = '
            UPDATE
                tbl_admin
            SET
                display_order = :display_order
            WHERE
                admin_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':id' => $data['id']
        ));
    }

    public function insertAdmin($data) {
        $sql = '
            INSERT INTO
                tbl_admin (
                    school_id
                  , admin_name
                  , enable
                ) VALUES (
                    :school_id
                  , :admin_name
                  , :enable
                )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':admin_name' => $data['admin_name']
          , ':enable' => 1
        ));
    }
}
