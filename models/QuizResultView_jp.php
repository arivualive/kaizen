<?php

class QuizResultView
{
    private $Result;

    public function __construct(QuizResult $result)
    {
        $this->Result = $result;
    }

    public function historyData()
    {
        return $this->Result->findQuizAnswerStudent();
    }

    /**
     * テストの履歴
     *
     * @return array $quizHistory
     */
    public function quizHistory($bid)
    {
        $quiz = $this->Result->getQuiz();

        $history = array();

        if ($this->historyData()) {
            foreach ((array) $this->historyData() as $key => $value) {
                if ($value['answer_id'] == $this->Result->getAnswerId()) {
                    $active = '<li class="active">';
                } else {
                    $active = '<li>';
                }

                $item = '';
                if ($quiz['qualifying_score'] == 0) {
                    $item  = $active;
                    $item .= sprintf('<a href="result.php?id=%s&an=%s&bid=%s">%d回目<span class="success">―</span></a>',
                            $value['quiz_id'], $value['answer_id'], $bid, $key+1);
                    $item .= "</li>";
                } else {

                    if ($quiz['qualifying_score'] > $value['total_score']) {
                        $item  = $active;
                        $item .= sprintf('<a href="result.php?id=%s&an=%s&bid=%s">%d回目<span class="fail">不合格</span></a>',
                                $value['quiz_id'], $value['answer_id'], $bid, $key+1);
                        $item .= "</li>";
                    }

                    if ($quiz['qualifying_score'] <= $value['total_score']) {
                        $item  = $active;
                        $item .= sprintf('<a href="result.php?id=%s&an=%s&bid=%s">%d回目<span class="success">合格</span></a>',
                                $value['quiz_id'], $value['answer_id'], $bid, $key+1);
                        $item .= "</li>";
                    }
                }

                $history[] = $item;
            }
        }

        return $history;
    }

    public function answerRegistDate()
    {
        if ($this->historyData()) {
            foreach ((array) $this->historyData() as $key => $value) {
                if ($value['answer_id'] == $this->Result->getAnswerId()) {
                    return $value['register_datetime'];
                }
            }
        }

    }

    public function totalScore()
    {
        if ($this->historyData()) {
            foreach ((array) $this->historyData() as $key => $value) {
                if ($value['answer_id'] == $this->Result->getAnswerId()) {
                    return $value['total_score'];
                }
            }
        }

    }

    /**
     * 合否
     *
     * @return string $judgment
     */
    public function isSuccess()
    {
        $quiz = $this->Result->getQuiz();

        if ($quiz['qualifying_score'] == 0 ) {
            return '―';
        }

        if ($quiz['qualifying_score'] <= $this->totalScore()) {
            return '合格';
        }

        return '不合格';
    }

    /**
     * 正解 or 不正解の表示
     *
     *@return array
     */
    public function showCorrect($answer_query)
    {
        $tag = array();

        foreach ((array) $answer_query as $key => $value) {

            if ($value['flg_right'] == 1) {
                $tag[$key] = '<p class="result correct">正解</p>';
            } else {
                $tag[$key] = '<p class="result incorrect">不正解</p>';
            }
        }

        return $tag;
    }

    /**
     * 選択肢に解答をマージ
     *
     * @return array 選択肢
     *
    public function correctSelection()
    {
        $selection_data = $this->getSelection();

        foreach ($selection_data as $i => $value){
            foreach ($value as $j => $item){
                $tag['correct_class'] = '<td class="incorrect">×</td>';
                if ($item['correct_flg'] == 1) {
                    $tag['correct_class'] = '<td class="correct">◯</td>';
                }
                //debug($item);
                $selection_data[$i][$j] += $tag;
            }
        }

        return $selection_data;
    }

    /**
     * quiz_id毎の選択肢の取得
     *
     * @return array 選択肢
     *
    public function getSelection()
    {
        $query = $this->Result->getQuery();

        foreach ((array) $query as $key => $value) {
            $selection[$key] = $this->Result->getSelection($value['query_id']);
        }

        return $selection;
    }


    /**
     * 選択した解答群
     *
     * @return array
     *
    public function choice()
    {
        #$query_data = $this->Result->findQueryQuizId();
        $query_data = $this->Result->getQueryId();
        //debug($query_data);

        foreach ((array) $query_data as $key => $value) {
            $choice[$key] = $this->Result->findAnswerChoice($value);
        }

        return $choice;
    }

    /**
     * 選択した解答をマージ
     *
     * @return array 選択した解答
     *
    public function choiceSelection()
    {
       # $selection_data = $this->correctSelection();
        $choice_data = $this->choice();

        //debug($choice_data);
        $choice = array();

        foreach ($choice_data as $value) {
            foreach ($value as $item) {
                $choice[] = $item['selection_id'];
            }
        }

        // //debug($choice);
        if (! empty($choice)) {
        // die("Error. " . __METHOD__) ;

            foreach ($selection_data as $i => $value) {
                foreach ($value as $j => $item) {

                    if (in_array($item['selection_id'], $choice)) {
                        $tag['choice_class'] = '<td class="user-answer"><span style="font-size:80%">あなたの解答</span></td>';
                    } else {
                        $tag['choice_class'] = '<td>&nbsp;</td>';
                    }

                    $selection_data[$i][$j] += $tag;
                }
            }
        }

        //debug($selection_data);
        return $selection_data;
    }
    */
}

?>
