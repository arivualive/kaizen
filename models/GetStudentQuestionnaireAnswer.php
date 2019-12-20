<?php

class GetStudentQuestionnaireAnswer
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $questionnaire_id, $curl) 
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->questionnaire_id = $questionnaire_id;
        $this->curl = $curl;
    }

    // アンケートIDからアンケートの質問内容を取得-タイトル
    public function getQuestionnaire()
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'getQuestionnaire'
          , 'params' => array(
                'questionnaire_id' => $this->questionnaire_id
            )
        );

        return $this->curl->send($curl);
    }

    // アンケートIDからアンケートの質問内容を取得-質問内容
    public function getQuestionnaireQuery()
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'getQuestionnaireQuery'
          , 'params' => array(
                'questionnaire_id' => $this->questionnaire_id
            )
        );

        return $this->curl->send($curl);
    }

    // 質問IDから単一/複数選択形式の設定内容を取得
    public function getQuestionnaireQueryChoices($data)
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'getQuestionnaireQueryChoices'
          , 'params' => array(
                'query_id' => $data
            )
        );

        return $this->curl->send($curl);
    }

    // 質問IDから数値回答形式の設定内容を取得
    public function getQuestionnaireQueryLength($data)
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'getQuestionnaireQueryLength'
          , 'params' => array(
                'query_id' => $data
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の回答データの保存その１-タイトル
    public function setQuestionnaireAnswer($data)
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswer'
          , 'params' => array(
                'questionnaire_id' => $this->questionnaire_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);;
    }

    // 学生の回答データの保存その２-質問内容
    public function setQuestionnaireAnswerQuery($data)
    {
        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswerQuery'
          , 'params' => array(
                'answer_id' => $data['answer_id']
              , 'query_id' => $data['query_id']
              , 'query_type' => $data['query_type']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の回答データの保存(単一選択)
    public function setQuestionnaireAnswerQuerySingleChoice($data)
    {
        //$sql_string = $this->setInsertString($data);

        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswerQuerySingleChoice'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
              , 'choice_id' => $data['query_data']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の回答データの保存(複数選択)
    public function setQuestionnaireAnswerQueryMultipleChoice($data)
    {
        $sql_string = $this->setInsertString($data);

        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswerQueryMultipleChoice'
          , 'params' => array(
                'sql_string' => $sql_string
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の回答データの保存(自由回答)
    public function setQuestionnaireAnswerQueryWord($data)
    {
        //$sql_string = $this->setInsertString($data);

        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswerQueryWord'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
              , 'word' => $data['query_data']
            )
        );

        return $this->curl->send($curl);
    }

    // 学生の回答データの保存(数値回答)
    public function setQuestionnaireAnswerQueryLength($data)
    {
        //$sql_string = $this->setInsertString($data);

        $curl = array(
            'repository' => 'GetStudentQuestionnaireAnswerModel'
          , 'method' => 'setQuestionnaireAnswerQueryLength'
          , 'params' => array(
                'answer_query_id' => $data['answer_query_id']
              , 'length' => $data['query_data']
            )
        );

        return $this->curl->send($curl);
    }

    // 複数INSERT処理対応
    public function setInsertString($data)
    {
        $string = "";
        for( $i = 0 ; $i < count($data['query_data']) ; $i++ ) {
            if($i == (count($data['query_data']) -1 ) ) {
                $string .= "(" . $data['answer_query_id'] . "," . $data['query_data'][$i] . ")";
            } else {
                $string .= "(" . $data['answer_query_id'] . "," . $data['query_data'][$i] . "),";
            }
        }

        return $string;
    }
}
