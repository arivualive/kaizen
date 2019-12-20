<?php

class AccessDataSubject
{
    private $curl;
    private $section_id;
    private $element;
    private $subject;

    public function __construct(Curl $curl, $section_id, AccessElement $element, SubjectAccess $subject)
    {
        $this->curl = $curl;
        $this->section_id = $section_id;
        $this->element = $element;
        $this->subject = $subject;
    }

    public function getGrade()
    {
        // grade データ
        $grade = $this->element->getGrade();
        print_r($grade);

        $section_grade = $this->subject->getGrade();
        print_r($section_grade);
/*
        // subject of grade
        $student = $this->student->getGrade();

        // matching
        $count = 0;
        foreach ($grade as $key => $item) {
            $data[$key] = $item;
            $data[$key]['checked'] = '';

            if ($item['grade_id'] == $student['grade_id']) {
                $data[$key]['checked'] = ' checked';
                $count++;
            }
        }

        $data[$key+1]['grade_id'] = '';
        $data[$key+1]['grade_name'] = '未設定';
        if ($count == 0) {
            $data[$key+1]['checked'] = ' checked';
        } else {
            $data[$key+1]['checked'] = '';
        }

        return $data;
        */
    }

    public function getClassroom()
    {
        $curl_data = array(
            'repository' => 'ClassroomRepository',
            'method' => 'findClassroomSchool',
            'params' => array('school_id' => $this->school_id)
        );
        $classroom_data = $this->curl->send($curl_data);
        $student_classroom_data = $this->student->getClassroom();

        foreach ((array) $classroom_data as $key => $classroom) {
            $classroom_box[$key] = $classroom;
            $classroom_box[$key]['checked'] = '';
            foreach ((array) $student_classroom_data as $student) {
                if ($classroom['classroom_id'] == $student['classroom_id']) {
                    $classroom_box[$key]['checked'] = ' checked';
                }
            }
        }

        return $classroom_box;
    }

    public function getCourse()
    {
        $curl_data = array(
            'repository' => 'CourseRepository',
            'method' => 'findCourseSchool',
            'params' => array('school_id' => $this->school_id)
        );

        $course = $this->curl->send($curl_data);
        $student = $this->student->getCourse();

        $count = 0;
        foreach ($course as $key => $item) {
            $data[$key] = $item;
            $data[$key]['checked'] = '';

            if ($item['course_id'] == $student['course_id']) {
                $data[$key]['checked'] = ' checked';
                $count++;
            }
        }

        $data[$key+1]['course_id'] = '';
        $data[$key+1]['course_name'] = '未設定';
        if ($count == 0) {
            $data[$key+1]['checked'] = ' checked';
        } else {
            $data[$key+1]['checked'] = '';
        }

        return $data;
    }

    /*
    private function add_notset($key, $count)
    {
        $data[$key+1]['grade_id'] = '';
        $data[$key+1]['grade_name'] = '未設定';
        if ($count == 0) {
            $data[$key+1]['checked'] = ' checked';
        } else {
            $data[$key+1]['checked'] = '';
        }

        return $data;
    }
     */
}
