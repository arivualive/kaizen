<?php

class StudentGetContentsList
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $curl)
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    //所属する参加年度IDの取得 -> 参加年度が参照可能な講義IDの取得 -> 参加年度が参照可能な講義IDを返す
    public function getGradeSubjectSectionId()
    {
        // 参加年度IDの取得
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

        // 所属ユニットID -> 所属ユニットが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GradeSubjectSectionRepository'
          , 'method' => 'getSubjectSectionId'
          , 'params' => array(
                'grade_id' => $string
            )
        );

        $grade = $this->curl->send($curl);

        // 講義：多次元配列 -> 一次元配列
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

    //所属する所属ユニットIDの取得 -> 所属ユニットが参照可能な講義IDの取得 -> 所属ユニットが参照可能な講義IDを返す
    public function getClassroomSubjectSectionId()
    {
        // 所属ユニットIDの取得
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

        // 所属ユニットID -> 所属ユニットが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'ClassroomSubjectSectionRepository'
          , 'method' => 'getSubjectSectionId'
          , 'params' => array(
                'classroom_id' => $string
            )
        );

        $classroom = $this->curl->send($curl);

        // 講義：多次元配列 -> 一次元配列
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

    //所属するコースIDの取得 -> コースが参照可能な講義IDの取得 -> コースが参照可能な講義IDを返す
    public function getCourseSubjectSectionId()
    {
        // コースIDの取得
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

        // コースID -> コースが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'CourseSubjectSectionRepository'
          , 'method' => 'getSubjectSectionId'
          , 'params' => array(
                'course_id' => $string
            )
        );

        $course = $this->curl->send($curl);

        // 講義：多次元配列 -> 一次元配列
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

    // 参加年度・所属ユニット・コースから対象講義を取得 -> コンテンツ一覧を取得
    public function getContentsCount()
    {
        $grade_string = $this->getGradeSubjectSectionId();
        $classroom_string = $this->getClassroomSubjectSectionId();
        $course_string = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースから対象講義を取得
        $curl = array(
            'repository' => 'SubjectSectionRepository'
          , 'method' => 'findSubjectSectionIdSelect'
          , 'params' => array(
                'grade_id' => $grade_string
              , 'classroom_id' => $classroom_string
              , 'course_id' => $course_string
            )
        );

        $subject_section = $this->curl->send($curl);

        // 対象講義：多次元配列 -> 一次元配列
        $flatten = [];
        array_walk_recursive($subject_section, function ($value) use (&$flatten) {
            $flatten[] = $value;
        });
        $flatten = array_values(array_unique($flatten));

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

        // コンテンツ一覧の取得
        $curl = array(
            'repository' => 'ContentsRepository'
          , 'method' => 'getContentsCount'
          , 'params' => array(
                'subject_section_id' => $string
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから対象講義を取得 -> コンテンツ一覧を取得
    public function getContentsOffset()
    {
        $grade_string = $this->getGradeSubjectSectionId();
        $classroom_string = $this->getClassroomSubjectSectionId();
        $course_string = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースから対象講義を取得
        $curl = array(
            'repository' => 'SubjectSectionRepository'
          , 'method' => 'findSubjectSectionIdSelect'
          , 'params' => array(
                'grade_id' => $grade_string
              , 'classroom_id' => $classroom_string
              , 'course_id' => $course_string
            )
        );

        $subject_section = $this->curl->send($curl);

        // 対象講義：多次元配列 -> 一次元配列
        $flatten = [];
        array_walk_recursive($subject_section, function ($value) use (&$flatten) {
            $flatten[] = $value;
        });
        $flatten = array_values(array_unique($flatten));

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

        // コンテンツ一覧の取得
        $curl = array(
            'repository' => 'ContentsRepository'
          , 'method' => 'findSubjectSection'
          , 'params' => array(
                'subject_section_id' => $string
            )
        );

        return $this->curl->send($curl);
    }
}
