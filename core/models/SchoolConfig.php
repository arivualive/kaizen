<?php

class SchoolConfig
{
    public $curl;

    public function __construct($curl)
    {
        $this->Curl = $curl;
    }

    // 学校の取得
    public function selectSchool($data, $mode)
    {
        //$modeの内容を確認('list' or 'person')
        // 学校の取得(一覧)
        if($mode == 'list') {
            $curl = array(
                'repository' => 'Core_SchoolRepository'
              , 'method' => 'selectSchoolList'
              , 'params' => array(
                )
            );
        // 学校の取得(個別)
        } else if($mode == 'person') {
            $curl = array(
                'repository' => 'Core_SchoolRepository'
              , 'method' => 'selectSchoolPerson'
              , 'params' => array(
                    'school_id' => $data['school_id']
                )
            );
        } else if($mode == 'call_sign') {
            $curl = array(
                'repository' => 'Core_SchoolRepository'
              , 'method' => 'selectSchoolCallSign'
              , 'params' => array(
                    'school_id' => $data['school_id']
                )
            );
        }

        return $this->Curl->send($curl);
    }

    // 学校の登録・編集
    public function changeSchool($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit')
        // 学校の登録
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'Core_SchoolRepository'
              , 'method' => 'insertSchool'
              , 'params' => array(
                    'school_name' => $data['school_name']
                  , 'call_sign' => $data['call_sign']
                  , 'max_id_of_admin' => $data['max_id_of_admin']
                  , 'max_id_of_teacher' => $data['max_id_of_teacher']
                  , 'max_id_of_student' => $data['max_id_of_student']
                  , 'max_school_contents_total_giga_byte' => $data['max_school_contents_total_giga_byte']
                  , 'enable' => $data['enable']
                  , 'display_order' => $data['display_order']
                )
            );
        // 学校の編集
        } else if($mode == 'update') {
            $curl = array(
                'repository' => 'Core_SchoolRepository'
              , 'method' => 'updateSchool'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'school_name' => $data['school_name']
                  , 'call_sign' => $data['call_sign']
                  , 'max_id_of_admin' => $data['max_id_of_admin']
                  , 'max_id_of_teacher' => $data['max_id_of_teacher']
                  , 'max_id_of_student' => $data['max_id_of_student']
                  , 'max_school_contents_total_giga_byte' => $data['max_school_contents_total_giga_byte']
                  , 'enable' => $data['enable']
                  , 'display_order' => $data['display_order']
                )
            );
        // 学校の論理削除
        // 論理削除は編集によって行うため、実装しない
        //} else if($mode == 'delete') {
        //  $curl = array(
        //      'repository' => 'Core_SchoolRepository'
        //    , 'method' => 'deleteSchool'
        //    , 'params' => array(
        //          'school_id' => $data['school_id']
        //      )
        //  );
        }

        return $this->Curl->send($curl);
    }

}
