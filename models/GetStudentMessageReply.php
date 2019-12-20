<?php

class GetStudentMessageReply
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $message_id, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->message_id = $message_id;
        $this->curl = $curl;
    }

    // 生徒一覧の取得
    //public function getMessageReplyStudentList()
    //{
    //    $curl = array(
    //        'repository' => 'GetStudentMessageReplyModel'
    //      , 'method' => 'getMessageReplyStudentList'
    //      , 'params' => array(
    //            'school_id' => $this->school_id
    //          , 'message_id' => $this->message_id
    //        )
    //    );
    //
    //    return $this->curl->send($curl);
    //}

    // 教員一覧の取得
    public function getMessageReplyTeacherList()
    {
        $curl = array(
            'repository' => 'GetStudentMessageReplyModel'
          , 'method' => 'getMessageReplyTeacherList'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'message_id' => $this->message_id
            )
        );

        return $this->curl->send($curl);
    }

    // 管理者一覧の取得
    public function getMessageReplyAdminList()
    {
        $curl = array(
            'repository' => 'GetStudentMessageReplyModel'
          , 'method' => 'getMessageReplyAdminList'
          , 'params' => array(
                'message_id' => $this->message_id
            )
        );

        return $this->curl->send($curl);
    }

    // 年度一覧の取得
    //public function getMessageReplyGradeList()
    //{
    //    $curl = array(
    //        'repository' => 'GetStudentMessageReplyModel'
    //      , 'method' => 'getMessageReplyGradeList'
    //      , 'params' => array(
    //            'school_id' => $this->school_id
    //          , 'message_id' => $this->message_id
    //        )
    //    );
    //
    //    return $this->curl->send($curl);
    //}

    // コース一覧の取得
    //public function getMessageReplyCourseList()
    //{
    //    $curl = array(
    //        'repository' => 'GetStudentMessageReplyModel'
    //      , 'method' => 'getMessageReplyCourseList'
    //      , 'params' => array(
    //            'school_id' => $this->school_id
    //          , 'message_id' => $this->message_id
    //        )
    //    );
    //
    //    return $this->curl->send($curl);
    //}

    // クラス一覧の取得
    //public function getMessageReplyClassroomList()
    //{
    //    $curl = array(
    //        'repository' => 'GetStudentMessageReplyModel'
    //      , 'method' => 'getMessageReplyClassroomList'
    //      , 'params' => array(
    //            'school_id' => $this->school_id
    //          , 'message_id' => $this->message_id
    //        )
    //    );
    //
    //    return $this->curl->send($curl);
    //}

    // メッセージ作成-tbl_message部
    //public function setMessageReplyInsertMessage($data)
    //{
    //    $curl = array(
    //        'repository' => 'GetStudentMessageReplyModel'
    //      , 'method' => 'setMessageReplyInsertMessage'
    //      , 'params' => array(
    //            'school_id' => $this->school_id
    //          , 'title' => $data['title']
    //          , 'auther_user_level_id' => 3
    //          , 'auther_user_id' => $this->student_id
    //          , 'type' => 2
    //          , 'enable' => 1
    //        )
    //    );

    //    return $this->curl->send($curl);
    //}
    
    // メッセージ作成-tbl_message_detail部
    public function setMessageReplyInsertMessageDetail($data)
    {
        $curl = array(
            'repository' => 'GetStudentMessageReplyModel'
          , 'method' => 'setMessageReplyInsertMessageDetail'
          , 'params' => array(
                'message_id' => $this->message_id
              , 'message' => $data['message']
              , 'send_user_level_id' => 3
              , 'send_user_id' => $this->student_id
              , 'enable' => 1
            )
        );

        return $this->curl->send($curl);
    }
    
    // メッセージ作成-tbl_message_target部
    public function setMessageReplyInsertMessageTarget($data)
    {
        $curl = array(
            'repository' => 'GetStudentMessageReplyModel'
          , 'method' => 'setMessageReplyInsertMessageTarget'
          , 'params' => array(
                'message_detail_id' => $data['message_detail_id']
              , 'grade_id' => $data['grade_id']
              , 'course_id' => $data['course_id']
              , 'classroom_id' => $data['classroom_id']
              , 'receive_user_level_id' => $data['receive_user_level_id']
              , 'receive_user_id' => $data['receive_user_id']
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージデータ取得
    public function getMessageReplyMessageData()
    {
        $curl = array(
            'repository' => 'GetStudentMessageReplyModel'
          , 'method' => 'getMessageReplyMessageData'
          , 'params' => array(
                'message_id' => $this->message_id
            )
        );

        return $this->curl->send($curl);
    }
}
