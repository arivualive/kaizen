<?php

class StudentGetQuestionnaireList
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    //参加年度の取得
    public function getGradeId()
    {
        $curl = array(
            'repository' => 'StudentGradeRepository'
          , 'method' => 'getGradeId'
          , 'params' => array(  
                'student_id' => $this->student_id
            )
        );

        $grade = $this->curl->send($curl);

        // 参加年度：多次元配列 -> 一次元配列
        $flatten = [];
        array_walk_recursive($grade, function ($value) use (&$flatten) {
            $flatten[] = $value;
        });

        $string = "";
        for( $i = 0 ; $i < count($flatten) ; $i++ ) {
            if(count($flatten) <= 1) {
                $string .= "IN ($flatten[$i])";
            } else if($i === 0) {
                $string .= "IN ($flatten[$i],";
            } else if($i < count($flatten) - 1) {
                $string .= "$flatten[$i],";
            } else {
                $string .= "$flatten[$i])";
            }
        }

        return $string;
    }

    //所属ユニットの取得
    public function getClassroomId()
    {
        $curl = array(
            'repository' => 'StudentClassroomRepository'
          , 'method' => 'getClassroomId'
          , 'params' => array(
                'student_id' => $this->student_id
            )
        );

        $classroom = $this->curl->send($curl);

        // 所属ユニット：多次元配列 -> 一次元配列
        $flatten = [];
        array_walk_recursive($classroom, function ($value) use (&$flatten) {
            $flatten[] = $value;
        });

        $string = "";
        for( $i = 0 ; $i < count($flatten) ; $i++ ) {
            if(count($flatten) <= 1) {
                $string .= "IN ($flatten[$i])";
            } else if($i === 0) {
                $string .= "IN ($flatten[$i],";
            } else if($i < count($flatten) - 1) {
                $string .= "$flatten[$i],";
            } else {
                $string .= "$flatten[$i])";
            }
        }

        return $string;
    }

    //コースの取得
    public function getCourseId()
    {
        $curl = array(
            'repository' => 'StudentCourseRepository'
          , 'method' => 'getCourseId'
          , 'params' => array(
                'student_id' => $this->student_id
            )
        );

        $course = $this->curl->send($curl);

        // コース：多次元配列 -> 一次元配列
        $flatten = [];
        array_walk_recursive($course, function ($value) use (&$flatten) {
            $flatten[] = $value;
        });

        $string = "";
        for( $i = 0 ; $i < count($flatten) ; $i++ ) {
            if(count($flatten) <= 1) {
                $string .= "IN ($flatten[$i])";
            } else if($i === 0) {
                $string .= "IN ($flatten[$i],";
            } else if($i < count($flatten) - 1) {
                $string .= "$flatten[$i],";
            } else {
                $string .= "$flatten[$i])";
            }
        }

        return $string;
    }

    // 参加年度・所属ユニット・コースからアンケートを取得
    public function getQuestionnaireCount()
    {
        $grade_id_string = $this->getGradeId();
        $classroom_id_string = $this->getClassroomId();
        $course_id_string = $this->getCourseId();

        $curl = array(
            'repository' => 'QuestionnaireRepository'
          , 'method' => 'getQuestionnaireDataCount'
          , 'params' => array(
                'grade_id' => $grade_id_string
              , 'classroom_id' => $classroom_id_string
              , 'course_id' => $course_id_string
              , 'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからアンケートを取得
    public function getQuestionnaireOffset($limit, $offset)
    {
        $grade_id_string = $this->getGradeId();
        $classroom_id_string = $this->getClassroomId();
        $course_id_string = $this->getCourseId();

        $curl = array(
            'repository' => 'QuestionnaireRepository'
          , 'method' => 'getQuestionnaireDataOffset'
          , 'params' => array(
                'grade_id' => $grade_id_string
              , 'classroom_id' => $classroom_id_string
              , 'course_id' => $course_id_string
              , 'school_id' => $this->school_id
              , 'limit' => $limit
              , 'offset' => $offset
            )
        );

        return $this->curl->send($curl);
    }
}
