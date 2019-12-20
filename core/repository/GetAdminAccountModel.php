<?php
require_once '../config.php';

class GetAdminAccountModel extends PdoBase
{

    public function getAdmin($data)
    {
        $sql = "SELECT admin_id
                  FROM tbl_admin
                 WHERE admin_id = " . $data['admin_id'] . "
                   AND password = '" . $data['password'] . "'
        ";

        return $this->fetchAll($sql, array(
            ':admin_id' => $data['admin_id']
          , ':password' => $data['password']
        ));
    }

    public function setAdmin($data)
    {
        $sql = "UPDATE tbl_admin
                   SET password = :password
                 WHERE admin_id = :admin_id
        ";

        return $this->fetchAll($sql, array(
            ':admin_id' => $data['admin_id']
          , ':password' => $data['password']
        ));
    }


}
