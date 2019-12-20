<?php

class GetAdminQuestionnaireRegist
{
    public $curl;

    public function __construct($school_id, $curl) 
    {
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    // 科目の取得
    public function getSubject()
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'GetSubject'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 講座の取得
    public function getSubjectSection($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'GetSubjectSection'
          , 'params' => array(
                'subject_genre_id' => $data['subject_genre_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートの登録
    public function setQuestionnaire($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'SetQuestionnaire'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'title' => $data['title']
              , 'description' => $data['description']
              , 'finished_message' => $data['finished_message']
              , 'enable' => $data['enable']
              , 'start_day' => $data['start_day']
              , 'last_day' => $data['last_day']
              , 'user_level_id' => $data['user_level_id']
              , 'register_user_id' => $data['register_user_id']
              , 'type' => $data['type']
              , 'enable' => $data['enable']
              , 'bit_classroom' => $data['bit_classroom']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケート最大IDの取得
    public function getQuestionnaireMaxId()
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'getQuestionnaireMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリの登録
    public function setQuestionnaireQuery($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'SetQuestionnaireQuery'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
              , 'query' => $data['query']
              , 'query_type' => $data['query_type']
              , 'flg_query_must' => $data['flg_query_must']
              , 'enable' => $data['enable']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ最大IDの取得
    public function getQuestionnaireQueryMaxId()
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'getQuestionnaireQueryMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリの登録
    public function setQuestionnaireQueryChoices($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'SetQuestionnaireQueryChoices'
          , 'params' => array(
                'query_id' => $data['query_id']
              , 'text' => $data['text']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリの登録
    public function setQuestionnaireQueryLength($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'setQuestionnaireQueryLength'
          , 'params' => array(
                'query_id' => $data['query_id']
              , 'min_label' => $data['min_label']
              , 'max_label' => $data['max_label']
              , 'min_limit' => $data['min_limit']
              , 'max_limit' => $data['max_limit']
              , 'step' => $data['step']
            )
        );

        return $this->curl->send($curl);
    }

    // 一覧情報の更新
    public function setFunctionList($data)
    {
        $curl = array(
            'repository' => 'GetAdminQuestionnaireRegistModel'
          , 'method' => 'SetFunctionList'
          , 'params' => array(
                'subject_section_id' => $data['subject_section_id']
              , 'function_group_id' => $data['function_group_id']
              , 'type' => $data['type']+1
            )
        );

        return $this->curl->send($curl);
    }
}
