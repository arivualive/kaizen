<?php

class StudentAccount
{
    private $now_password;
    private $new_password;

    public function __construct($student_id, $now_password, $new_password, $curl)
    {
        $crypt = new StringEncrypt;
        $this->student_id = $student_id;
        $this->now_password = $crypt->encrypt($now_password);
        $this->new_password = $crypt->encrypt($new_password);
        $this->curl = $curl;
        //debug($now_password . " -> " . $this->now_password);
        //debug($new_password . " -> " . $this->new_password);
    }


    public function getStudent()
    {
        $curl = array(
            'repository' => 'GetStudentAccountModel'
          , 'method' => 'getStudent'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'password' => $this->now_password
            )
        );

        return $this->curl->send($curl);
    }

    public function setStudent()
    {
        $curl = array(
            'repository' => 'GetStudentAccountModel'
          , 'method' => 'setStudent'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'password' => $this->new_password
            )
        );

        return $this->curl->send($curl);
    }
    
}
