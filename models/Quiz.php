<?php
class Quiz
{
    private $quiz_id;
    private $Curl;

    public function __construct($quiz_id, Curl $curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Curl = $curl;
    }

    /**
     * リダイレクト
     *
     * @param $path リダイレクト先
     */
    public function redirect($path)
    {
        $base = (empty($_SERVER["HTTPS"]) ? "http://" : "https://");
        $host  = $_SERVER['HTTP_HOST'];
        $uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        header("Location: $base$host$uri/$path");
        exit;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }

    public function getQuiz()
    {
        $data = array(
            'repository' => 'QuizRepository',
            'method' => 'findQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function findQuizOrderByRegisted()
    {
        $data = array(
            'repository' => 'QuizRepository',
            'method' => 'findQuizOrderByRegisted',
            'params' => array()
        );

        return $this->Curl->send($data);
    }

    public function deleteQuizId($quiz_id)
    {
        $data = array(
            'repository' => 'QuizRepository',
            'method' => 'deleteQuizId',
            'params' => array('quiz_id' => $quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function findQuiz()
    {
        $data = array(
            'repository' => 'QuizRepository',
            'method' => 'findQuiz',
            'params' => array()
        );

        return $this->Curl->send($data);
    }

    public function updateQuiz($data)
    {
        $curl_data = array(
                  'repository' => 'QuizRepository'
                , 'method' => 'updateBaseQuizId'
                , 'params' => $data
        );

        return $this->Curl->send($curl_data);
    }

    public function updateQuizConfirm($quiz_id)
    {
        $curl_data = array(
                  'repository' => 'QuizRepository'
                , 'method' => 'updateQuizConfirm'
                , 'params' => array('quiz_id' => $quiz_id)
        );

        return $this->Curl->send($curl_data);
    }

    //------ tbl_function_list ------//
    // 一覧情報の登録 (setter tbl_function_list)
    public function setFunctionList($data)
    {
        $curl = array(
            'repository' => 'QuizRepository'
          , 'method' => 'SetFunctionList'
          , 'params' => array(
                'type' => $data['type']
            )
        );

        return $this->Curl->send($curl);
    }
    //!!!!!! tbl_function_list !!!!!!//
}
