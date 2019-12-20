<?php

class GetAdminContentsControl
{
    public $curl;

    public function __construct($school_id, $curl) 
    {
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getContents()
    {
        $curl = array(
            'repository' => 'GetAdminContentsControlModel'
          , 'method' => 'getContents'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからアンケートを取得
    public function getQuestionnaire($type)
    {
        $curl = array(
            'repository' => 'GetAdminContentsControlModel'
          , 'method' => 'getQuestionnaire'
          , 'params' => array(
                'type' => $type
              , 'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからテストを取得
    public function getQuiz()
    {
        $curl = array(
            'repository' => 'GetAdminContentsControlModel'
          , 'method' => 'getQuiz'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから科目・講座を取得
    public function getSubject()
    {
        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetAdminContentsControlModel'
          , 'method' => 'getSubject'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースから科目・講座を取得
    public function getSubjectSection($data)
    {
        // 参加年度・所属ユニット・コースの全てが対象となっている講義を取得
        $curl = array(
            'repository' => 'GetAdminContentsControlModel'
          , 'method' => 'getSubjectSection'
          , 'params' => array(
                'subject_genre_id' => $data['subject_genre_id']
            )
        );

        return $this->curl->send($curl);
    }
}
