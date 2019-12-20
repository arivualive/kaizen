<?php

class AdminQuestionnaireModel
{
    public $curl;

    public function __construct($school_id, $curl) 
    {
        $this->school_id = $school_id;
        $this->curl = $curl;

        $this->crypt = new StringEncrypt;
    }

    //------ tbl_questionnaire ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaire'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートの登録 (setter tbl_questionnaire)
    public function setQuestionnaire($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit')
        // アンケートの挿入 (setter tbl_questionnaire)
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminQuestionnaireRepository'
              , 'method' => 'InsertQuestionnaire'
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
        // アンケートの編集 (setter tbl_questionnaire)
        } else if($mode == 'edit') {
            $curl = array(
                'repository' => 'AdminQuestionnaireRepository'
              , 'method' => 'EditQuestionnaire'
              , 'params' => array(
                    'questionnaire_id' => $data['questionnaire_id']
                  , 'title' => $data['title']
                  , 'description' => $data['description']
                  , 'finished_message' => $data['finished_message']
                  , 'start_day' => $data['start_day']
                  , 'last_day' => $data['last_day']
                  , 'bit_classroom' => $data['bit_classroom']
                )
            );
        // アンケートの論理削除 (setter tbl_questionnaire)
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'GetAdminStudentModel'
              , 'method' => 'DeleteQuestionnaire'
              , 'params' => array(
                    'questionnaire_id' => $data['questionnaire_id']
                )
            );
        }

        return $this->curl->send($curl);
    }

    // アンケート最大IDの取得 (getter tbl_questionnaire MAX_ID)
    public function getQuestionnaireMaxId()
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'getQuestionnaireMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire !!!!!!//

    //------ tbl_questionnaire_query ------//
    // アンケートクエリの取得 (getter tbl_questionnaire_query)
    public function getQuestionnaireQuery($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQuery'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリの登録 (setter tbl_questionnaire_query)
    public function setQuestionnaireQuery($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
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

    // アンケートクエリの物理削除 (delete tbl_questionnaire_query)
    public function deleteQuestionnaireQuery($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'DeleteQuestionnaireQuery'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ最大IDの取得 (getter tbl_questionnaire_query MAX_ID)
    public function getQuestionnaireQueryMaxId()
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'getQuestionnaireQueryMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire_query !!!!!!//

    //------ tbl_questionnaire_query_choices ------//
    // アンケートクエリ単一・複数選択形式の取得 (getter tbl_questionnaire_query_choices)
    public function getQuestionnaireQueryChoices($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQueryChoices'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ単一・複数選択形式の登録 (setter tbl_questionnaire_query_choices)
    public function setQuestionnaireQueryChoices($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'SetQuestionnaireQueryChoices'
          , 'params' => array(
                'query_id' => $data['query_id']
              , 'text' => $data['text']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ単一・複数選択形式の物理削除 (delete tbl_questionnaire_query_choices)
    public function deleteQuestionnaireQueryChoices($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'DeleteQuestionnaireQueryChoices'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire_query_choices !!!!!!//

    //------ tbl_questionnaire_query_length ------//
    // アンケートクエリ数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireQueryLength($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQueryLength'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ数値回答形式の登録 (setter tbl_questionnaire_query_length)
    public function setQuestionnaireQueryLength($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
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

    // アンケートクエリ数値回答形式の物理削除 (delete tbl_questionnaire_query_length)
    public function deleteQuestionnaireQueryLength($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'DeleteQuestionnaireQueryLength'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire_query_length !!!!!!//

    //------ tbl_questionnaire_answer ------//
    public function deleteQuestionnaireAnswer($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'DeleteQuestionnaireAnswer'
          , 'params' => array(
                'answer_id' => $data
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire_answer !!!!!!//

    //------ tbl_questionnaire_answer_query ------//
    public function getQuestionnaireAnswerQuery($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQuery'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire_answer_query !!!!!!//

    //------ アンケート・レポート集計画面用 ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire_AnswerCount($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaire_AnswerCount'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリの取得 (getter tbl_questionnaire_query)
    public function getQuestionnaireQuery_AnswerCount($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQuery_AnswerCount'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ単一選択形式の取得 (getter tbl_questionnaire_query_choices)
    public function getQuestionnaireQuerySingleChoices_Analysis($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQuerySingleChoices_Analysis'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ複数選択形式の取得 (getter tbl_questionnaire_query_choices)
    public function getQuestionnaireQueryMultipleChoices_Analysis($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQueryMultipleChoices_Analysis'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートクエリ数値回答形式の取得 (getter tbl_questionnaire_query_word)
    public function getQuestionnaireQueryWord_Analysis($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQueryWord_Analysis'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        $data = $this->curl->send($curl);

        for($i = 0 ; $i < count($data) ; $i++) {
            $data[$i]['id'] = mb_strcut($this->crypt->decrypt($data[$i]['id']), 3);
        }

        return $data;
    }

    // アンケートクエリ数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireQueryLength_Analysis($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireQueryLength_Analysis'
          , 'params' => array(
                'query_id' => $data['query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生回答情報-答案受講者一覧の取得 (getter tbl_questionnaire_query_single_choice)
    public function getStudent_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetStudent_Student'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
              , 'school_id' => $this->school_id
            )
        );

        $data = $this->curl->send($curl);

        for($i = 0 ; $i < count($data) ; $i++) {
            $data[$i]['id'] = mb_strcut($this->crypt->decrypt($data[$i]['id']), 3);
        }

        return $data;
    }

    // 学生回答情報-質問回答情報の取得 (getter tbl_questionnaire_query_single_choice)
    public function getQuestionnaireAnswerQuery_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQuery_Student'
          , 'params' => array(
                'questionnaire_id' => $data['questionnaire_id']
              , 'answer_id' => $data['answer_id']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生回答情報-単一選択形式の取得 (getter tbl_questionnaire_query_single_choice)
    public function getQuestionnaireAnswerQuerySingleChoice_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQuerySingleChoice_Student'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生回答情報-複数選択形式の取得 (getter tbl_questionnaire_query_multiple_choice)
    public function getQuestionnaireAnswerQueryMultipleChoice_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQueryMultipleChoice_Student'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
            )
        );

        return $this->curl->send($curl);
    }


    // 学生回答情報-自由回答形式の取得 (getter tbl_questionnaire_query_word)
    public function getQuestionnaireAnswerQueryWord_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQueryWord_Student'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生回答情報-数値回答形式の取得 (getter tbl_questionnaire_query_length)
    public function getQuestionnaireAnswerQueryLength_Student($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'GetQuestionnaireAnswerQueryLength_Student'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生回答情報-質問回答情報の取得 (getter tbl_questionnaire_query_single_choice)

    //!!!!!! アンケート・レポート集計画面用 !!!!!!//

    //------ tbl_function_list ------//
    // 一覧情報の登録 (setter tbl_function_list)
    public function setFunctionList($data)
    {
        $curl = array(
            'repository' => 'AdminQuestionnaireRepository'
          , 'method' => 'SetFunctionList'
          , 'params' => array(
                'function_group_id' => $data['function_group_id']
              , 'type' => $data['type']+1
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_function_list !!!!!!//
}
