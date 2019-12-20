<?php

class GetBit
{
    public $student_id;
    public $curl;

    public function __construct($curl) 
    {
        $this->curl = $curl;
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getSubjectBit($user_id, $user_type)
    {
        $curl = array(
            'repository' => 'GetBitModel'
          , 'method' => 'getSubjectBit'
          , 'params' => array(
                'user_id' => $user_id
              , 'user_type' => $user_type
            )
        );

        return $this->curl->send($curl);
    }

    public function setSubjectBit($user_id, $user_type, $bit_strings)
    {
        $curl = array(
            'repository' => 'GetBitModel'
          , 'method' => 'setSubjectBit'
          , 'params' => array(
                'user_id' => $user_id
              , 'user_type' => $user_type
              , 'bit_strings' => $bit_strings
            )
        );

        return $this->curl->send($curl);
    }
}
