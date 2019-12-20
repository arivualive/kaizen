<?php

/**
 * QuizAnswerView
 *
 * @package
 * @version $id$
 * @copyright 2008-2018 KJS
 * @author Shinichiro Kondo <evah6809@gmail.com>
 * @license PHP Version 3.0 {@link https://www.php.net/license/3_0.txt}
 */
class QuizAnswerView
{
    protected $Quiz;
    protected $Query;
    protected $Selection;
    protected $Choice;
    protected $CurrentPage;

    /**
     * コンストラクタ
     *
     */
    public function __construct(Quiz $quiz, Query $query, QuerySelection $selection)
    {
        $this->Quiz = $quiz;
        $this->Query = $query;
        $this->Selection = $selection;
    }

    /**
     * リダイレクト
     * Quiz.phpに移動
     *
     * @param $path リダイレクト先
    public function redirect($path)
    {
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: https://$host$uri/$path");
        exit;
    }
     */

    /**
     * クイズ本体の取得
     * Quiz.phpに移動
     *
     * @return array クイズの基本情報
    public function getQuiz()
    {
        return $this->Quiz->getQuiz();
    }
     */

    /**
     * 問題の取得
     * 直接アクセスし取得 getQueryId();
     *
     * @return array 問題
     *
    public function getQueryData()
    {
        return $this->getQueryData();
    }
     */

    /**
     * 選択肢クラスのインスタンスを保存
     *
     * @param object 選択肢クラスのインスタンス
     */
    public function setChoice($choice)
    {
        $this->Choice = $choice;
    }

    /**
     * 出題数の取得
     *
     * @return integer 問題数
     */
    public function countQuery()
    {
        return $this->Query->countQuery();
    }

    /**
     * GETでページインデックスを取得
     *
     * @return integer $this->CurrentPage
     */
    public function setPage()
    {
        $this->CurrentPage = filter_input(INPUT_POST, 'current_page');

        if (! isset($this->CurrentPage) || empty($this->CurrentPage)) {
            $this->CurrentPage = 0;
        }

        if ($this->CurrentPage <= 0) {
            $this->CurrentPage = 0;
        }

        # 前へ
        if (filter_input(INPUT_POST, 'submit') == 'back') {
            $this->CurrentPage = $this->CurrentPage - 2;

            if ($this->CurrentPage <= 0) {
                $this->CurrentPage = 0;
            }
        }

        # 最大数
        $max_query = $this->countQuery();

        # 最後
        if ($this->CurrentPage >= $max_query) {
            $quiz_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $answer_id = filter_input(INPUT_GET, 'an', FILTER_SANITIZE_SPECIAL_CHARS);
            $bid = filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_SPECIAL_CHARS);

            $this->Quiz->redirect("end.php?id=$quiz_id&an=$answer_id&bid=$bid");
        }

        return $this->CurrentPage;
    }

    /**
     * queryデータをkey番号で取得
     *
     * @return array
     */
    public function getQueryId()
    {
        $data = $this->Query->getQuery();

        return $data[$this->CurrentPage];
    }

    /**
     * 解答形式の取得
     *
     * @return string 回答形式(単一、複数)
     */
    public function queryType()
    {
        $type = $this->Query->queryTypeArray();
        $data = $this->getQueryId();

        return $type[$data['query_type']];
    }

    /**
     * 選択肢の取得
     *
     * @return array 選択肢
     */
    public function getSelection()
    {
        $data = $this->getQueryId();

        return $this->Selection->getSelection($data['query_id']);
    }

    /**
     * 回答した選択肢の取得
     *
     * @return array 選択肢
     */
    public function getChoiceSelection()
    {
        $data = $this->getQueryId();

        if ($this->Choice->findAnswerChoice($data)) {
            return $this->Choice->findAnswerChoice($data);
        }

        return;
    }

    /**
     * 選択肢から選択した回答のkeyを見つける
     *
     * @return array 選択肢のkey
     */
    public function checkedSelection()
    {
        $choiceData = $this->getChoiceSelection();

        if (! isset($choiceData) || empty($choiceData)) {
            return;
        }

        $selectData = $this->getSelection();

        foreach ($choiceData as $key => $value) {
            $checkKey[] = array_search($choiceData[$key]['selection_id'], array_column($selectData, 'selection_id'));
        }

        return $checkKey;
    }

    /**
     * 選択した回答にcheckedを付加する
     *
     * @return array 選択肢
     */
    public function setChecked()
    {
        $checkKey = $this->checkedSelection();

        $selectData = $this->getSelection();

        if (! isset($checkKey) || empty($checkKey)) {
            return $selectData;
        }

        foreach($selectData as $key => $value) {
            foreach ($checkKey as $item) {
                if ($item == $key) {
                    $selectData[$key]['checked'] = ' checked';
                }
            }
        }

        return $selectData;
    }

    /**
     * htmlに渡す変数をまとる
     *
     * @return array data viewのhtmlに渡す変数 extractを使用する
     */
    public function showData()
    {
        # 現在のページ(問題)
        $data['p'] = $this->setPage();

        # クイズ本体の取得
        $data['quiz_data'] = $this->Quiz->getQuiz();

        # 問題数
        $data['max_query'] = $this->countQuery();

        # ページ毎の問題
        $data['query_data'] = $this->getQueryId();

        # 解答形式の取得
        $data['type_jp'] = $this->queryType();

        # 回答した選択肢の取得
        $data['selection_data'] = $this->setChecked();

        return $data;
    }
}
