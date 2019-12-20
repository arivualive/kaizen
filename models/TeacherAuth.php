<?php

class TeacherAuth
{
    private $username;
    private $password;

    public function __construct($id, $pw)
    {
        $crypt = new StringEncrypt;
        $this->username = $crypt->encrypt($id);
        $this->password = $crypt->encrypt($pw);
    }

    public function authCheck()
    {
        return array(
            'module' => 'default',
            'repository' => 'TeacherRepository',
            'method' => 'authCheck',
            'params' => array(
                'id' => $this->username,
                'pw' => $this->password
            )
        );
    }
}
