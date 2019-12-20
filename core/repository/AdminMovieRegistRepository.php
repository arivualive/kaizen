<?php
require_once '../config.php';

class AdminMovieRegistRepository extends PdoBase
{
    //------ tbl_contents ------//
    // コンテンツの取得 (getter tbl_contents)
    public function getContents($data)
    {
        $sql = ' SELECT a.contents_id
                      , a.school_id
                      , a.contents_name
                      , a.comment
                      , a.first_day
                      , a.last_day
                      , a.bit_classroom
                      , a.proportion
                   FROM tbl_contents a
                  WHERE a.contents_id = :contents_id
                    AND a.enable = 1
        ';

        return $this->fetch($sql, array(
            ':contents_id' => $data['contents_id']
        ));
    }

    // コンテンツの挿入 (setter tbl_contents)
    public function insertContents($data)
    {
        $sql = ' INSERT INTO
                    tbl_contents
                    (   school_id
                      , subject_section_id
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
                      , proportion
                    ) VALUES (
                        :school_id
                      , :subject_section_id
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
                      , :proportion
                    )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':subject_section_id' => $data['subject_section_id']
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
          , ':proportion' => $data['proportion']
        ));
    }

    // コンテンツの編集 (setter tbl_contents)
    public function editContents($data)
    {
        $sql = 'UPDATE tbl_contents
                   SET contents_name = :contents_name
                     , comment = :comment
                     , first_day = :first_day
                     , last_day = :last_day
                     , bit_classroom = :bit_classroom
                 WHERE contents_id = :contents_id
        ';

        return $this->exec($sql, array(
            ':contents_id' => $data['contents_id']
          , ':contents_name' => $data['contents_name']
          , ':comment' => $data['comment']
          , ':first_day' => $data['first_day']
          , ':last_day' => $data['last_day']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // コンテンツの論理削除 (setter tbl_contents)
    public function deleteContents($data)
    {
        $sql = 'UPDATE tbl_contents
                   SET enable = 0
                 WHERE contents_id = :contents_id
        ';

        return $this->exec($sql, array(
            ':contents_id' => $data['contents_id']
        ));
    }

    // コンテンツ最大IDの取得 (getter tbl_contents MAX_ID)
    public function getContentsMaxId()
    {
        $sql = 'SELECT MAX(a.contents_id) as max_contents_id
                  FROM tbl_contents a
        ';

        return $this->fetch($sql, array());
    }
    //!!!!!! tbl_contents !!!!!!//

    //------ tbl_contents_attachment ------//
    // コンテンツの取得 (getter tbl_contents)
    public function getContentsAttachment($data)
    {
        $sql = ' SELECT a.contents_attachment_id
                      , a.contents_category_id
                      , a.contents_id
                      , a.file_name
                   FROM tbl_contents_attachment as a
                  WHERE a.contents_id = :contents_id
        ';

        return $this->fetch($sql, array(
            ':contents_id' => $data['contents_id']
        ));
    }

    // コンテンツ添付ファイルの挿入 (setter tbl_contents_attachment)
    public function insertContentsAttachment($data)
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

    // コンテンツ添付ファイルの物理削除 (setter tbl_contents_attachment)
    public function deleteContentsAttachment($data)
    {
        $sql = 'DELETE
                  FROM tbl_contents_attachment
                 WHERE contents_id = :contents_id
        ';

        return $this->exec($sql, array(
            ':contents_id' => $data['contents_id']
        ));
    }

    // コンテンツ添付ファイル最大IDの取得 (getter tbl_contents_attachment MAX_ID)
    public function getContentsAttachMaxId()
    {
        $sql = 'SELECT MAX(a.contents_attachment_id) as max_contents_attachment_id
                  FROM tbl_contents_attachment as a
        ';

        return $this->fetch($sql, array());
    }
    //!!!!!! tbl_contents_attachment !!!!!!//

    //------ tbl_function_list ------//
    // 一覧情報の登録 (setter tbl_function_list)
    public function setFunctionList($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_list
                    (   type
                      , primary_id
                      , parent_function_group_id
                      , display_order
                    ) VALUES (
                        :type
                      ,(SELECT IFNULL(MAX(contents_id), 1)
                          FROM tbl_contents
                       )
                      , 0
                      ,(SELECT display_order
                          FROM
                           (SELECT IFNULL(MAX(a.display_order) + 1, 1) as display_order
                              FROM tbl_function_list as a
                           ) AS display_order
                       )
                    )
        ';

        return $this->execute($sql, array(
            ':type' => $data['type']
        ));
    }
    //!!!!!! tbl_function_list !!!!!!//
}
