<?php
require_once '../config.php';

class TeacherRepository extends PdoBase
{
    public function findTeacherAll()
    {
        $sql = '
            SELECT
                teacher_id
              , a.school_id
              , b.school_name
              , teacher_name
              , a.enable
              , a.display_order
            FROM
                tbl_teacher a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findTeacherCount()
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_teacher
        ';

        return $this->fetch($sql);
    }

    public function findTeacherOffset($data)
    {
        $sql = 'SELECT teacher_id
                     , a.school_id
                     , b.school_name
                     , teacher_name
                     , a.enable
                     , a.display_order
                  FROM tbl_teacher a
             LEFT JOIN tbl_school b USING (school_id)
              ORDER BY display_order ASC
                 LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTeacherSchool($data)
    {
        $sql = '
            SELECT
                teacher_id
              , a.school_id
              , b.school_name
              , teacher_name
              , a.enable
              , a.display_order
            FROM
                tbl_teacher a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            WHERE
                    school_id = :school_id
                AND
                    a.enable = 1
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql, array(
            ':school_id' => $data['school_id']
        ));
    }

    public function findTeacherOrderByName($data)
    {
        $sql = '
            SELECT
                teacher_id
              , a.school_id
              , b.school_name
              , teacher_name
              , a.enable
              , a.display_order
            FROM
                tbl_teacher a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                teacher_name ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTeacherOrderById($data)
    {
        $sql = '
            SELECT
                teacher_id
              , a.school_id
              , b.school_name
              , teacher_name
              , a.enable
              , a.display_order
            FROM
                tbl_teacher a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                teacher_id ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findTeacherId($data)
    {
        $sql = '
            SELECT
                teacher_id
              , school_id
              , teacher_name
              , enable
              , display_order
            FROM
                tbl_teacher where teacher_id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    public function updateTeacherId($data)
    {
        $sql = '
            UPDATE
                tbl_teacher
            SET
                school_id = :school_id
              , name = :teacher_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                teacher_id = :teacher_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id'],
            ':teacher_name' => $data['teacher_name'],
            ':enable' => $data['enable'],
            ':display_order' => $data['display_order'],
            ':teacher_id' => $data['teacher_id']
        ));
    }

    public function updateTeacherDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_teacher
            SET
                display_order = :display_order
            WHERE
                teacher_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order'],
            ':id' => $data['id']
        ));
    }

    public function insertTeacher($data)
    {
        $sql = '
            INSERT INTO
                tbl_teacher (
                    school_id
                  , teacher_name
                  , enable
                  , display_order
                ) VALUES (
                    :school_id
                  , :teacher_name
                  , :enable
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id'],
            ':teacher_name' => $data['teacher_name'],
            ':enable' => 1,
            ':display_order' => $data['display_order']
        ));
    }

    public function authCheck($data) {
        $sql = '
            SELECT
                teacher_id
              , school_id
              , teacher_name
            FROM
                tbl_teacher
            WHERE
                    id = :id
                AND
                    password = :pw
                AND
                    enable = 1
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
          , ':pw' => $data['pw']
        ));
    }
}
