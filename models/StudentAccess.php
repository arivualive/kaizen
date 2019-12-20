<?php

class StudentAccess
{
    private $id;
    private $curl;

    public function __construct($id, $curl)
    {
        $this->id = $id;
        $this->curl = $curl;
    }

    public function getStudent()
    {
        $data = array(
            'repository' => 'StudentRepository',
            'method' => 'findStudentId',
            'params' => array('id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function getGrade()
    {
        $data = array(
            'repository' => 'StudentGradeRepository',
            'method' => 'findStudentId',
            'params' => array('student_id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function getClassroom()
    {
        $data = array(
            'repository' => 'StudentClassroomRepository',
            'method' => 'findStudentId',
            'params' => array('student_id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function getCourse()
    {
        $data = array(
            'repository' => 'StudentCourseRepository',
            'method' => 'findStudentId',
            'params' => array('student_id' => $this->id)
        );

        return $this->curl->send($data);
    }

}
