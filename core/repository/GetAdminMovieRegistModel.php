<?php
require_once '../config.php';

class GetAdminMovieRegistModel extends PdoBase
{
    public function getSubject($data)
    {
        $sql = 'SELECT a.subject_genre_id
                     , a.subject_genre_name
                  FROM tbl_subject_genre as a
             LEFT JOIN tbl_subject_group as b
                 USING(subject_group_id)
                 WHERE b.school_id = :school_id
                   AND a.enable = 1
              GROUP BY a.subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function getSubjectSection($data)
    {
        $sql = 'SELECT a.subject_section_id
                     , a.subject_section_name
                  FROM tbl_subject_section as a
                 WHERE a.subject_genre_id = :subject_genre_id
              ORDER BY a.display_order DESC
        ';

        return $this->fetchAll($sql, array(
            ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

    public function setContents($data)
    {
        $sql = ' INSERT INTO
                    tbl_contents
                    (   subject_section_id
                      , contents_name
                      , comment
                      , first_day
                      , last_day
                      , file_name
                      , user_level_id
                      , register_id
                      , register_datetime
                      , contents_extension_id
                      , size
                      , enable
                      , display_order
                      , bit_classroom
                    ) VALUES (
                        :subject_section_id
                      , :contents_name
                      , :comment
                      , :first_day
                      , :last_day
                      , :file_name
                      , :user_level_id
                      , :register_id
                      , NOW()
                      , :contents_extension_id
                      , :size
                      , :enable
                      ,(SELECT IFNULL(MAX(a.contents_id) + 1, 1) as contents_id
                          FROM tbl_contents as a
                       )
                      , :bit_classroom
                    )
        ';

        $this->execute($sql, array(
            ':subject_section_id' => $data['subject_section_id']
          , ':contents_name' => $data['contents_name']
          , ':comment' => $data['comment']
          , ':first_day' => $data['first_day']
          , ':last_day' => $data['last_day']
          , ':file_name' => $data['file_name']
          , ':user_level_id' => $data['user_level_id']
          , ':register_id' => $data['register_id']
          , ':contents_extension_id' => $data['contents_extension_id']
          , ':size' => $data['size']
          , ':enable' => $data['enable']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    public function setContentsAttachment($data)
    {
        $sql = ' INSERT INTO
                    tbl_contents_attachment
                    (   contents_category_id
                      , contents_id
                      , file_name
                    ) VALUES (
                        :contents_category_id
                      , :contents_id
                      , :file_name
                    )
        ';

        return $this->execute($sql, array(
            ':contents_category_id' => $data['contents_category_id']
          , ':contents_id' => $data['contents_id']
          , ':file_name' => $data['file_name']
        ));
    }

    public function getContentsMaxId()
    {
        $sql = 'SELECT MAX(a.contents_id) as max_contents_id
                  FROM tbl_contents a
        ';

        return $this->fetch($sql, array());
    }

    public function getContentsAttachMaxId()
    {
        $sql = 'SELECT MAX(a.contents_attachment_id) as max_contents_attachment_id
                  FROM tbl_contents_attachment as a
        ';

        return $this->fetch($sql, array());
    }

    public function setFunctionList($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_list
                    (   type
                      , primary_id
                      , function_group_id
                      , display_order
                    ) VALUES (
                        :type
                      ,(SELECT MAX(contents_id)
                          FROM tbl_contents
                       )
                      , :function_group_id
                      ,(SELECT subject_genre_id
                          FROM tbl_subject_section
                         WHERE subject_section_id = :subject_section_id)
                      , :subject_section_id
                      ,(SELECT display_order
                          FROM
                           (SELECT IFNULL(MAX(a.display_order) + 1, 1) as display_order
                              FROM tbl_contents as a
                           )
                       )
        ';

        return $this->execute($sql, array(
            ':type' => $data['type']
          , ':function_group_id' => $data['function_group_id']
          , ':subject_section_id' => $data['subject_section_id']
        ));
    }
}
