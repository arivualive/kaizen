<?php

class AccessElement
{
    private $curl;

    public function __construct(Curl $curl)
    {
        $this->curl = $curl;
    }

    public function getGrade()
    {
        $curl_data = array(
            'repository' => 'GradeRepository',
            'method' => 'findGradeEnable'
        );
        
        // grade データ
        return $this->curl->send($curl_data);
    }

    public function getClassroom()
    {
        $curl_data = array(
            'repository' => 'ClassroomRepository',
            'method' => 'findClassroomEnable'
        );
        
        return $this->curl->send($curl_data);
    }

    public function getCourse()
    {
        $curl_data = array(
            'repository' => 'CourseRepository',
            'method' => 'findCourseEnable'
        );

        return $this->curl->send($curl_data);
    }
}
