<?php

class AdminAccount
{
    private $now_password;
    private $new_password;

    public function __construct($admin_id, $now_password, $new_password, $curl)
    {
        $crypt = new StringEncrypt;
        $this->admin_id = $admin_id;
        $this->now_password = $crypt->encrypt($now_password);
        $this->new_password = $crypt->encrypt($new_password);
        $this->curl = $curl;
        //debug($now_password . " -> " . $this->now_password);
        //debug($new_password . " -> " . $this->new_password);
    }


    public function getAdmin()
    {
        $curl = array(
            'repository' => 'GetAdminAccountModel'
          , 'method' => 'getAdmin'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'password' => $this->now_password
            )
        );

        return $this->curl->send($curl);
    }

    public function setAdmin()
    {
        $curl = array(
            'repository' => 'GetAdminAccountModel'
          , 'method' => 'setAdmin'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'password' => $this->new_password
            )
        );

        return $this->curl->send($curl);
    }
    
}
