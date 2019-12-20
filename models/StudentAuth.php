<?php

class StudentAuth
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
            'repository' => 'StudentRepository',
            'method' => 'authCheck',
            'params' => array(
                'id' => $this->username,
                'pw' => $this->password
            )
        );
    }

    public function updateAccessDate($student_id)
    {
        return array(
            'module' => 'default',
            'repository' => 'StudentRepository',
            'method' => 'updateAccessData',
            'params' => array(
                'student_id' => $student_id
            )
        );
    }
    
}
