<?php

class GetAdminMovieRegist
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
            'repository' => 'GetAdminMovieRegistModel'
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
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'GetSubjectSection'
          , 'params' => array(
                'subject_genre_id' => $data['subject_genre_id']
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツの登録
    public function setContents($data)
    {
        $curl = array(
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'SetContents'
          , 'params' => array(
                'contents_category_id' => $data['contents_category_id']
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
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツ最大IDの取得
    public function getContentsMaxId()
    {
        $curl = array(
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'getContentsMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // コンテンツの格納
    public function moveUploadFileContents($data)
    {
        //$path = $_SERVER['DOCUMENT_ROOT'] . "/file/contents/" . $data['contents_id'] . $data['extension'];
        $path = "../../file/contents/" . $data['contents_id'] . $data['extension'];
        $tmp_name = $data["contents_tmp_name"];
        move_uploaded_file($tmp_name, $path);
    }

    // コンテンツ添付ファイルの登録
    public function setContentsAttachment($data)
    {
        $curl = array(
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'SetContentsAttachment'
          , 'params' => array(
                'contents_category_id' => $data['contents_category_id']
              , 'contents_id' => $data['contents_id']
              , 'file_name' => $data['attach_file_name']
            )
        );

        return $this->curl->send($curl);
    }

    // コンテンツ添付ファイル最大IDの取得
    public function getContentsAttachMaxId()
    {
        $curl = array(
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'getContentsAttachMaxId'
          , 'params' => array()
        );

        return $this->curl->send($curl);
    }

    // コンテンツ添付ファイルの格納
    public function moveUploadFileAttach($data)
    {
        //$path = $_SERVER['DOCUMENT_ROOT'] . "/file/attach/" . $data['attach_id'] . ".deploy";
        $path = "../../file/attach/" . $data['attach_id'] . ".deploy";
        $tmp_name = $data["attach_tmp_name"];
        move_uploaded_file($tmp_name, $path);
    }

    // 一覧情報の更新
    public function setFunctionList($data)
    {
        $curl = array(
            'repository' => 'GetAdminMovieRegistModel'
          , 'method' => 'SetFunctionList'
          , 'params' => array(
                'function_group_id' => $data['function_group_id']
              , 'type' => $data['type']
            )
        );

        return $this->curl->send($curl);
    }
}
