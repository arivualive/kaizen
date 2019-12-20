<?php
class Student
{
    private $student_id;
    private $Curl;

    public function __construct(Curl $curl)
    {
        $this->Curl = $curl;
    }

    public function setStudentId($student_id)
    {
        $this->student_id = $student_id;
    }

    public function getStudentId()
    {
        return $this->student_id;
    }

    public function findStudentId()
    {
        $curl = array(
            'repository' => 'StudentRepository'
            , 'method' => 'findStudentId'
            , 'params' => array(
                'id' => $this->student_id
            )
        );

        return $this->Curl->send($curl);
    }

}
