<?php

class Query
{
    protected $Curl;
    protected $quiz_id;
    protected $query_data;
    protected $query_id;

    public function __construct($quiz_id, Curl $curl)
    {
        $this->quiz_id = $quiz_id;
        $this->Curl = $curl;
    }

    public function setQueryData($query_data)
    {
       $this->query_data = $query_data;
    }

    public function getQuery()
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function setQueryId($query_id)
    {
        $this->query_id = $query_id;
    }

    public function getQueryId($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryId',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function queryTypeAll()
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'queryTypeAll'
        );

        return $this->Curl->send($data);
    }

    public function queryTypeArray()
    {
        $result = array();

        $query_type = $this->queryTypeAll();

        foreach ((array) $query_type as $item) {
            $result["{$item['query_type_id']}"] = $item['type_jp'];
        }

        return $result;
    }

    public function getSelection($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findSelectionQueryId',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function getQuerySelection()
    {
        #$query_data = $this->getQuery();

        foreach ($this->getQuery() as $key => $value) {
            $selection_data[$key] = $this->getSelection($value['query_id']);
        }

        return $selection_data;
    }

    public function getQueryInfo($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryId',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    /*
     * queryFollowに移動
     *
    public function getContents()
    {
        $quiz = $this->getQuiz();

        $data = array(
            'repository' => 'ContentsRepository',
            'method' => 'findSubjectSectionEnable',
            'params' => array('subject_section_id' => $quiz['subject_section_id'])
        );

        return $this->curl->send($data);
    }

    public function findFollowContents($contents_id)
    {
        $data = array(
            'repository' => 'FollowContentsRepository',
            'method' => 'findFollowContentsContentsId',
            'params' => array(
                'contents_id' => $contents_id
            )
        );

        return $this->curl->send($data);
    }

    public function checkedFollowContents($follow_data)
    {
        $data = array(
            'repository' => 'FollowContentsRepository',
            'method' => 'findFollowContentsQueryId',
            'params' => array(
                'quiz_query_id' => $follow_data['quiz_query_id'],
                'contents_category_id' => $follow_data['contents_category_id']
            )
        );

        return $this->curl->send($data);
    }

    public function deleteFollowContents($follow_data)
    {
        $data = array(
            'repository' => 'FollowContentsRepository',
            'method' => 'deleteFollowContents',
            'params' => array(
                'quiz_query_id' => $follow_data['quiz_query_id'],
                'contents_category_id' => $follow_data['contents_category_id']
            )
        );

        return $this->curl->send($data);
    }

    public function saveFollowContents($follow_data)
    {
        $data = array(
            'repository' => 'FollowContentsRepository',
            'method' => 'saveFollowContents',
            'params' => array(
                'quiz_query_id' => $follow_data['quiz_query_id'],
                'contents_id' => $follow_data['contents_id'],
                'contents_category_id' => $follow_data['contents_category_id']
            )
        );

        return $this->curl->send($data);
    }
     */

    public function insertQuery($quiz_id, $display_order)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'insertQuery',
            'params' => array('quiz_id' => $quiz_id, 'display_order' => $display_order)
        );

        return $this->Curl->send($data);
    }

    public function updateQuery($post_data)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'updateQuery',
            'params' => $post_data
        );

        return $this->Curl->send($data);
    }

    public function updateQueryType($post_data)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'updateQueryType',
            'params' => array(
                'query_id' => $post_data['query_id'],
                'query_type' => $post_data['query_type']
            )
        );

        return $this->Curl->send($data);
    }

    public function removeQuery($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'removeQuery',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function disableQuery($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'disableQuery',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    /*
     * 削除予定
     */
    public function countQuery()
    {
       # $query_data = $this->getQueryData();
        $query_data = $this->getQuery();
        // 2019/6/03 count関数対策
        if(is_countable($query_data)){
          return count($query_data);
        }
        //return count($query_data);
    }

    public function showQueryQuizId()
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function showQuery()
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'findQueryQuizId',
            'params' => array('quiz_id' => $this->quiz_id)
        );

        return $this->Curl->send($data);
    }

    public function showQueryAnswerNumber()
    {
        $data = array(
            'repository' => 'QuizAnswerQueryRepository',
            'method' => 'findAnswerQuery',
            'params' => array('answer_query_id' => $this->answer_query_id)
        );

        return $this->Curl->send($data);
    }

    public function queryNav()
    {
        $nav = array();

        foreach ($this->query_data as $key => $value) {
            $nav[$key]['quiz_id'] = $this->quiz_id;
            $nav[$key]['query_id'] = $value['query_id'];
            $nav[$key]['title'] = mb_strimwidth($value['query_text'], 0, 20, '...', 'UTF-8');
            $nav[$key]['p'] = $key;
        }

        return $nav;
    }

    /*
     * 削除予定
     */
    public function makeQueryNav()
    {
        $queryData = $this->getQuery();
        $nav = array();

        foreach ($queryData as $key => $value) {
            $nav[$key]['quiz_id'] = $this->quiz_id;
            $nav[$key]['query_id'] = $value['query_id'];
            $nav[$key]['title'] = mb_strimwidth($value['query_text'], 0, 20, '...', 'UTF-8');
            $nav[$key]['p'] = $key;
        }

        return $nav;
    }

    public function imageFileUpload()
    {
        if (isset($_FILES['images']) && $_FILES['images']['name'][0] != '') {
            $image = array();

            $image['file_name'] = $_FILES['images']['name'][0];
            $image['tmp_path'] = $_FILES['images']['tmp_name'][0];
            $image['file_dir_path'] = __dir__ . "/../file/image/";
            $image['uniq_name'] = $this->quiz_id . '_' . $this->query_id . '.deploy';

            if (is_uploaded_file($image['tmp_path'])) {
                if(move_uploaded_file( $image['tmp_path'], $image['file_dir_path'] . $image['uniq_name'])) {
                    chmod($image['file_dir_path'] . $image['uniq_name'], 0644);

                    //データベース書き込み
                    $data = array();
                    $data['query_id'] = $this->query_id;
                    $data['image_file_name'] = $image['file_name'];
                    $result = $this->updateImageFile($data);
                } else {
                    $result = "Error:Could not upload.";
                }
            }

            return $result;
        }
    }

    public function updateImageFile($image_data)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'updateImageFile',
            'params' => array(
                'query_id' => $image_data['query_id']
              , 'image_file_name' => $image_data['image_file_name']
            )
        );

        return $this->Curl->send($data);
    }

    public function soundFileUpload($query_id)
    {
        if (isset($_FILES['sound']) && $_FILES['sound']['name'] != '') {
            $sound = array();

            $sound['file_name'] = $_FILES['sound']['name'];
            $sound['tmp_path'] = $_FILES['sound']['tmp_name'];
            $sound['file_dir_path'] = __dir__ . "/../file/sound/";
            $sound['uniq_name'] = $this->quiz_id . '_' . $query_id . '.deploy';

            if (is_uploaded_file($sound['tmp_path'])) {
                if(move_uploaded_file( $sound['tmp_path'], $sound['file_dir_path'] . $sound['uniq_name'])) {
                    chmod($sound['file_dir_path'] . $sound['uniq_name'], 0644);

                    //データベース書き込み
                    $data = array();
                    $data['query_id'] = $query_id;
                    $data['sound_file_name'] = $sound['file_name'];
                    $result = $this->updateSoundFile($data);
                } else {
                    $result = "Error:Could not upload.";
                }
            }

            return $result;
        }
    }

    public function updateSoundFile($sound_data)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'updateSoundFile',
            'params' => array(
                'query_id' => $sound_data['query_id']
              , 'sound_file_name' => $sound_data['sound_file_name']
            )
        );

        return $this->Curl->send($data);
    }

    /* getQueryInfoに置き換えのため不使用
    public function imageFile($quiz_query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'getImage',
            'params' => array('quiz_query_id' => $quiz_query_id)
        );

        return $this->curl->send($data);
    }
    */

    public function removeImageFile($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'removeImageFile',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    public function removeSoundFile($query_id)
    {
        $data = array(
            'repository' => 'QuizQueryRepository',
            'method' => 'removeSoundFile',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }

    /**
     * 問題の正解
     *
     * @return correct 正解
     */
    public function correct($query_id)
    {
        $data = array(
            'repository' => 'QuizCorrectAnswerRepository',
            'method' => 'findCorrectAnswer',
            'params' => array('query_id' => $query_id)
        );

        return $this->Curl->send($data);
    }
}
