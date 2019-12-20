<?php

class GetStudentContentsList
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $bit_classroom, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->bit_classroom = $bit_classroom;
        $this->curl = $curl;
    }

    public function getFlattenString($data)
    {
        //各SQL問い合わせで条件に合致するデータがなかった時の処理
        if(isset($data) != true) {
            $string = '!= 0';
            return $string;
        }

        $flatten = [];
        array_walk_recursive($data, function ($value) use (&$flatten) {
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

    //参加年度IDの取得
    public function getGradeId()
    {
        // 参加年度IDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getGradeId'
          , 'params' => array(
                'student_id' => $this->student_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //参加年度が参照可能な講義IDの取得
    public function getGradeSubjectSectionId()
    {
        //参加年度IDの取得
        $string = $this -> getGradeId();

        // 参加年度ID -> 参加年度が参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getGradeSubjectSectionId'
          , 'params' => array(
                'grade_id' => $string
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //所属ユニットIDの取得
    public function getClassroomId()
    {
        // 所属ユニットIDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getClassroomId'
          , 'params' => array(
                'student_id' => $this->student_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //所属ユニットが参照可能な講義IDの取得
    public function getClassroomSubjectSectionId()
    {
        //所属ユニットIDの取得
        $string = $this -> getClassroomId();

        // 所属ユニットID -> 所属ユニットが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getClassroomSubjectSectionId'
          , 'params' => array(
                'classroom_id' => $string
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //コースIDの取得
    public function getCourseId()
    {
        // コースIDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getCourseId'
          , 'params' => array(
                'student_id' => $this->student_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //コースが参照可能な講義IDの取得
    public function getCourseSubjectSectionId()
    {
        //コースIDの取得
        $string = $this -> getCourseId();

        // コースID -> コースが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getCourseSubjectSectionId'
          , 'params' => array(
                'course_id' => $string
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ数の取得)
    public function getSubjectSectionId()
    {
        // 各対象講義（参加年度・所属ユニット・コース）の取得
        $grade_string = $this->getGradeSubjectSectionId();
        $classroom_string = $this->getClassroomSubjectSectionId();
        $course_string = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'GetSubjectSectionId'
          , 'params' => array(
                'grade_id' => $grade_string
              , 'classroom_id' => $classroom_string
              , 'course_id' => $course_string
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    public function getContentsCount()
    {
        $subject_section_string = $this->getSubjectSectionId();

        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'GetContentsCount'
          , 'params' => array(
                'subject_section_id' => $subject_section_string
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getContents()
    {
        $subject_section_string = $this->getSubjectSectionId();

        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getContents'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getContentsAttachment($data)
    {
        $subject_section_string = $this->getSubjectSectionId();

        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getContentsAttachment'
          , 'params' => array(
              'contents_id' => $data['primary_key']
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからアンケートを取得
    public function getQuestionnaire($type)
    {
        $grade_id_string = $this->getGradeId();
        $classroom_id_string = $this->getClassroomId();
        $course_id_string = $this->getCourseId();

        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getQuestionnaire'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'type' => $type
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからテストを取得
    public function getQuiz()
    {
        $grade_id_string = $this->getGradeId();
        $classroom_id_string = $this->getClassroomId();
        $course_id_string = $this->getCourseId();

        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getQuiz'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getQuizAnswer($data)
    {
        $subject_section_string = $this->getSubjectSectionId();

        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'getQuizAnswer'
          , 'params' => array(
              'student_id' => $this->student_id
            , 'quiz_id' => $data['primary_key']
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    //public function getMessage()
    //{
    //    $grade_id_string = $this->getGradeId();
    //    $classroom_id_string = $this->getClassroomId();
    //    $course_id_string = $this->getCourseId();

    //    $curl = array(
    //        'repository' => 'GetStudentContentsListModel'
    //      , 'method' => 'getMessage'
    //      , 'params' => array(
    //            'grade_id' => $grade_id_string
    //          , 'classroom_id' => $classroom_id_string
    //          , 'course_id' => $course_id_string
    //          , 'school_id' => $this->school_id
    //          , 'student_id' => $this->student_id
    //        )
    //    );

    //    return $this->curl->send($curl);
    //}

    // 参加年度・所属ユニット・コースから科目・講座を取得
    public function getSubject()
    {
        // 各対象講義（参加年度・所属ユニット・コース）の取得
        $grade_id_string = $this->getGradeSubjectSectionId();
        $classroom_id_string = $this->getClassroomSubjectSectionId();
        $course_id_string = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'GetSubject'
          , 'params' => array(
                'grade_id' => $grade_id_string
              , 'classroom_id' => $classroom_id_string
              , 'course_id' => $course_id_string
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから科目・講座を取得
    public function getSubjectSection($data)
    {
        // 各対象講義（参加年度・所属ユニット・コース）の取得
        $grade_id_string = $this->getGradeSubjectSectionId();
        $classroom_id_string = $this->getClassroomSubjectSectionId();
        $course_id_string = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetStudentContentsListModel'
          , 'method' => 'GetSubjectSection'
          , 'params' => array(
                'grade_id' => $grade_id_string
              , 'classroom_id' => $classroom_id_string
              , 'course_id' => $course_id_string
              , 'subject_genre_id' => $data['subject_genre_id']
            )
        );

        return $this->curl->send($curl);
    }
}
