<?php

class GetAdminMessageDetail
{
    public $student_id;
    public $curl;

    public function __construct($admin_id, $school_id, $message_id, $curl) 
    {
        $this->admin_id = $admin_id;
        $this->school_id = $school_id;
        $this->message_id = $message_id;
        $this->curl = $curl;
    }

    // スレッドデータを取得
    public function getMessageDetailTitle()
    {
        $curl = array(
            'repository' => 'GetAdminMessageDetailModel'
          , 'method' => 'getMessageDetailTitle'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'message_id' => $this->message_id
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージデータを取得
    public function getMessageDetailOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetAdminMessageDetailModel'
          , 'method' => 'getMessageDetailOffset'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'message_id' => $this->message_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから受信者を取得
    public function getMessageDetailReceiver($message_detail_id, $type)
    {
        $curl = array(
            'repository' => 'GetAdminMessageDetailModel'
          , 'method' => 'getMessageDetailReceiver'
          , 'params' => array(
                'message_detail_id' => $message_detail_id
              , 'type' => $type
            )
        );

        return $this->curl->send($curl);
    }

    // メッセージの削除
    public function setMessageDetailDelete($message_detail_id)
    {
        $curl = array(
            'repository' => 'GetAdminMessageDetailModel'
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
            'repository' => 'GetAdminMessageDetailModel'
          , 'method' => 'getMessageDetailDateCheck'
          , 'params' => array(
                'message_id' => $this->message_id
              , 'user_level_id' => 1
              , 'user_id' => $this->admin_id
            )
        );

        return $this->curl->send($curl);
    }
}
