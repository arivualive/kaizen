<?php
require_once '../config.php';

class OrganizerRepository extends PdoBase
{
    public function authCheck($data) {
        $sql = ' SELECT organizer_id
                      , organizer_name
                   FROM tbl_organizer
                  WHERE id = :id
                    AND password = :pw
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
          , ':pw' => $data['pw']
        ));
    }
}
