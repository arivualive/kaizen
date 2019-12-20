<?php

class QueryFollow
{
    protected $Quiz;
    protected $curl;
    protected $query_id;

    public function __construct($query_id, $Quiz, $curl)
    {
        $this->query_id = $query_id;
        $this->Quiz = $Quiz;
        $this->curl = $curl;
    }

    public function getContents()
    {
        $quiz = $this->Quiz->getQuiz();

        $data = array(
            'repository' => 'ContentsRepository',
            'method' => 'findSubjectSectionEnable',
            'params' => array('subject_section_id' => $quiz['subject_section_id'])
        );

        return $this->curl->send($data);
    }

    public function contentsCategoryId()
    {
        $quiz = $this->Quiz->getQuiz();

        return $quiz['contents_category_id'];
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

    public function followContents()
    {
        $subject_section = $this->getContents();
        //debug($subject_section);


        foreach ($subject_section as $key => $item) {
            $subject_section[$key]['checked'] = ''; 

            if ($this->findFollowContents($item['contents_id']) != '') {
                $subject_section[$key]['checked'] = 'checked'; 
            }
        }

        return $subject_section;
    }

    public function checkedFollowContents()
    {
        $data = array(
            'repository' => 'FollowContentsRepository',
            'method' => 'findFollowContentsQueryId',
            'params' => array(
                'quiz_query_id' => $this->query_id,
                'contents_category_id' => $this->contentsCategoryId()
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
                'quiz_query_id' => $this->query_id,
                'contents_category_id' => $this->contentsCategoryId()
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
                'quiz_query_id' => $this->query_id,
                'contents_id' => $follow_data['contents_id'],
                'contents_category_id' => $this->contentsCategoryId()
            )
        );

        return $this->curl->send($data);
    }

}
