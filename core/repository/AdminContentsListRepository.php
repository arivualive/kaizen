<?php
require_once '../config.php';

class AdminContentsListRepository extends PdoBase
{
    //------ tbl_contents ------//
    // コンテンツの取得 (getter tbl_contents)
    public function getContents($data)
    {
        $sql = 'SELECT 0 as type
                     , a.contents_id as primary_key
                     , a.contents_name as title
                     , a.register_datetime
                     , a.contents_extension_id
                     , a.subject_section_id
                     , a.first_day
                     , a.last_day
                     , c.parent_function_group_id
                     , c.display_order
                     , a.bit_classroom
                  FROM tbl_contents a
             LEFT JOIN mst_contents_category b
                 USING (contents_category_id)
             LEFT JOIN tbl_function_list c
                    ON c.type = 0
                   AND c.primary_id = a.contents_id
                 WHERE a.school_id = :school_id
                   AND a.bit_classroom = :bit_classroom
                   AND a.enable = 1
              GROUP BY a.contents_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
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
    //!!!!!! tbl_contents !!!!!!//

    //------ tbl_questionnaire ------//
    // アンケートの取得 (getter tbl_questionnaire)
    public function getQuestionnaire($data)
    {
        $sql = 'SELECT (1+:type) as type
                     , a.questionnaire_id as primary_key
                     , a.title
                     , CASE
                       WHEN a.register_datetime < a.start_day THEN
                         a.start_day
                       WHEN a.register_datetime > a.start_day THEN
                         a.register_datetime
                       END as register_datetime
                     , b.subject_section_id
                     , a.start_day
                     , a.last_day
                     , c.parent_function_group_id
                     , c.display_order
                     , a.bit_classroom
                  FROM tbl_questionnaire as a
             LEFT JOIN tbl_questionnaire_target_range_school as b
                 USING (questionnaire_id)
             LEFT JOIN tbl_function_list c
                    ON c.type = (1+:type)
                   AND c.primary_id = a.questionnaire_id
                 WHERE a.school_id = :school_id
                   AND a.type = :type
                   AND a.bit_classroom = :bit_classroom
                   AND a.enable = 1
              GROUP BY a.questionnaire_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':type' => $data['type']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // アンケートの論理削除 (setter tbl_questionnaire)
    public function deleteQuestionnaire($data)
    {
        $sql = 'UPDATE tbl_questionnaire
                   SET enable = 0
                 WHERE questionnaire_id = :questionnaire_id
        ';

        return $this->exec($sql, array(
            ':questionnaire_id' => $data['questionnaire_id']
        ));
    }
    //!!!!!! tbl_questionnaire !!!!!!//

    //------ tbl_quiz ------//
    // クイズの取得 (getter tbl_quiz)
    public function getQuiz($data)
    {
        $sql = 'SELECT 3 as type
                     , a.quiz_id as primary_key
                     , a.title
                     , CASE
                       WHEN a.register_datetime < a.start_day THEN
                         a.start_day
                       WHEN a.register_datetime > a.start_day THEN
                         a.register_datetime
                       END as register_datetime
                     , a.start_day
                     , a.last_day
                     , a.subject_section_id
                     , a.repeat_challenge
                     , a.qualifying_score
                     , e.parent_function_group_id
                     , e.display_order
                     , a.bit_classroom
                  FROM tbl_quiz as a
             LEFT JOIN tbl_quiz_target_access_restriction as b
                 USING (quiz_id)
             LEFT JOIN tbl_school c
                 USING (school_id)
             LEFT JOIN tbl_quiz_answer as d
                    ON a.quiz_id = d.quiz_id
             LEFT JOIN tbl_function_list e
                    ON e.type = 3
                   AND e.primary_id = a.quiz_id
                 WHERE a.school_id = :school_id
                   AND a.bit_classroom = :bit_classroom
                   AND a.enable = 1
              GROUP BY a.quiz_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // クイズの論理削除 (setter tbl_quiz)
    public function deleteQuiz($data)
    {
        $sql = 'UPDATE tbl_quiz
                   SET enable = 0
                 WHERE quiz_id = :quiz_id
        ';

        return $this->exec($sql, array(
            ':quiz_id' => $data['quiz_id']
        ));
    }
    //!!!!!! tbl_quiz !!!!!!//

    //------ tbl_function_group ------//
    // フォルダの取得 (getter tbl_function_group)
    public function getFunctionGroup($data)
    {
        $sql = 'SELECT 4 as type
                     , a.function_group_id as primary_key
                     , a.function_group_name as title
                     , b.parent_function_group_id
                     , b.display_order
                     , a.bit_classroom
                  FROM tbl_function_group as a
             LEFT JOIN tbl_function_list b
                    ON b.type = 4
                   AND b.primary_id = a.function_group_id
                 WHERE a.school_id = :school_id
                   AND a.bit_classroom = :bit_classroom
                   AND a.enable = 1
              GROUP BY a.function_group_id
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // フォルダの挿入 (setter tbl_function_group)
    public function insertFunctionGroup($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_group
                    (   school_id
                      , function_group_name
                      , bit_classroom
                      , enable
                    ) VALUES (
                        :school_id
                      , :function_group_name
                      , :bit_classroom
                      , 1
                    )
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id']
          , ':function_group_name' => $data['function_group_name']
          , ':bit_classroom' => $data['bit_classroom']
        ));
    }

    // フォルダの編集 (setter tbl_function_group)
    public function editFunctionGroup($data)
    {
        $sql = 'UPDATE tbl_function_group
                   SET function_group_name = :function_group_name
                 WHERE function_group_id = :function_group_id
        ';

        return $this->execute($sql, array(
            ':function_group_id' => $data['function_group_id']
          , ':function_group_name' => $data['function_group_name']
        ));
    }

    // フォルダの論理削除 (setter tbl_function_group)
    public function deleteFunctionGroup($data)
    {
        $sql = 'UPDATE tbl_function_group
                   SET enable = 0
                 WHERE function_group_id = :function_group_id
        ';

        return $this->exec($sql, array(
            ':function_group_id' => $data['function_group_id']
        ));
    }

    // コンテンツ添付ファイル最大IDの取得 (getter tbl_contents_attachment MAX_ID)
    public function getFunctionGroupMaxId()
    {
        $sql = 'SELECT MAX(a.function_group_id) as max_function_group_id
                  FROM tbl_function_group as a
        ';

        return $this->fetch($sql, array());
    }
    //!!!!!! tbl_function_group !!!!!!//

    //------ tbl_function_list ------//
    // コンテンツ一覧の挿入 (setter tbl_function_list)
    public function insertFunctionList($data)
    {
        $sql = ' INSERT INTO
                    tbl_function_list
                    (   type
                      , primary_id
                      , parent_function_group_id
                      , display_order
                    ) VALUES (
                        :type
                      , :primary_id
                      , :parent_function_group_id
                      , (SELECT IFNULL(MAX(a.display_order) + 1, 1)
                           FROM tbl_function_list as a
                        )
                    )
        ';

        return $this->execute($sql, array(
            ':type' => $data['type']
          , ':primary_id' => $data['primary_id']
          , ':parent_function_group_id' => $data['parent_function_group_id']
        ));
    }

    // コンテンツ一覧の編集_ソート (setter tbl_function_list)
    public function sortEditFunctionList($data)
    {
        $sql = 'UPDATE tbl_function_list
                   SET display_order = :display_order
                 WHERE primary_id = :primary_id
                   AND type = :type
        ';

        return $this->exec($sql, array(
            ':display_order' => $data['display_order']
          , ':primary_id' => $data['primary_id']
          , ':type' => $data['type']
        ));
    }

    // コンテンツ一覧の編集_フォルダ (setter tbl_function_list)
    public function folderEditFunctionList($data)
    {
        $sql = 'UPDATE tbl_function_list
                   SET parent_function_group_id = :parent_function_group_id
                 WHERE primary_id = :primary_id
                   AND type = :type
        ';

        return $this->exec($sql, array(
            ':parent_function_group_id' => $data['parent_function_group_id']
          , ':primary_id' => $data['primary_id']
          , ':type' => $data['type']
        ));
    }

    // コンテンツ一覧の論理削除 (setter tbl_function_list)
    public function deleteFunctionList($data)
    {
        $sql = 'UPDATE tbl_function_list
                   SET parent_function_group_id = 0
                 WHERE parent_function_group_id = :parent_function_group_id
        ';

        return $this->exec($sql, array(
            ':parent_function_group_id' => $data['parent_function_group_id']
        ));
    }
    //!!!!!! tbl_function_list !!!!!!//
}
