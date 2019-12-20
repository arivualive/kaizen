<?php

class StudentAccessSave
{
    private $student_id;
    private $curl;

    public function __construct($student_id, $curl)
    {
        $this->student_id = $student_id;
        $this->curl = $curl;
    }

    public function deleteGrade($data)
    {
        $curl_data = array('repository' => 'StudentGradeRepository', 'method' => 'deleteStudentGrade', 'params' => $data);

        return  $this->curl->send($curl_data);
    }

    public function insertGrade($data)
    {
        // 無条件で削除する。
        $this->deleteGrade($data);

        // 未設定の場合は1を返してデータベースにアクセスしない。
        if ($data['grade_id'] == '') {

            return $this->message(1);
        }

        $curl_data = array('repository' => 'StudentGradeRepository', 'method' => 'insertStudentGrade', 'params' => $data);
        $result = $this->curl->send($curl_data);

        return $this->message($result);
    }

    public function deleteClassroom($data)
    {
        $curl_data = array('repository' => 'StudentClassroomRepository', 'method' => 'deleteStudentClassroom', 'params' => $data);
        $result = $this->curl->send($curl_data);
    }

    public function insertClassroom($data)
    {
        $this->deleteClassroom($data);

        if (count($data['classroom_id']) == 0) {

            return $this->message(1);
        }

        foreach ((array) $data['classroom_id'] as $classroom_id) {
            $curl_data = array(
                'repository' => 'StudentClassroomRepository',
                'method' => 'insertStudentClassroom',
                'params' => array('classroom_id' => $classroom_id, 'student_id' => $data['student_id'])
            );

            $result = $this->curl->send($curl_data);
        }

        return $this->message($result);
    }

    public function deleteCourse($data)
    {
        $curl_data = array('repository' => 'StudentCourseRepository', 'method' => 'deleteStudentCourse', 'params' => $data);

        return  $this->curl->send($curl_data);
    }

    public function insertCourse($data)
    {
        $this->deleteCourse($data);

        if ($data['course_id'] == '') {

            return $this->message(1);
        }

        $curl_data = array('repository' => 'StudentCourseRepository', 'method' => 'insertStudentCourse', 'params' => $data);
        $result = $this->curl->send($curl_data);

        return $this->message($result);
    }

    public function message($result)
    {
        $message = "保存できませんでした。";

        if ($result == '1') {
            $message = "保存しました。";
        }

        return $message;
    }

}
