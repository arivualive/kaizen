<?php
require_once '../config.php';

class SubjectGenreRepository extends PdoBase
{

    public function findSubjectGenreAll()
    {
        $sql = '
            SELECT
                b.subject_group_id
              , b.subject_group_name
              , a.subject_genre_id
              , a.subject_genre_name
              , a.enable
              , a.display_order
            FROM
                tbl_subject_genre a
            LEFT JOIN
                tbl_subject_group b USING (subject_group_id)
            ORDER BY
                a.display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findSubjectGenreEnable()
    {
        $sql = 'SELECT subject_genre_id
                                 , subject_genre_name
                                 , enable
                                 , display_order
                       FROM tbl_subject_genre
                ORDER BY display_order ASC ';

        return $this->fetchAll($sql);
    }

    public function findSubjectGenreId($data)
    {
        $sql = '
            SELECT
                *
            FROM
                tbl_subject_genre
            WHERE
                subject_genre_id = :subject_genre_id
        ';

        return $this->fetch($sql, array(
            ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

    public function updateSubjectGenreId($data)
    {
        $sql = '
            UPDATE
                tbl_subject_genre
            SET
                subject_group_id = :subject_group_id
              , subject_section_id = :subject_section_id
              , subject_genre_name = :subject_genre_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                subject_genre_id = :subject_genre_id
        ';

        return $this->execute($sql, array(
            ':subject_group_id' => $data['subject_group_id']
          , ':subject_section_id' => $data['subject_section_id']
          , ':subject_genre_name' => $data['subject_genre_name']
          , ':enable' => $data['enable']
          , ':display_order' => $data['display_order']
          , ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

    public function updateSubjectGenreDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_subject_genre
            SET
                display_order = :display_order
            WHERE
                subject_genre_id = :subject_genre_id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order']
          , ':subject_genre_id' => $data['subject_genre_id']
        ));
    }

    public function insertSubjectGenre($data)
    {
        $sql = '
            INSERT INTO
                tbl_subject_genre (
                    subject_group_id
                  , subject_section_id
                  , subject_genre_name
                  , display_order
                  , enable
                ) VALUES (
                    :subject_group_id
                  , :subject_section_id
                  , :subject_genre_name
                  , :display_order
                  , :enable
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':subject_group_id' => $data['subject_group_id']
          , ':subject_section_id' => $data['subject_section_id']
          , ':subject_genre_name' => $data['subject_genre_name']
          , ':display_order' => $data['display_order']
          , ':enable' => 1
        ));
    }

    public function findSubjectGenreName($subject_section_id)
    {
        $sql = '
            SELECT
                subject_genre_name
            FROM
                tbl_subject_genre b
            WHERE
                subject_section_id = :subject_section_id
            AND enable = 1
        ';

        return $this->execute($sql, array(':subject_section_id' => $subject_section_id));
}
    
}
