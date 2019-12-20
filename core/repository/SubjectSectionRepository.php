<?php
require_once '../config.php';

class SubjectSectionRepository extends PdoBase
{

    public function findSubjectSectionAll()
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                     , a.subject_genre_id
                     , b.subject_genre_name
                     , a.subject_group_id
                     , c.subject_group_name
                     , a.proportion
                     , a.editable
                     , a.enable
                     , a.display_order
                  FROM tbl_subject_section a
             LEFT JOIN tbl_subject_genre b ON a.subject_genre_id = b.subject_genre_id
             LEFT JOIN tbl_subject_group c ON a.subject_group_id = c.subject_group_id
              ORDER BY a.display_order ASC';

        return $this->fetchAll($sql);
    }

    public function findSubjectSectionIdSelect($data)
    {
        $sql = 'SELECT subject_section_id
                  FROM tbl_subject_section
                 WHERE subject_section_id ' . $data['grade_id'] . '
                   AND subject_section_id ' . $data['classroom_id'] . '
                   AND subject_section_id ' . $data['course_id'] . '
              ORDER BY display_order ASC';

        return $this->fetchAll($sql);
    }

//  public function findSubjectSectionIdSelect($data)
//  {
//      $sql = 'SELECT subject_section_id
//                FROM tbl_subject_section
//               WHERE subject_section_id IN (:grade_subject_section_id)
//                 AND subject_section_id IN (:classroom_subject_section_id)
//                 AND subject_section_id IN (:course_subject_section_id)
//            ORDER BY display_order ASC';

//      $stmt = $this->db->prepare($sql);
//      $stmt->bindValue(':grade_subject_section_id', (int) $data['grade_subject_section_id'], PDO::PARAM_INT);
//      $stmt->bindValue(':classroom_subject_section_id', (int) $data['classroom_subject_section_id'], PDO::PARAM_INT);
//      $stmt->bindValue(':course_subject_section_id', (int) $data['course_subject_section_id'], PDO::PARAM_INT);
//      $stmt->execute();

//      return $stmt->fetchAll();
//  }

    public function findSubjectSectionEnable()
    {
        $sql = '
            SELECT
                subject_section_id
              , subject_section_name
              , proportion
              , editable
              , enable
              , display_order
            FROM
                tbl_subject_section
            WHERE
                enable = 1
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findSubjectSectionId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_section
            WHERE
                subject_section_id = :subject_section_id
        ';

        return $this->fetch($sql, array(
            ':subject_section_id' => $data['subject_section_id']
        ));
    }

    public function updateSubjectSectionId($data)
    {
        $sql = '
            UPDATE
                tbl_subject_section
            SET
                subject_group_id = :subject_group_id,
                subject_genre_id = :subject_genre_id,
                subject_section_name = :subject_section_name,
                proportion = :proportion,
                editable = :editable,
                enable = :enable,
                display_order = :display_order
            WHERE
                subject_section_id = :subject_section_id
        ';

        return $this->execute($sql,
            array(
                ':subject_group_id' => $data['subject_group_id'],
                ':subject_genre_id' => $data['subject_genre_id'],
                ':subject_section_name' => $data['subject_section_name'],
                ':proportion' => $data['proportion'],
                ':editable' => $data['editable'],
                ':enable' => $data['enable'],
                ':display_order' => $data['display_order'],
                ':subject_section_id' => $data['subject_section_id']
            )
        );
    }

    public function updateSubjectSection($data)
    {
        $sql = '
            UPDATE
                tbl_subject_section
            SET
                display_order = :display_order
            WHERE
                subject_section_id = :subject_section_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':subject_section_id' => $data['subject_section_id']
        ));
    }

    public function insertSubjectSection($data)
    {
        $sql = '
            INSERT INTO
                tbl_subject_section (
                    subject_section_name
                  , proportion
                  , editable
                  , enable
                  , display_order
                ) VALUES (
                    :subject_section_name
                  , :proportion
                  , :editable
                  , :enable
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':subject_section_name' => $data['subject_section_name']
          , ':proportion' => $data['proportion']
          , ':editable' => $data['editable']
          , ':enable' => 1
          , ':display_order' => $data['display_order']
        ));
    }

    // 全ての科目を取得
    public function findSubjectGenreAll($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_genre
            WHERE
                enable = :enable
        ';

        return $this->fetchAll($sql, array(
            ':enable' => $data[ 'enable' ]
        ));
    }

    // subject_genre_id でのsubject_section 抽出
    public function findSubjectSectionSearch( $data )
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                     , a.subject_genre_id
                     , b.subject_genre_name
                     , a.subject_group_id
                     , c.subject_group_name
                     , a.proportion
                     , a.editable
                     , a.enable
                     , a.display_order
                  FROM tbl_subject_section a
             LEFT JOIN tbl_subject_genre b ON a.subject_genre_id = b.subject_genre_id
             LEFT JOIN tbl_subject_group c ON a.subject_group_id = c.subject_group_id
             WHERE b.subject_genre_id = :subject_genre_id
             AND a.enable = 1
              ORDER BY a.subject_section_id ASC';

        return $this->fetchAll($sql, array(
          ':subject_genre_id' => $data[ 'subject_genre_id' ]
        ));
    }

    // course_id を取得
    public function findCourseId( $data )
    {
      $sql = 'SELECT a.course_id
                , a.subject_section_id
                , b.course_name
              FROM tbl_course_subject_section as a
              INNER JOIN tbl_course as b
              ON a.course_id = b.course_id
              WHERE subject_section_id = :subject_section_id
              AND b.enable = 1';

              return $this->fetchAll($sql, array(
                ':subject_section_id' => $data[ 'subject_section_id' ]
              ));

    }

    // classroom_id を取得
    public function findClassroomId( $data )
    {
      $sql = 'SELECT a.classroom_id
                , a.subject_section_id
                , b.classroom_name
              FROM tbl_classroom_subject_section as a
              INNER JOIN tbl_classroom as b
              ON a.classroom_id = b.classroom_id
              WHERE subject_section_id = :subject_section_id
              AND b.enable = 1';

              return $this->fetchAll($sql, array(
                ':subject_section_id' => $data[ 'subject_section_id' ]
              ));

    }

    // grade_id を取得
    public function findGradeId( $data )
    {
      $sql = 'SELECT a.grade_id
                , a.subject_section_id
                , b.grade_name
              FROM tbl_grade_subject_section as a
              INNER JOIN tbl_grade as b
              ON a.grade_id = b.grade_id
              WHERE subject_section_id = :subject_section_id
              AND b.enable = 1';

              return $this->fetchAll($sql, array(
                ':subject_section_id' => $data[ 'subject_section_id' ]
              ));
    }


    // subject_genre_id での course 対象生徒を抽出
    public function findCourseStudentSearch( $data )
    {

        $sql = 'SELECT  a.course_id
                      , b.course_name
                      , c.student_id
                      , d.subject_section_id
                      , d.subject_section_name
                      , e.student_id
                      , e.student_name
               FROM tbl_course_subject_section as a
               LEFT JOIN tbl_course as b ON a.course_id = b.course_id
               LEFT JOIN tbl_student_course as c ON b.course_id = c.course_id
               LEFT JOIN tbl_subject_section as d
               ON d.subject_section_id = a.subject_section_id
               LEFT JOIN tbl_student as e ON e.student_id = c.student_id
               WHERE a.subject_section_id = :subject_section_id AND b.enable = 1 AND e.enable = 1';

        return $this->fetchAll($sql, array(
          ':subject_section_id' => $data[ 'subject_section_id' ]
        ));

    }

    // subject_genre_id での course 対象生徒を抽出
    public function findClassRoomStudentSearch( $data )
    {
        $sql = 'SELECT  a.classroom_id
                      , b.classroom_name
                      , c.student_id
                      , d.subject_section_id
                      , d.subject_section_name
                      , e.student_id
                      , e.student_name
               FROM tbl_classroom_subject_section as a
               LEFT JOIN tbl_classroom as b ON a.classroom_id = b.classroom_id
               LEFT JOIN tbl_student_classroom as c ON b.classroom_id = c.classroom_id
               LEFT JOIN tbl_subject_section as d
               ON d.subject_section_id = a.subject_section_id
               LEFT JOIN tbl_student as e ON e.student_id = c.student_id
               WHERE a.subject_section_id = :subject_section_id AND b.enable = 1 AND e.enable = 1';

        return $this->fetchAll($sql, array(
          ':subject_section_id' => $data[ 'subject_section_id' ]
        ));

    }

    // subject_genre_id での grade 対象生徒を抽出
    public function findGradeStudentSearch( $data )
    {
        $sql = 'SELECT  a.grade_id
                      , b.grade_name
                      , c.student_id
                      , d.subject_section_id
                      , d.subject_section_name
                      , e.student_id
                      , e.student_name
               FROM tbl_grade_subject_section as a
               LEFT JOIN tbl_grade as b ON a.grade_id = b.grade_id
               LEFT JOIN tbl_student_grade as c ON b.grade_id = c.grade_id
               LEFT JOIN tbl_subject_section as d
               ON d.subject_section_id = a.subject_section_id
               LEFT JOIN tbl_student as e ON e.student_id = c.student_id
               WHERE a.subject_section_id = :subject_section_id AND b.enable = 1 AND e.enable = 1';

        return $this->fetchAll($sql, array(
          ':subject_section_id' => $data[ 'subject_section_id' ]
        ));

    }

    // subject_section 対象 student を取得
    public function findSubjectSectionStudentSearch ( $data )
    {
      $sql = 'SELECT student_id
                    ,student_name
              FROM tbl_student
              WHERE student_id in ('.$data[ 'union_sql' ].')
              ORDER BY tbl_student.student_id ASC';

      return $this->fetchAll( $sql );

    }

    // content を取得 ( 旧アクセス権 )
    public function findSubjectSectionContentsSearch ( $data )
    {
      $sql = 'SELECT contents_id
                    ,contents_name
              FROM   tbl_contents
              WHERE  subject_section_id = :subject_section_id
              AND    enable = 1
              ORDER BY contents_id';

      return $this->fetchAll( $sql, array(
        ':subject_section_id' => $data[ 'subject_section_id' ]
      ));

    }

    // content を取得 (新アクセス権 )
    public function findSubjectSectionContentsSearch_Type1 ( $data )
    {
      $sql = 'SELECT contents_id
                    ,contents_name
                    ,bit_classroom
              FROM   tbl_contents
              WHERE  bit_classroom = :bit_classroom
              AND    enable = 1
              AND    school_id = :school_id
              ORDER BY contents_id';

      return $this->fetchAll( $sql, array(
         ':bit_classroom' => $data[ 'subject_section_id' ]
        ,':school_id' => $data[ 'school_id' ]
      ));

    }

    // test を抽出 ( 旧アクセス権 )
    public function findSubjectSectionTestSearch ( $data )
    {
      $sql = 'SELECT quiz_id
                    ,title
                    ,qualifying_score
              FROM   tbl_quiz
              WHERE  subject_section_id = :subject_section_id
              AND    enable = 1
              ORDER BY quiz_id';

      return $this->fetchAll( $sql, array(
        ':subject_section_id' => $data[ 'subject_section_id' ]
      ));

    }

    // test を抽出 ( 新アクセス権 )
    public function findSubjectSectionTestSearch_Type1 ( $data )
    {
      $sql = 'SELECT quiz_id
                    ,title
                    ,qualifying_score
                    ,bit_classroom
              FROM   tbl_quiz
              WHERE  bit_classroom = :bit_classroom
              AND    enable = 1
              AND    school_id = :school_id
              ORDER BY quiz_id';

      return $this->fetchAll( $sql, array(
        ':bit_classroom' => $data[ 'subject_section_id' ]
       ,':school_id' => $data[ 'school_id' ]
      ));

    }

    // アンケート or レポートを抽出 ( 旧DB )
    public function findSubjectSectionQuestionnaireSearch ( $data )
    {
      $sql = 'SELECT a.questionnaire_school_target_range_id
                    ,a.questionnaire_id
                    ,a.contents_category_id
                    ,a.subject_section_id
                    ,b.title
              FROM   tbl_questionnaire_target_range_school as a
              LEFT JOIN tbl_questionnaire as b
              ON     a.questionnaire_id = b.questionnaire_id
              WHERE  a.subject_section_id = :subject_section_id
              AND    b.type = :type
              AND    b.enable = 1
              ORDER BY questionnaire_id';

      return $this->fetchAll( $sql, array(
         ':subject_section_id' => $data[ 'subject_section_id' ]
        ,':type'               => $data[ 'type' ]
      ));

    }

    // アンケート or レポートを抽出 新アクセス権でのデータ
    public function findSubjectSectionQuestionnaireSearch_Type1 ( $data )
    {
      //return $data;

      $sql = 'SELECT questionnaire_id
                , title
                , enable
                , type
                , bit_classroom
              FROM tbl_questionnaire
              WHERE bit_classroom = :bit_classroom
              AND enable = 1
              AND school_id = :school_id
              AND type = :type
              ORDER BY questionnaire_id';

      return $this->fetchAll( $sql, array(
         ':bit_classroom' => $data[ 'subject_section_id' ]
        ,':type'          => $data[ 'type' ]
        ,':school_id' => $data[ 'school_id' ]
      ));

    }

    // contents 視聴結果確認
    public function contentsStudentResultSearch ( $data )
    {

      //return $data[ 'student_id' ];
      $sql = 'SELECT MAX( history_id ) as history_id
                , MAX( play_start_datetime ) as play_start_datetime
                , school_contents_id
                , proportion_flg
                , MAX( CASE WHEN proportion is null THEN "-"
                    WHEN proportion is not null THEN proportion END ) AS proportion
                , CASE proportion_flg
                    WHEN 0 THEN "×" WHEN 1 THEN "○" END AS contents_result
              FROM
                log_contents_history_student
              WHERE
                school_contents_id = :contents_id
              AND
                student_id = :student_id
              AND
                proportion is not null
              GROUP BY
                student_id';

      return $this->fetchAll( $sql, array(
         ':contents_id' => $data[ 'contents_id' ]
        ,':student_id'  => $data[ 'sid' ]
      ));

    }

    // quiz 受講確認 ( 旧アクセス権 )
    public function quizStudentResultSearch ( $data )
    {
      $sql = 'SELECT
                     MAX( b.quiz_answer_id ) as quiz_answer_id
                    ,MAX( b.total_score ) as total_score
                    ,MAX( b.register_datetime ) as register_datetime
                    ,(CASE
                      WHEN b.quiz_answer_id is not null THEN "○"
                      WHEN b.quiz_answer_id is null THEN "×" END) as quiz_result
                    ,a.quiz_id
                    ,a.title
                    ,a.subject_section_id
              FROM  tbl_quiz as a
              LEFT JOIN tbl_quiz_answer as b
              ON a.quiz_id = b.quiz_id
              WHERE a.quiz_id = :quiz_id
              AND b.student_id = :student_id
              AND a.enable = 1
              ORDER BY a.quiz_id';


      return $this->fetchAll( $sql, array(
         ':quiz_id' => $data[ 'quiz_id' ]
        ,':student_id' => $data[ 'sid' ]
      ));

    }

    // quiz 受講確認 ( 新アクセス権 )
    public function quizStudentResultSearch_Type1 ( $data )
    {
      $sql = 'SELECT
                     b.answer_id as quiz_answer_id
                    ,MAX( b.total_score ) as total_score
                    ,MAX(b.register_datetime) as register_datetime
                    ,(CASE
                      WHEN b.answer_id is not null THEN "○"
                      WHEN b.answer_id is null THEN "×" END) as quiz_result
                    ,a.quiz_id
                    ,a.title
                    ,a.subject_section_id
              FROM  tbl_quiz as a
              LEFT JOIN tbl_quiz_answer as b
              ON a.quiz_id = b.quiz_id
              WHERE a.quiz_id = :quiz_id
              AND b.student_id = :student_id
              AND a.enable = 1
              ORDER BY a.quiz_id';


      return $this->fetchAll( $sql, array(
         ':quiz_id' => $data[ 'quiz_id' ]
        ,':student_id' => $data[ 'sid' ]
      ));

    }

    // questionnaire 受講確認 ( 旧DBタイプ )
    public function questionnaireResultSearch ( $data )
    {
      $sql = 'SELECT a.questionnaire_id
                    ,a.subject_section_id
                    ,b.title
                    ,MAX( c.answer_datetime ) as answer_datetime
              FROM  tbl_questionnaire_target_range_school as a
              LEFT JOIN tbl_questionnaire as b
              ON  a.questionnaire_id = b.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer as c
              ON  b.questionnaire_id = c.questionnaire_id
              WHERE a.subject_section_id = :subject_section_id
              AND a.questionnaire_id = :questionnaire_id
              AND c.student_id = :student_id
              AND b.type = :type
              ORDER BY a.questionnaire_id';

      return $this->fetchAll( $sql, array(
         ':subject_section_id' => $data[ 'subject_section_id' ]
        ,':questionnaire_id' => $data[ 'questionnaire_id' ]
        ,':student_id' => $data[ 'sid' ]
        ,':type' => $data[ 'type' ]
      ));

    }

    // questionnaire 受講確認 ( 新DBタイプ )
    public function questionnaireResultSearch_Type1 ( $data )
    {
      $sql = 'SELECT a.questionnaire_id
                    ,a.bit_classroom
                    ,a.title
					          ,b.answer_id
                    ,MAX( b.answer_datetime ) as answer_datetime
              FROM  tbl_questionnaire as a
              LEFT JOIN tbl_questionnaire_answer as b
              ON  a.questionnaire_id = b.questionnaire_id
              WHERE a.questionnaire_id = :questionnaire_id
              AND b.student_id = :student_id
              AND a.type = :type
              ORDER BY a.questionnaire_id';
      /*$sql = 'SELECT a.questionnaire_id
                    ,a.subject_section_id
                    ,b.title
                    ,MAX( c.answer_datetime ) as answer_datetime
              FROM  tbl_questionnaire_target_range_school as a
              LEFT JOIN tbl_questionnaire as b
              ON  a.questionnaire_id = b.questionnaire_id
              LEFT JOIN tbl_questionnaire_answer as c
              ON  b.questionnaire_id = c.questionnaire_id
              WHERE a.questionnaire_id = :questionnaire_id
              AND c.student_id = :student_id
              AND b.type = :type
              ORDER BY a.questionnaire_id';*/

      return $this->fetchAll( $sql, array(
         ':questionnaire_id' => $data[ 'questionnaire_id' ]
        ,':student_id' => $data[ 'sid' ]
        ,':type' => $data[ 'type' ]
      ));

    }

    // ここからアクセス権新手法
    public function getStudentBitSubject ( $data )
    {
      $sql = 'SELECT student_id as sid
                    ,school_id
                    ,id
                    ,student_name as sn
                    ,bit_subject
              FROM tbl_student
              WHERE enable = 1
              AND school_id = :school_id
              ';

      return $this->fetchAll( $sql, array(
        ':school_id' => $data[ 'school_id' ]
      ));
    }

    public function getContentsName ( $data )
    {
      $sql = 'SELECT contents_name
                    ,bit_classroom
                    ,contents_extension_id
              FROM tbl_contents
              WHERE contents_id = :contents_id
              AND enable = 1';

      return $this->fetchAll( $sql, array(
        ':contents_id' => $data[ 'contents_id' ]
      ));

    }














}
