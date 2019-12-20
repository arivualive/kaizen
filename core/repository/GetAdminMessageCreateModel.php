<?php
require_once '../config.php';

class GetAdminMessageCreateModel extends PdoBase
{
    public function getMessageCreateStudentList($data)
    {
        $sql = "SELECT a.student_id
                     , a.student_name
                     , a.bit_subject
                  FROM tbl_student as a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.student_id
              ORDER BY a.display_order DESC
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getMessageCreateTeacherList($data)
    {
        $sql = "SELECT a.teacher_id
                     , a.teacher_name
                  FROM tbl_teacher as a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.teacher_id
              ORDER BY a.display_order
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    //public function getMessageCreateAdminList()
    //{
    //    $sql = "SELECT '0' as admin_id 
    //                 , '早稲田アカデミー管理者' as admin_name
    //    ";
    //
    //    return $this->fetchAll($sql, array());
    //}

    public function getMessageCreateGradeList($data)
    {
        $sql = "SELECT a.grade_id
                     , a.grade_name
                  FROM tbl_grade as a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.grade_id
              ORDER BY a.display_order DESC
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getMessageCreateCourseList($data)
    {
        $sql = "SELECT a.course_id
                     , a.course_name
                  FROM tbl_course as a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.course_id
              ORDER BY a.display_order DESC
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getMessageCreateClassroomList($data)
    {
        $sql = "SELECT a.classroom_id
                     , a.classroom_name
                  FROM tbl_classroom as a
                 WHERE a.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.classroom_id
              ORDER BY a.display_order DESC
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function setMessageCreateInsertMessage($data)
    {
        $sql = " INSERT INTO
                    tbl_message
                    (   school_id
                      , title
                      , auther_user_level_id
                      , auther_user_id
                      , register_datetime
                      , type
                      , message_level
                      , limit_date
                      , enable
                    ) VALUES (
                        :school_id
                      , :title
                      , :auther_user_level_id
                      , :auther_user_id
                      , NOW()
                      , :type
                      , :message_level
                      , :limit_date
                      , :enable
                    )
        ";

        $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':title' => $data['title']
          , ':auther_user_level_id' => $data['auther_user_level_id']
          , ':auther_user_id' => $data['auther_user_id']
          , ':type' => $data['type']
          , ':message_level' => $data['message_level']
          , ':limit_date' => $data['limit_date']
          , ':enable' => $data['enable']
        ));
        
        $sql = "SELECT MAX(message_id) AS message_id
                  FROM tbl_message
        ";

        return $this->fetch($sql, array());
    }

    public function setMessageCreateInsertMessageDetail($data)
    {
        $sql = " INSERT INTO
                    tbl_message_detail
                    (   message_id
                      , message
                      , send_user_level_id
                      , send_user_id
                      , register_datetime
                      , enable
                    ) VALUES (
                        :message_id
                      , :message
                      , :send_user_level_id
                      , :send_user_id
                      , NOW()
                      , :enable
                    )
        ";

        $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':message' => $data['message']
          , ':send_user_level_id' => $data['send_user_level_id']
          , ':send_user_id' => $data['send_user_id']
          , ':enable' => $data['enable']
        ));
        
        $sql = "SELECT MAX(message_detail_id) AS message_detail_id
                  FROM tbl_message_detail
        ";

        return $this->fetch($sql, array());
    }

    public function setMessageCreateInsertMessageTarget($data)
    {
        $sql = " INSERT INTO
                    tbl_message_target
                    (   message_detail_id
                      , grade_id
                      , course_id
                      , classroom_id
                      , receive_user_level_id
                      , receive_user_id
                    ) VALUES (
                        :message_detail_id
                      , :grade_id
                      , :course_id
                      , :classroom_id
                      , :receive_user_level_id
                      , :receive_user_id
                    )
        ";

        $this->execute($sql, array(
            ':message_detail_id' => $data['message_detail_id']
          , ':grade_id' => $data['grade_id']
          , ':course_id' => $data['course_id']
          , ':classroom_id' => $data['classroom_id']
          , ':receive_user_level_id' => $data['receive_user_level_id']
          , ':receive_user_id' => $data['receive_user_id']
        ));
        
        $sql = "SELECT MAX(message_target_id) AS message_target_id
                  FROM tbl_message_target
        ";

        return $this->fetch($sql, array());
    }
}
