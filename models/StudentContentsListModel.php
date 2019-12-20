<?php

class StudentContentsListModel
{
    public $student_id;
    public $curl;

    public function __construct($student_id, $school_id, $bit_classroom, $curl)
    {
        $this->student_id = $student_id;
        $this->school_id = $school_id;
        $this->bit_classroom = $bit_classroom;
        $this->curl = $curl;
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getContents()
    {
        $curl = array(
            'repository' => 'StudentContentsListRepository'
          , 'method' => 'getContents'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getContentsAttachment($data)
    {
        $curl = array(
            'repository' => 'StudentContentsListRepository'
          , 'method' => 'getContentsAttachment'
          , 'params' => array(
              'contents_id' => $data['primary_key']
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからアンケートを取得
    public function getQuestionnaire($type)
    {
        $curl = array(
            'repository' => 'StudentContentsListRepository'
          , 'method' => 'getQuestionnaire'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'type' => $type
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからテストを取得
    public function getQuiz()
    {
        $curl = array(
            'repository' => 'StudentContentsListRepository'
          , 'method' => 'getQuiz'
          , 'params' => array(
                'bit_classroom' => $this->bit_classroom
              , 'school_id' => $this->school_id
              , 'student_id' => $this->student_id
            )
        );

        return $this->curl->send($curl);
    }

    // 参加年度・所属ユニット・コースからコンテンツを取得(コンテンツ内容の取得)
    public function getQuizAnswer($data)
    {
        $curl = array(
            'repository' => 'StudentContentsListRepository'
          , 'method' => 'getQuizAnswer'
          , 'params' => array(
              'student_id' => $this->student_id
            , 'quiz_id' => $data['primary_key']
            )
        );

        return $this->curl->send($curl);
    }

    // フォルダグループ取得
    public function getFunctionGroup ()
    {
      $curl = array(
         'repository' => 'StudentContentsListRepository'
        ,'method' => 'getFunctionGroup'
        ,'params' => array(
           'school_id' => $this->school_id
          ,'bit_classroom' => $this->bit_classroom
        )
      );

      return $this->curl->send( $curl );
    }

    // 参加年度・所属ユニット・コースからメッセージを取得
    //public function getMessage()
    //{
    //    $grade_id_string = $this->getGradeId();
    //    $classroom_id_string = $this->getClassroomId();
    //    $course_id_string = $this->getCourseId();

    //    $curl = array(
    //        'repository' => 'StudentContentsListRepository'
    //      , 'method' => 'getMessage'
    //      , 'params' => array(
    //            'grade_id' => $grade_id_string
    //          , 'classroom_id' => $classroom_id_string
    //          , 'course_id' => $course_id_string
    //          , 'school_id' => $this->school_id
    //          , 'student_id' => $this->student_id
    //        )
    //    );

    //    return $this->curl->send($curl);
    //}
}
