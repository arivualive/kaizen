<?php

class GetAdminMessageList
{
    public $admin_id;
    public $curl;

    public function __construct($admin_id, $school_id, $curl) 
    {
        $this->admin_id = $admin_id;
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getMessageListCount()
    {
        $curl = array(
            'repository' => 'GetAdminMessageListModel'
          , 'method' => 'getMessageListCount'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getMessageListOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetAdminMessageListModel'
          , 'method' => 'getMessageListOffset'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'school_id' => $this->school_id
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
            'repository' => 'GetAdminMessageListModel'
          , 'method' => 'getNoticeListCount'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    public function getNoticeListOffset($limit, $offset)
    {
        $curl = array(
            'repository' => 'GetAdminMessageListModel'
          , 'method' => 'getNoticeListOffset'
          , 'params' => array(
                'admin_id' => $this->admin_id
              , 'school_id' => $this->school_id
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
            'repository' => 'GetAdminMessageListModel'
          , 'method' => 'setMessageListDelete'
          , 'params' => array(
                'message_id' => $message_id
            )
        );

        return $this->curl->send($curl);
    }
}
