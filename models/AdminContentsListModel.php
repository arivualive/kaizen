<?php

class AdminContentsListModel
{
    public $curl;

    public function __construct($school_id, $bit_classroom, $curl) 
    {
        $this->school_id = $school_id;
        $this->bit_classroom = $bit_classroom;
        $this->curl = $curl;
    }

    //------ tbl_contents ------//
    // コンテンツの取得 (getter tbl_contents)
    public function getContents()
    {
        $curl = array(
            'repository' => 'AdminContentsListRepository'
          , 'method' => 'getContents'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'bit_classroom' => $this->bit_classroom
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツの登録 (setter tbl_contents)
    // ContentsListで使うのは論理削除のみ
    public function setContents($data, $mode)
    {
        // コンテンツの論理削除 (setter tbl_contents)
        if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'DeleteContents'
              , 'params' => array(
                    'contents_id' => $data['primary_id']
                )
            );
        }

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_contents !!!!!!//

    //------ tbl_questionnaire ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire($type)
    {
        $curl = array(
            'repository' => 'AdminContentsListRepository'
          , 'method' => 'getQuestionnaire'
          , 'params' => array(
                'type' => $type
              , 'school_id' => $this->school_id
              , 'bit_classroom' => $this->bit_classroom
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートの登録 (setter tbl_questionnaire)
    // ContentsListで使うのは論理削除のみ
    public function setQuestionnaire($data, $mode)
    {
        // アンケートの論理削除 (setter tbl_questionnaire)
        if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'DeleteQuestionnaire'
              , 'params' => array(
                    'questionnaire_id' => $data['primary_id']
                )
            );
        }

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_questionnaire !!!!!!//

    //------ tbl_quiz ------//
    // コンテンツの取得 (getter tbl_quiz)
    public function getQuiz()
    {
        $curl = array(
            'repository' => 'AdminContentsListRepository'
          , 'method' => 'getQuiz'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'bit_classroom' => $this->bit_classroom
            )
        );

        return $this->curl->send($curl);
    }

    // クイズの登録 (setter tbl_quiz)
    // QuizListで使うのは論理削除のみ
    public function setQuiz($data, $mode)
    {
        // クイズの論理削除 (setter tbl_quiz)
        if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'DeleteQuiz'
              , 'params' => array(
                    'quiz_id' => $data['primary_id']
                )
            );
        }

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_quiz !!!!!!//


    //------ tbl_function_group ------//
    // フォルダの取得 (getter tbl_function_group)
    public function getFunctionGroup()
    {
        $curl = array(
            'repository' => 'AdminContentsListRepository'
          , 'method' => 'getFunctionGroup'
          , 'params' => array(
                'school_id' => $this->school_id
              , 'bit_classroom' => $this->bit_classroom
            )
        );

        return $this->curl->send($curl);
    }

    public function getFunctionGroupMaxId()
    {
        $curl = array(
            'repository' => 'AdminContentsListRepository'
          , 'method' => 'getFunctionGroupMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // フォルダの登録 (setter tbl_function_group)
    public function setFunctionGroup($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit' or 'delete')
        // コンテンツの挿入 (setter tbl_function_group)
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'InsertFunctionGroup'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'function_group_name' => $data['function_group_name']
                  , 'bit_classroom' => $this->bit_classroom
                )
            );
        // フォルダの編集 (setter tbl_function_group)
        } else if($mode == 'edit') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'EditFunctionGroup'
              , 'params' => array(
                    'function_group_id' => $data['function_group_id']
                  , 'function_group_name' => $data['function_group_name']
                )
            );
        // フォルダの論理削除 (setter tbl_function_group)
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'DeleteFunctionGroup'
              , 'params' => array(
                    'function_group_id' => $data['function_group_id']
                )
            );
        }

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_function_group !!!!!!//

    //------ tbl_function_list ------//
    // コンテンツ・アンケート・レポート・クイズのソート処理 (getter tbl_function_list)
    public function setFunctionList($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit' or 'delete')
        // コンテンツの挿入 (setter tbl_function_group)
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'InsertFunctionList'
              , 'params' => array(
                    'type' => $data['type']
                  , 'primary_id' => $data['primary_id']
                  , 'parent_function_group_id' => $data['parent_function_group_id']
                )
            );
        // フォルダの編集_ソート (setter tbl_function_group)
        } else if($mode == 's_edit') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'SortEditFunctionList'
              , 'params' => array(
                    'type' => $data['type']
                  , 'primary_id' => $data['primary_id']
                  , 'display_order' => $data['display_order']
                )
            );
        // フォルダの編集_フォルダ (setter tbl_function_group)
        } else if($mode == 'f_edit') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'FolderEditFunctionList'
              , 'params' => array(
                    'type' => $data['type']
                  , 'primary_id' => $data['primary_id']
                  , 'parent_function_group_id' => $data['parent_function_group_id']
                )
            );
        // フォルダの論理削除 (setter tbl_function_group)
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminContentsListRepository'
              , 'method' => 'deleteFunctionList'
              , 'params' => array(
                    'parent_function_group_id' => $data['parent_function_group_id']
                )
            );
        }

        // クイズの論理削除 (setter tbl_quiz)

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_function_list !!!!!!//
}
