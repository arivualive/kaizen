<?php

class OrganizerAuth
{
    private $username;
    private $password;
    private $Curl;

    public function __construct($id, $pw, $curl)
    {
        $crypt = new StringEncrypt;
        $this->username = $crypt->encrypt($id);
        $this->password = $crypt->encrypt($pw);
        $this->Curl = $curl;
    }

    public function authCheck()
    {
        $curl = array(
            'module' => 'default',
            'repository' => 'OrganizerRepository',
            'method' => 'authCheck',
            'params' => array(
                'id' => $this->username,
                'pw' => $this->password
            )
        );

        return $this->Curl->send($curl);
    }
}
