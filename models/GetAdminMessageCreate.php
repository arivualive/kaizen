<?php

class GetAdminMessageCreate
{
    public $admin_id;
    public $curl;

    public function __construct($admin_id, $school_id, $curl) 
    {
        $this->admin_id = $admin_id;
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    // 生徒一覧の取得
    public function getMessageCreateStudentList()
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'getMessageCreateStudentList'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 教員一覧の取得
    public function getMessageCreateTeacherList()
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'getMessageCreateTeacherList'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 管理者一覧の取得
    //public function getMessageCreateAdminList()
    //{
    //    $curl = array(
    //        'repository' => 'GetAdminMessageCreateModel'
    //      , 'method' => 'getMessageCreateAdminList'
    //      , 'params' => array()
    //    );
    //
    //    return $this->curl->send($curl);
    //}

    // 年度一覧の取得
    public function getMessageCreateGradeList()
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'getMessageCreateGradeList'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );
    
        return $this->curl->send($curl);
    }

    // コース一覧の取得
    public function getMessageCreateCourseList()
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'getMessageCreateCourseList'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );
    
        return $this->curl->send($curl);
    }

    // クラス一覧の取得
    public function getMessageCreateClassroomList()
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'getMessageCreateClassroomList'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );
    
        return $this->curl->send($curl);
    }

    // メッセージ作成-tbl_message部
    public function setMessageCreateInsertMessage($data)
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'setMessageCreateInsertMessage'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'title' => $data['title']
              , 'auther_user_level_id' => 1
              , 'auther_user_id' => $this->admin_id
              , 'type' => $data['type']
              , 'message_level' => $data['message_level']
              , 'limit_date' => $data['limit_date']
              , 'enable' => 1
            )
        );

        return $this->curl->send($curl);
    }
    
    // メッセージ作成-tbl_message_detail部
    public function setMessageCreateInsertMessageDetail($data)
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'setMessageCreateInsertMessageDetail'
          , 'params' => array(
                'message_id' => $data['message_id']
              , 'message' => $data['message']
              , 'send_user_level_id' => 1
              , 'send_user_id' => $this->admin_id
              , 'enable' => 1
            )
        );

        return $this->curl->send($curl);
    }
    
    // メッセージ作成-tbl_message_target部
    public function setMessageCreateInsertMessageTarget($data)
    {
        $curl = array(
            'repository' => 'GetAdminMessageCreateModel'
          , 'method' => 'setMessageCreateInsertMessageTarget'
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
}
