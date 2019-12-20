<?php

class AdminConfig
{
    public $curl;

    public function __construct($curl)
    {
        $this->Curl = $curl;
        $this->crypt = new StringEncrypt;
    }

    public function return_test ( $data ) {
      return $data;
    }

    // 管理者の取得
    public function selectAdmin($data, $mode)
    {
        //$modeの内容を確認('list' or 'person')
        // 管理者の取得(一覧)
        if ($mode == 'list') {
          $curl = array(
              'repository' => 'Core_AdminRepository'
            , 'method' => 'selectAdminList'
            , 'params' => array(
                  'school_id'  => $data['school_id']
                , 'own_id'  => $data['own_id']
              )
          );

          $data = $this->Curl->send($curl);
        // 管理者の取得(個別)
        } else if ($mode == 'list_all') {
          $curl = array(
              'repository' => 'Core_AdminRepository'
            , 'method' => 'selectAdminListAll'
            , 'params' => array(
                'school_id'  => $data['school_id']
              )
          );

          $data = $this->Curl->send($curl);
        // 管理者の取得(個別)
        } else if($mode == 'person') {
            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'selectAdminPerson'
              , 'params' => array(
                    'admin_id' => $data['admin_id']
                  , 'school_id' => $data['school_id']
                )
            );

            $data = $this->Curl->send($curl);

            $data['id'] = mb_strcut($this->crypt->decrypt($data['id']), 3);
            $data['password'] = $this->crypt->decrypt($data['password']);
        // 管理者の取得(レコード総数)
        } else if($mode == 'count') {
          $curl = array(
              'repository' => 'Core_AdminRepository'
            , 'method' => 'selectAdminCount'
            , 'params' => array(
                'own_id'  => $data['own_id']
              )
          );

          $data = $this->Curl->send($curl)['count'];
        }
        else if($mode == 'count_all') {
          $curl = array(
              'repository' => 'Core_AdminRepository'
            , 'method' => 'selectAdminCountAll'
            , 'params' => array(
            )
          );

          $data = $this->Curl->send($curl)['count'];
        }

        return $data;
    }

    // 管理者の登録・編集
    public function changeAdmin($data, $mode)
    {
        //$modeの内容を確認('insert' or 'edit')
        // 管理者の登録
        if($mode == 'insert') {
            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'insertAdmin'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'admin_name' => $data['admin_name']
                  , 'id' => $this->crypt->encrypt($data['call_sign'] . $data['id'])
                  , 'password' => $this->crypt->encrypt($data['password'])
                  , 'enable' => $data['enable']
                  , 'display_order' => $data['display_order']
                  , 'manage' => $data['manage']
                  , 'permission' => $data['permission']
                  , 'bit_subject' => $data['bit_subject']
                  )
            );
            //return $curl;
        // 管理者の編集
        } else if($mode == 'update') {
            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'updateAdmin'
              , 'params' => array(
                    'admin_id' => $data['admin_id']
                  , 'school_id' => $data['school_id']
                  , 'admin_name' => $data['admin_name']
                  , 'id' => $this->crypt->encrypt($data['call_sign'] . $data['id'])
                  , 'password' => $this->crypt->encrypt($data['password'])
                  , 'enable' => $data['enable']
                  , 'display_order' => $data['display_order']
                  , 'manage' => $data['manage']
                  , 'permission' => $data['permission']
                  , 'bit_subject' => $data['bit_subject']
                )
            );
        // 管理者の論理削除
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

    //管理者のログインIDの重複チェック
    public function checkAdminLoginId($data)
    {
        $curl = array(
            'repository' => 'Core_AdminRepository'
          , 'method' => 'checkAdminLoginId'
          , 'params' => array(
                'id' => $this->crypt->encrypt($data['call_sign'] . $data['id'])
            )
        );

        return $this->Curl->send($curl);
    }

    //------ ソート関連 ------//
    public function sortAdminDisplayOrder($data, $mode)
    {
        //選択した学生のdisplay_orderを取得
        $curl = array(
            'repository' => 'Core_AdminRepository'
          , 'method' => 'selectAdminPerson'
          , 'params' => array(
              'admin_id' => $data['admin_id'],
              'school_id' => $data['school_id']
              )
        );
        $data["display_order"] = $this->Curl->send($curl)["display_order"];

        //TOP
        if($mode == 'top') {

            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'getAdminDisplayOrderSortTop'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'own_id'  => $data['own_id']
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->Curl->send($curl);

        //UP
        } else if($mode == 'up') {

            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'getAdminDisplayOrderSortUp'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'own_id'  => $data['own_id']
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->Curl->send($curl);

        //DOWN
        } else if($mode == 'down') {

            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'getAdminDisplayOrderSortDown'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'own_id'  => $data['own_id']
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->Curl->send($curl);

        //BOTTOM
        } else if($mode == 'bottom') {

            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'getAdminDisplayOrderSortBottom'
              , 'params' => array(
                    'school_id' => $data['school_id']
                  , 'own_id'  => $data['own_id']
                  , 'display_order' => $data['display_order']
                )
            );
            $sql = $this->Curl->send($curl);

        } else {
            print_r("エラー：ソートファンクションエラー");
            exit();
        }

        //選択した学生以外のdisplay_orderを更新
        for($i = 1 ; count($sql) > $i ; $i++) {

            $curl = array(
                'repository' => 'Core_AdminRepository'
              , 'method' => 'setAdminDisplayOrder'
              , 'params' => array(
                    'admin_id' => $sql[$i]['admin_id']
                  , 'display_order' => $sql[$i - 1]['display_order']
                )
            );
            $this->Curl->send($curl);

        }

        //選択した学生のdisplay_orderを更新
        $curl = array(
            'repository' => 'Core_AdminRepository'
          , 'method' => 'setAdminDisplayOrder'
          , 'params' => array(
                'admin_id' => $sql[0]['admin_id']
              , 'display_order' => $sql[$i - 1]['display_order']
            )
        );
        $this->Curl->send($curl);

        return;
    }
}
