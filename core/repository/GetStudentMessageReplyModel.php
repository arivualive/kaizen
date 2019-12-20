<?php
require_once '../config.php';

class GetStudentMessageReplyModel extends PdoBase
{
    //public function getMessageReplyStudentList($data)
    //{
    //    $sql = " SELECT c.student_id
    //                  , c.student_name
    //               FROM tbl_message_detail as a
    //          LEFT JOIN tbl_message_target as b
    //              USING (message_detail_id)
    //          LEFT JOIN tbl_student as c
    //                 ON a.send_user_id = c.student_id
    //                 OR b.receive_user_id = c.student_id
    //              WHERE c.school_id = :school_id
    //                AND a.message_id = :message_id
    //                AND c.enable = 1
    //                AND (
    //                    a.send_user_level_id = 3
    //                 OR b.receive_user_level_id = 3
    //                    )
    //           GROUP BY c.student_id
    //           ORDER BY c.display_order DESC
    //    ";

    //    return $this->fetchAll($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':message_id' => $data['message_id']
    //    ));
    //}

    public function getMessageReplyTeacherList($data)
    {
        $sql = " SELECT c.teacher_id
                      , c.teacher_name
                   FROM tbl_message_detail as a
              LEFT JOIN tbl_message_target as b
                  USING (message_detail_id)
              LEFT JOIN tbl_teacher as c
                     ON a.send_user_id = c.teacher_id
                     OR b.receive_user_id = c.teacher_id
                  WHERE c.school_id = :school_id
                    AND a.message_id = :message_id
                    AND c.enable = 1
                    AND (
                        a.send_user_level_id = 2
                     OR b.receive_user_level_id = 2
                        )
               GROUP BY c.teacher_id
               ORDER BY c.display_order DESC
        ";

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':message_id' => $data['message_id']
        ));
    }

    public function getMessageReplyAdminList($data)
    {
        $sql = " SELECT a.send_user_level_id
                      , b.receive_user_level_id
                   FROM tbl_message_detail as a
              LEFT JOIN tbl_message_target as b
                  USING (message_detail_id)
                  WHERE (
                        a.send_user_level_id = 1
                     OR b.receive_user_level_id = 1
                        )
                    AND a.message_id = :message_id
               GROUP BY a.message_id
        ";

        $admin_check = $this->fetchAll($sql, array(
            ':message_id' => $data['message_id']
        ));

        if(count($admin_check) == 0) {
            return $admin_check;
        } else {
            $sql = " SELECT '0' as admin_id
                          , 'Administrator' as admin_name
            ";

            return $this->fetchAll($sql, array());
        }
    }

    //public function getMessageReplyGradeList($data)
    //{
    //    $sql = " SELECT c.grade_id
    //                  , c.grade_name
    //               FROM tbl_message_detail as a
    //          LEFT JOIN tbl_message_target as b
    //              USING (message_detail_id)
    //          LEFT JOIN tbl_grade as c
    //                 ON b.grade_id = c.grade_id
    //              WHERE c.school_id = :school_id
    //                AND a.message_id = :message_id
    //                AND c.enable = 1
    //           GROUP BY c.grade_id
    //           ORDER BY c.display_order DESC
    //    ";

    //    return $this->fetchAll($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':message_id' => $data['message_id']
    //    ));
    //}

    //public function getMessageReplyCourseList($data)
    //{
    //    $sql = " SELECT c.course_id
    //                  , c.course_name
    //               FROM tbl_message_detail as a
    //          LEFT JOIN tbl_message_target as b
    //              USING (message_detail_id)
    //          LEFT JOIN tbl_course as c
    //                 ON b.course_id = c.course_id
    //              WHERE c.school_id = :school_id
    //                AND a.message_id = :message_id
    //                AND c.enable = 1
    //           GROUP BY c.course_id
    //           ORDER BY c.display_order DESC
    //    ";

    //    return $this->fetchAll($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':message_id' => $data['message_id']
    //    ));
    //}

    //public function getMessageReplyClassroomList($data)
    //{
    //    $sql = " SELECT c.classroom_id
    //                  , c.classroom_name
    //               FROM tbl_message_detail as a
    //          LEFT JOIN tbl_message_target as b
    //              USING (message_detail_id)
    //          LEFT JOIN tbl_classroom as c
    //                 ON b.classroom_id = c.classroom_id
    //              WHERE c.school_id = :school_id
    //                AND a.message_id = :message_id
    //                AND c.enable = 1
    //           GROUP BY c.classroom_id
    //           ORDER BY c.display_order DESC
    //    ";

    //    return $this->fetchAll($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':message_id' => $data['message_id']
    //    ));
    //}

    //public function setMessageReplyInsertMessage($data)
    //{
    //    $sql = " INSERT INTO
    //                tbl_message
    //                (   school_id
    //                  , title
    //                  , auther_user_level_id
    //                  , auther_user_id
    //                  , register_datetime
    //                  , type
    //                  , enable
    //                ) VALUES (
    //                    :school_id
    //                  , :title
    //                  , :auther_user_level_id
    //                  , :auther_user_id
    //                  , NOW()
    //                  , :type
    //                  , :enable
    //                )
    //    ";

    //    $this->execute($sql, array(
    //        ':school_id' => $data['school_id']
    //      , ':title' => $data['title']
    //      , ':auther_user_level_id' => $data['auther_user_level_id']
    //      , ':auther_user_id' => $data['auther_user_id']
    //      , ':type' => $data['type']
    //      , ':enable' => $data['enable']
    //    ));
    //
    //    $sql = "SELECT MAX(message_id) AS message_id
    //              FROM tbl_message
    //    ";

    //    return $this->fetch($sql, array());
    //}

    public function setMessageReplyInsertMessageDetail($data)
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

        $sql = " SELECT MAX(message_detail_id) AS message_detail_id
                   FROM tbl_message_detail
        ";

        return $this->fetch($sql, array());
    }

    public function setMessageReplyInsertMessageTarget($data)
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

        $sql = " SELECT MAX(message_target_id) AS message_target_id
                   FROM tbl_message_target
        ";

        return $this->fetch($sql, array());
    }

    public function getMessageReplyMessageData($data)
    {
        $sql = " SELECT a.title
                      , a.type
                   FROM tbl_message as a
                  WHERE a.message_id = :message_id
        ";

        return $this->fetchAll($sql, array(
            ':message_id' => $data['message_id']
        ));
    }
}
