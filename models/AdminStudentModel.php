<?php

class AdminStudentModel
{
    public $curl;

    public function __construct($school_id, $curl)
    {
        $this->school_id = $school_id;
        $this->curl = $curl;

        $this->crypt = new StringEncrypt;
    }

    // 学生の取得
    public function getStudent($data, $mode)
    {
        //$modeの内容を確認('list' or 'person')
        // 学生の取得(一覧)
        if($mode == 'list') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'GetStudentList'
              , 'params' => array(
                    'school_id' => $this->school_id
                )
            );
        // 学生の取得(個人)
        } else if($mode == 'person') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'GetStudentPerson'
              , 'params' => array(
                    'student_id' => $data['student_id']
                )
            );
        } else if($mode == 'already') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'GetStudentAlready'
              , 'params' => array(
                    'id' => $this->crypt->encrypt($data['id'])
                )
            );
            return $this->curl->send($curl);
        }

        $data = $this->curl->send($curl);

        for($i = 0 ; $i < count($data) ; $i++) {
            $data[$i]['id'] = mb_strcut($this->crypt->decrypt($data[$i]['id']), 3);
            $data[$i]['password'] = $this->crypt->decrypt($data[$i]['password']);
        }

        return $data;
    }

    // 学生人数の取得
    public function getStudentCount()
    {
        $curl = array(
            'repository' => 'AdminStudentRepository'
          , 'method' => 'GetStudentCount'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の登録・編集
    public function setStudent($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit' or delete)
        // 学生の登録
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'InsertStudent'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'student_code' => $data['student_code']
                  , 'student_name' => $data['student_name']
                  , 'joining' => $data['joining']
                  , 'id' => $this->crypt->encrypt($data['id'])
                  , 'password' => $this->crypt->encrypt($data['password'])
                  , 'bit_subject' => $data['bit_subject']
                )
            );
        // 学生の編集
        } else if($mode == 'edit') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'EditStudent'
              , 'params' => array(
                    'student_id' => $data['student_id']
                  , 'student_code' => $data['student_code']
                  , 'student_name' => $data['student_name']
                  , 'joining' => $data['joining']
                  , 'id' => $this->crypt->encrypt($data['id'])
                  , 'password' => $this->crypt->encrypt($data['password'])
                  , 'bit_subject' => $data['bit_subject']
                )
            );
        // 学生の論理削除
        } else if($mode == 'delete') {
            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'DeleteStudent'
              , 'params' => array(
                    'student_id' => $data['student_id']
                )
            );
        }

        return $this->curl->send($curl);
    }

    //学生IDの重複チェック
    public function checkStudentId($data)
    {
        $curl = array(
            'repository' => 'AdminStudentRepository'
          , 'method' => 'CheckStudentId'
          , 'params' => array(
                'id' => $this->crypt->encrypt($data['id'])
            )
        );

        return $this->curl->send($curl);
    }

    // コールサインの取得
    public function getCallSign()
    {
        $curl = array(
            'repository' => 'AdminStudentRepository'
          , 'method' => 'GetCallSign'
          , 'params' => array(
                'school_id' => $this->school_id
            )
        );

        return $this->curl->send($curl);
    }

    //------ ソート関連 ------//

    // TOP
    public function sortStudentDisplayOrder($data, $mode)
    {
        //選択した学生のdisplay_orderを取得
        $curl = array(
            'repository' => 'AdminStudentRepository'
          , 'method' => 'getStudentDisplayOrder'
          , 'params' => array(
                'student_id' => $data['student_id']
            )
        );
        $data["display_order"] = $this->curl->send($curl)["display_order"];

        //TOP
        if($mode == 'top') {

            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'getStudentDisplayOrderSortTop'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->curl->send($curl);

        //UP
        } else if($mode == 'up') {

            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'getStudentDisplayOrderSortUp'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->curl->send($curl);

        //DOWN
        } else if($mode == 'down') {

            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'getStudentDisplayOrderSortDown'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->curl->send($curl);

        //BOTTOM
        } else if($mode == 'bottom') {

            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'getStudentDisplayOrderSortBottom'
              , 'params' => array(
                    'school_id' => $this->school_id
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->curl->send($curl);

        } else {
            print_r("Error: Sort function error");
            exit();
        }

        //選択した学生以外のdisplay_orderを更新
        for($i = 1 ; count($sql) > $i ; $i++) {

            $curl = array(
                'repository' => 'AdminStudentRepository'
              , 'method' => 'setStudentDisplayOrder'
              , 'params' => array(
                    'student_id' => $sql[$i]['student_id']
                  , 'display_order' => $sql[$i - 1]['display_order']
                )
            );
            $this->curl->send($curl);

        }

        //選択した学生のdisplay_orderを更新
        $curl = array(
            'repository' => 'AdminStudentRepository'
          , 'method' => 'setStudentDisplayOrder'
          , 'params' => array(
                'student_id' => $sql[0]['student_id']
              , 'display_order' => $sql[$i - 1]['display_order']
            )
        );
        $this->curl->send($curl);

        return;
    }
}
