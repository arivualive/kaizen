<?php

class AdminMovieRegistModel
{
    public $curl;

    public function __construct($school_id, $curl)
    {
        $this->school_id = $school_id;
        $this->curl = $curl;
    }

    //------ tbl_contents ------//
    // コンテンツの取得 (getter tbl_contents)
    public function getContents($data)
    {
        $curl = array(
            'repository' => 'AdminMovieRegistRepository'
          , 'method' => 'GetContents'
          , 'params' => array(
                'contents_id' => $data['contents_id']
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツの登録 (setter tbl_contents)
    public function setContents($data, $mode)
    { //return $data;
        //$modeの内容を確認('insert' or 'edit' or 'delete')
        // コンテンツの挿入 (setter tbl_contents)
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'InsertContents'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'contents_category_id' => $data['contents_category_id']
                  , 'subject_section_id' => $data['subject_section_id']
                  , 'contents_name' => $data['contents_name']
                  , 'comment' => $data['comment']
                  , 'first_day' => $data['first_day']
                  , 'last_day' => $data['last_day']
                  , 'file_name' => $data['contents_file_name']
                  , 'user_level_id' => $data['user_level_id']
                  , 'register_id' => $data['register_id']
                  , 'contents_extension_id' => $data['contents_extension_id']
                  , 'size' => $data['size']
                  , 'enable' => $data['enable']
                  , 'bit_classroom' => $data['bit_classroom']
                  , 'proportion' => $data['proportion']
                )
            );
        // コンテンツの編集 (setter tbl_contents)
        } else if($mode == 'edit') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'EditContents'
              , 'params' => array(
                    'contents_id' => $data['contents_id']
                  , 'contents_name' => $data['contents_name']
                  , 'comment' => $data['comment']
                  , 'first_day' => $data['first_day']
                  , 'last_day' => $data['last_day']
                  , 'bit_classroom' => $data['bit_classroom']
                )
            );
        // コンテンツの論理削除 (setter tbl_contents)
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'DeleteContents'
              , 'params' => array(
                    'contents_id' => $data['contents_id']
                )
            );
        }
        return $this->curl->send($curl);
    }

    // コンテンツ最大IDの取得 (getter tbl_contents MAX_ID)
    public function getContentsMaxId()
    {
        $curl = array(
            'repository' => 'AdminMovieRegistRepository'
          , 'method' => 'getContentsMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // コンテンツファイルの移動 (-)
    public function moveUploadFileContents($data)
    { //return $data;
        //$path = $_SERVER['DOCUMENT_ROOT'] . "/file/contents/" . $data['contents_id'] . $data['extension'];
        $path = "../../file/contents/" . $data['contents_id'] . $data['extension'];
        $tmp_name = $data["contents_tmp_name"];
        move_uploaded_file($tmp_name, $path);
    }
    //!!!!!! tbl_contents !!!!!!//

    //------ tbl_contents_attachment ------//
    // コンテンツ添付ファイルの取得 (getter tbl_contents_attachment)
    public function getContentsAttachment($data)
    {
        $curl = array(
            'repository' => 'AdminMovieRegistRepository'
          , 'method' => 'GetContentsAttachment'
          , 'params' => array(
                'contents_id' => $data['contents_id']
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツ添付ファイルの登録 (setter tbl_contents_attachment)
    public function setContentsAttachment($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit' or 'delete')
        // コンテンツ添付ファイルの挿入 (setter tbl_contents_attachment)
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'InsertContentsAttachment'
              , 'params' => array(
                    'contents_category_id' => $data['contents_category_id']
                  , 'contents_id' => $data['contents_id']
                  , 'file_name' => $data['attach_file_name']
                )
            );
        // コンテンツ添付ファイルの編集 (setter tbl_contents_attachment)
        } else if($mode == 'edit') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'EditContentsAttachment'
              , 'params' => array(
                    'contents_category_id' => $data['contents_category_id']
                  , 'contents_id' => $data['contents_id']
                  , 'file_name' => $data['attach_file_name']
                )
            );
        // コンテンツ添付ファイルの物理削除 (delete tbl_contents_attachment)
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminMovieRegistRepository'
              , 'method' => 'DeleteContentsAttachment'
              , 'params' => array(
                    'contents_id' => $data['contents_id']
                )
            );
        }
        return $this->curl->send($curl);
    }

    // コンテンツ添付ファイル最大IDの取得 (getter tbl_contents_attachment MAX_ID)
    public function getContentsAttachMaxId()
    {
        $curl = array(
            'repository' => 'AdminMovieRegistRepository'
          , 'method' => 'getContentsAttachMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // コンテンツ添付ファイルの移動 (-)
    public function moveUploadFileAttach($data)
    {
        //$path = $_SERVER['DOCUMENT_ROOT'] . "/file/attach/" . $data['attach_id'] . ".deploy";
        $path = "../../file/attach/" . $data['attach_id'] . ".deploy";
        $tmp_name = $data["attach_tmp_name"];
        move_uploaded_file($tmp_name, $path);
    }
    //!!!!!! tbl_contents_attachment !!!!!!//

    //------ tbl_function_list ------//
    // 一覧情報の登録 (setter tbl_function_list)
    public function setFunctionList($data)
    {
        $curl = array(
            'repository' => 'AdminMovieRegistRepository'
          , 'method' => 'SetFunctionList'
          , 'params' => array(
                'function_group_id' => $data['function_group_id']
              , 'type' => $data['type']
            )
        );

        return $this->curl->send($curl);
    }
    //!!!!!! tbl_function_list !!!!!!//
}
