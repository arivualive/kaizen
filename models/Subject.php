<?php

class Subject
{
    public function __construct($curl)
    {
        $this->curl = $curl;
    }

    public function subject_section($subject_section_id)
    {
        $data = array(
            'repository' => 'SubjectSectionRepository',
            'method' => 'findSubjectSectionId',
            'params' => array('subject_section_id' => $subject_section_id)
        );

        return $this->curl->send($data);
        
    }
    
    public function subject_genre_name($subject_section_id)
    {
        $data = array(
            'repository' => 'SubjectGenreRepository',
            'method' => 'findSubjectGenreName',
            'params' => array('subject_section_id' => $subject_section_id)
        );

        return $this->curl->send($data);
    }

}
