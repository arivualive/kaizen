<?php
require_once '../config.php';

class GetStudentMessageDetailModel extends PdoBase
{
    public function getMessageDetailTitle($data)
    {
        $sql = " SELECT a.message_id
                      , a.auther_user_level_id as auther_user_level_id
                      , CASE
                            WHEN a.auther_user_level_id = 1 THEN
                                (SELECT admin_name
                                   FROM tbl_admin
                                  WHERE admin_id = a.auther_user_id
                                )
                            WHEN a.auther_user_level_id = 2 THEN
                                (SELECT teacher_name
                                   FROM tbl_teacher
                                  WHERE teacher_id = a.auther_user_id
                                )
                            WHEN a.auther_user_level_id = 3 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = a.auther_user_id
                                )
                        END AS auther
                      , a.register_datetime
                      , (a.auther_user_id = :student_id AND a.auther_user_level_id = 3) as auther_flag
                      , a.title as title
                      , a.enable as enable
                      , a.type as type
                      , MAX(b.register_datetime) as last_sending_date
                      , MAX(b.register_datetime) < d.check_date AND d.check_date IS NOT NULL as check_flag
                      , CASE
                            WHEN b.send_user_level_id = 1 THEN
                                (SELECT admin_name
                                   FROM tbl_admin
                                  WHERE admin_id = b.send_user_id
                                )
                            WHEN b.send_user_level_id = 2 THEN
                                (SELECT teacher_name
                                   FROM tbl_teacher
                                  WHERE teacher_id = b.send_user_id
                                )
                            WHEN b.send_user_level_id = 3 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = b.send_user_id
                                )
                        END AS last_sender
                      , CASE
                            WHEN a.type = 0 THEN
                                (SELECT COUNT(DISTINCT c.message_detail_id) as total_message
                                )
                            WHEN a.type = 1 THEN
                                (SELECT COUNT(DISTINCT message_detail_id)
                                   FROM tbl_message_detail
                                  WHERE message_id = a.message_id
                                )
                            WHEN a.type = 2 THEN
                                (SELECT COUNT(DISTINCT c.message_detail_id) as total_message
                                )
                        END as count
                   FROM tbl_message as a
              LEFT JOIN tbl_message_detail as b
                     ON a.message_id = b.message_id
              LEFT JOIN tbl_message_target as c
                     ON b.message_detail_id = c.message_detail_id
              LEFT JOIN tbl_message_check as d
                     ON a.message_id = d.message_id
                    AND d.user_level_id = 3
                    AND d.user_id = :student_id
                  WHERE (
                            -- ユーザがメッセージ作成者なら表示
                            (a.auther_user_level_id = 3 AND a.auther_user_id = :student_id)
                        OR
                            -- ユーザがメッセージ表示対象なら一覧に表示(本人が対象 or 所属するコースが対象)
                            (
                                -- ユーザIDによる抽出
                                (c.grade_id " . $data['grade_id'] . " OR c.grade_id = 0 ) AND
                                (c.course_id " . $data['course_id'] . " OR c.course_id = 0 ) AND
                                (c.classroom_id " . $data['classroom_id'] . " OR c.classroom_id = 0 )AND

                                (c.receive_user_level_id = 3 OR c.receive_user_level_id = 0 ) AND
                                (c.receive_user_id = :student_id OR c.receive_user_id = 0 )
                            )
                        )
                    AND a.enable = 1
                    AND a.message_id = :message_id
               GROUP BY a.message_id
        ";

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':message_id' => $data['message_id']
        ));
    }

    public function getMessageDetailOffset($data)
    {
        $sql = " SELECT b.message_detail_id
                      , b.register_datetime
                      , b.message
                      , b.send_user_level_id
                      , b.send_user_id = :student_id as send_flag
                      , b.enable
                      , CASE
                            WHEN send_user_level_id = 1 THEN
                                (SELECT admin_name
                                   FROM tbl_admin
                                  WHERE admin_id = b.send_user_id
                                )
                            WHEN send_user_level_id = 2 THEN
                                (SELECT teacher_name
                                   FROM tbl_teacher
                                  WHERE teacher_id = b.send_user_id
                                )
                            WHEN send_user_level_id = 3 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = b.send_user_id
                                )
                        END AS sender
                   FROM tbl_message as a
              LEFT JOIN tbl_message_detail as b
                     ON a.message_id = b.message_id
              LEFT JOIN tbl_message_target as c
                     ON b.message_detail_id = c.message_detail_id
                  WHERE
                        CASE
                           WHEN a.type = 0 OR a.type = 2 THEN
                                (
                                    -- ユーザがメッセージ作成者なら表示
                                    (b.send_user_level_id = 3 AND b.send_user_id = :student_id)
                                OR
                                    -- ユーザがメッセージ表示対象なら一覧に表示(本人が対象 or 所属するコースが対象)
                                    (
                                        -- ユーザIDによる抽出
                                        (c.grade_id " . $data['grade_id'] . " OR c.grade_id = 0 ) AND
                                        (c.course_id " . $data['course_id'] . " OR c.course_id = 0 ) AND
                                        (c.classroom_id " . $data['classroom_id'] . " OR c.classroom_id = 0 )AND

                                        (c.receive_user_level_id = 3 OR c.receive_user_level_id = 0 ) AND
                                        (c.receive_user_id = :student_id OR c.receive_user_id = 0 )
                                    )
                                )
                                AND a.message_id = :message_id
                           WHEN a.type = 1 THEN
                                a.message_id = :message_id
                        END
                    AND a.enable = 1
               GROUP BY b.message_detail_id
               ORDER BY b.register_datetime ASC
        ";

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':message_id' => $data['message_id']
        ));
    }

    public function getMessageDetailReceiver($data)
    {
        $sql = " SELECT (SELECT grade_name
                           FROM tbl_grade
                          WHERE grade_id = a.grade_id
                        ) as grade
                      , (SELECT course_name
                           FROM tbl_course
                          WHERE course_id = a.course_id
                        ) as course
                      , (SELECT classroom_name
                           FROM tbl_classroom
                          WHERE classroom_id = a.classroom_id
                        ) as classroom
                      , CASE
                            WHEN receive_user_level_id = 1 THEN
                                (SELECT 'Administrator'
                                )
                            WHEN receive_user_level_id = 2 THEN
                                (SELECT teacher_name
                                   FROM tbl_teacher
                                  WHERE teacher_id = a.receive_user_id
                                )
                            WHEN receive_user_level_id = 3 AND :type = 0 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = a.receive_user_id
                                )
                            WHEN receive_user_level_id = 3 AND :type = 1 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = a.receive_user_id
                                )
                            WHEN receive_user_level_id = 3 AND :type = 2 THEN
                                (SELECT student_name
                                   FROM tbl_student
                                  WHERE student_id = a.receive_user_id
                                    AND student_id = :student_id
                                )
                        END AS receiver
                   FROM tbl_message_target as a
                  WHERE a.message_detail_id = :message_detail_id
        ";

        return $this->fetchAll($sql, array(
            ':student_id' => $data['student_id']
          , ':message_detail_id' => $data['message_detail_id']
          , ':type' => $data['type']
        ));
    }

    public function setMessageDetailDelete($data)
    {
        $sql = " UPDATE tbl_message_detail
                    SET enable = 0
                  WHERE message_detail_id = :message_detail_id
        ";

        return $this->execute($sql, array(
            ':message_detail_id' => $data['message_detail_id']
        ));
    }

    public function getMessageDetailDateCheck($data)
    {
        $sql = " DELETE
                   FROM tbl_message_check
                  WHERE message_id = :message_id
                    AND user_level_id = :user_level_id
                    AND user_id = :user_id
        ";

        $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':user_level_id' => $data['user_level_id']
          , ':user_id' => $data['user_id']
        ));

        $sql = " INSERT
                   INTO tbl_message_check
                        (   message_id
                          , user_level_id
                          , user_id
                          , check_date
                        ) VALUES (
                            :message_id
                          , :user_level_id
                          , :user_id
                          , NOW()
                        )
        ";

        return $this->execute($sql, array(
            ':message_id' => $data['message_id']
          , ':user_level_id' => $data['user_level_id']
          , ':user_id' => $data['user_id']
        ));
    }
}
