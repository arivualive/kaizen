<?php

class GetStudentMessageList
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->curl = $curl;

        //$this->grade_id = $this->getGradeId();
        $this->grade_id = 0;
        //$this->classroom_id = $this->getClassroomId();
        $this->classroom_id = 0;
        //$this->course_id = $this->getCourseId();
        $this->course_id = 0;
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
            'repository' => 'GetStudentAccessModel'
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
        // 参加年度ID -> 参加年度が参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
          , 'method' => 'getGradeSubjectSectionId'
          , 'params' => array(
                'grade_id' => $this->grade_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //所属ユニットIDの取得
    public function getClassroomId()
    {
        // 所属ユニットIDの取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
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
        // 所属ユニットID -> 所属ユニットが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
          , 'method' => 'getClassroomSubjectSectionId'
          , 'params' => array(
                'classroom_id' => $this->classroom_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    //コースIDの取得
    public function getCourseId()
    {
        // コースIDの取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
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
        // コースID -> コースが参照可能な講義IDの取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
          , 'method' => 'getCourseSubjectSectionId'
          , 'params' => array(
                'course_id' => $this->course_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    // *---デバッグ用関数---*
    // この関数を呼び出すと、現在ログインしている学生の所属状況・所属先が参照できる講義IDが取得できる
    public function getStudentDataId()
    {
        // 各対象講義（参加年度・所属ユニット・コース）の取得
        $student_data = [];
        $student_data[0] = $this->grade_id;
        $student_data[1] = $this->classroom_id;
        $student_data[2] = $this->course_id;
        $student_data[3] = $this->getGradeSubjectSectionId();
        $student_data[4] = $this->getClassroomSubjectSectionId();
        $student_data[5] = $this->getCourseSubjectSectionId();

        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得

        return($student_data);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ数の取得)
    public function getSubjectSectionId()
    {
        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetStudentAccessModel'
          , 'method' => 'GetSubjectSectionId'
          , 'params' => array(
                'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
            )
        );

        return($this->getFlattenString($this->curl->send($curl)));
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getMessageListCount()
    {
        $curl = array(
            'repository' => 'GetStudentMessageListModel'
          , 'method' => 'getMessageListCount'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'school_id' => $this->school_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getMessageListOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetStudentMessageListModel'
          , 'method' => 'getMessageListOffset'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'school_id' => $this->school_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
              , 'limit' => $limit
              , 'offset' => $offset
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getNoticeListCount()
    {
        $curl = array(
            'repository' => 'GetStudentMessageListModel'
          , 'method' => 'getNoticeListCount'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'school_id' => $this->school_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getNoticeListOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetStudentMessageListModel'
          , 'method' => 'getNoticeListOffset'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'school_id' => $this->school_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
              , 'limit' => $limit
              , 'offset' => $offset
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージの削除
    public function setMessageListDelete($message_id)
    {
        $curl = array(
            'repository' => 'GetStudentMessageListModel'
          , 'method' => 'setMessageListDelete'
          , 'params' => array(
                'message_id' => $message_id
            )
        );

        return $this->curl->send($curl);
    }
}
