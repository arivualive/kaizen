<?php

class QuerySelection
{
  #  protected $QueryObj;
    protected $page;
   # protected $default_cnt;
    protected $quiz_id;
    protected $Curl;

    public function __construct($quiz_id, Curl $curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Curl = $curl;
       # $this->default_cnt = $default_cnt;
       # $this->QueryObj = $QueryObj;
    }

    /*
    public function __construct($default_cnt, $QueryObj, $curl)
    {
        $this->curl = $curl;
        $this->default_cnt = $default_cnt;
        $this->QueryObj = $QueryObj;
    }

    public function getCurl()
    {
        return $this->QueryObj->getCurl();
    }
    */

    /*
    public function quizId()
    {
        return $this->QueryObj->getQuizId();
    }
    */

    public function getSelection($query_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'findSelectionQueryId',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function getSelectionQuizId()
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'findSelectionQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);

        #$res = $this->Curl->send($data);
        //debug($res);
    }


    public function makeSelectionInput($query_id, $default_count = 5)
    {
        #$selection = $this->selectionCount($query_id);
        $selection = $this->getSelection($query_id);

        foreach ((array) $selection as $key => $value) {
            $select[$key]['selection_id'] = $value['selection_id'];
            $select[$key]['text'] = $value['text'];

            if ($value['correct_flg'] == 1) {
                $select[$key]['checked'] = 'checked';
            } else {
                $select[$key]['checked'] = '';
            }
        }

        // 2019/6/03 count関数対策
        $query_cnt = 0;
        if(is_countable($selection)){
          $query_cnt = count($selection);
        }
        //$query_cnt = count($selection);

        # デフォルト値より小さい場合に選択肢欄を追加する
        if ($query_cnt < $default_count) {
            for ($i = $query_cnt; $i < $default_count; $i++) {
                $select[$i]['selection_id'] = '';
                $select[$i]['text'] = '';
                $select[$i]['checked'] = '';
            }
        }

        return $select;
    }

    public function deleteMark($query_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'deleteMark',
            'params' => array(
                  'query_id' => $query_id
                , 'enable' => 0
            )
        );

        return $this->Curl->send($data);
    }

    public function updateSelection($query_id, $selection)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'updateSelection',
            'params' => array(
                  'query_id' => $query_id
                , 'text' => $selection['text']
                , 'display_order' => $selection['display_order']
                , 'correct_flg' => $selection['correct_flg']
                , 'selection_id' => $selection['selection_id']
            )
        );

        return $this->Curl->send($data);
    }

    public function insertSelection($query_id, $selection)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'insertSelection',
            'params' => array(
                  'quiz_id' => $this->quiz_id
                , 'query_id' => $query_id
                , 'text' => $selection['text']
                , 'display_order' => $selection['display_order']
                , 'correct_flg' => $selection['correct_flg']
            )
        );

        return $this->Curl->send($data);
    }

    public function insertSelectionNoData($query_id, $display_order)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'insertSelection',
            'params' => array(
                  'quiz_id' => $this->quiz_id
                , 'query_id' => $query_id
                , 'text' => ''
                , 'display_order' => $display_order
                , 'correct_flg' => 0
            )
        );

        return $this->Curl->send($data);
    }

    public function selectionText()
    {
        $selection_text = array();

        foreach ($_POST['text'] as $item) {
            # 空欄を除く
            if ($item != '') {
                $selection_text[] = htmlspecialchars($item);
            }
        }

        return $selection_text;
    }

    public function selectionId()
    {
        $selection_id = array();

        foreach ($_POST['selection_id'] as $item) {
            $selection_id[] = $item;
        }

        return $selection_id;
    }

    public function correctItem()
    {
        $correct_item = array();

        foreach ($_POST['correct'] as $item) {
            $correct_item[] = $item;
        }

        return $_POST['correct'];
    }

    public function saveSelection($query_id)
    {
        // 選択肢
        $data_text = $this->selectionText();

        // selection_id
        $data_id = $this->selectionId();

        // 正解フラグ
        $data_correct = $this->correctItem();
        //debug($data_correct);

        // enableを0にする
        $this->deleteMark($query_id);

        // update or insert
        foreach ($data_text as $key => $value) {
            $data['text'] = $value;
            $data['display_order'] = $key;
            $data['selection_id'] = $data_id[$key];

            if (isset($data_correct[$key]) && $data_correct[$key] > 0) {
                $data['correct_flg'] = 1;
            } else {
                $data['correct_flg'] = 0;
            }

            if ($data['selection_id'] != '') {
                $this->updateSelection($query_id, $data);
            } else {
                $this->insertSelection($query_id, $data);
            }
        }

        # print_r($this->saveCorrectItem());
    }

    /*
    public function saveCorrectItem()
    {
        $result = '';

        $selection_id = $this->selectionId();
        $correct_item = $this->correctItem();
      # print_r($correct_item);

        foreach ($correct_item as $item) {
            return $item;
        }

        foreach ($selection_id as $key => $value) {
            if ($key == $_POST['correct'][$key]) {
            }
        }

      #  return $correct_item_id;
    }
     */

    public function updateCorrectItem($selection_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'updateCorrectFlg',
            'params' => array('selection_id' => $selection_id)
        );

        return $this->Curl->send($data);
    }

    public function removeQuerySelection($query_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'removeQuerySelection',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function disableQuerySelection($query_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'disableQuerySelection',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function countCorrect($query_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'countCorrect',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function answerCount($selection_id)
    {
        $data = array(
            'repository' => 'QuerySelectionRepository',
            'method' => 'selectionCount',
            'params' => array(
                'selection_id' => $selection_id
            )
        );

        return $this->Curl->send($data);
    }

}
