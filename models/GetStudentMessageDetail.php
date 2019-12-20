<?php

class GetStudentMessageDetail
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $message_id, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->message_id = $message_id;
        $this->curl = $curl;

        $this->grade_id = $this->getGradeId();
        $this->classroom_id = $this->getClassroomId();
        $this->course_id = $this->getCourseId();
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

    // 参加年度・所属ユニット・コースからスレッドデータを取得
    public function getMessageDetailTitle()
    {
        $curl = array(
            'repository' => 'GetStudentMessageDetailModel'
          , 'method' => 'getMessageDetailTitle'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'message_id' => $this->message_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージデータを取得
    public function getMessageDetailOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetStudentMessageDetailModel'
          , 'method' => 'getMessageDetailOffset'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'message_id' => $this->message_id
              , 'grade_id' => $this->grade_id
              , 'classroom_id' => $this->classroom_id
              , 'course_id' => $this->course_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから受信者を取得
    public function getMessageDetailReceiver($message_detail_id, $type)
    {
        $curl = array(
            'repository' => 'GetStudentMessageDetailModel'
          , 'method' => 'getMessageDetailReceiver'
          , 'params' => array(
                'student_id' => $this->student_id
              , 'message_detail_id' => $message_detail_id
              , 'type' => $type
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージの削除
    public function setMessageDetailDelete($message_detail_id)
    {
        $curl = array(
            'repository' => 'GetStudentMessageDetailModel'
          , 'method' => 'setMessageDetailDelete'
          , 'params' => array(
                'message_detail_id' => $message_detail_id
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージの確認状況更新
    public function getMessageDetailDateCheck()
    {
        $curl = array(
            'repository' => 'GetStudentMessageDetailModel'
          , 'method' => 'getMessageDetailDateCheck'
          , 'params' => array(
                'message_id' => $this->message_id
              , 'user_level_id' => 3
              , 'user_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }
}
