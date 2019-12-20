<?php
require_once '../config.php';

class StudentRepository extends PdoBase
{
    public function findStudentAll()
    {
        $sql = '
            SELECT
                student_id
              , a.school_id
              , b.school_name
              , student_name
              , a.enable
              , a.display_order
            FROM
                tbl_student a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                display_order ASC
        ';

        return $this->fetchAll($sql);
    }

    public function findStudentCount()
    {
        $sql = '
            SELECT
                count(*) as count
            FROM
                tbl_student
        ';

        return $this->fetch($sql);
    }

    public function findStudentOffset($data)
    {
        $sql = 'SELECT student_id
                     , a.school_id
                     , b.school_name
                     , student_name
                     , a.enable
                     , a.display_order
                  FROM tbl_student a
             LEFT JOIN tbl_school b USING (school_id)
              ORDER BY display_order ASC
                 LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findStudentSchool($data)
    {
        $sql = '
            SELECT
                student_id
              , a.school_id
              , b.school_name
              , student_name
              , a.enable
              , a.display_order
            FROM
                tbl_student a
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

    public function findStudentOrderByName($data)
    {
        $sql = '
            SELECT
                student_id
              , a.school_id
              , b.school_name
              , student_name
              , a.enable
              , a.display_order
            FROM
                tbl_student a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                student_name ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findStudentOrderById($data)
    {
        $sql = '
            SELECT
                student_id
              , a.school_id
              , b.school_name
              , student_name
              , a.enable
              , a.display_order
            FROM
                tbl_student a
            LEFT JOIN
                tbl_school b
            USING
                (school_id)
            ORDER BY
                student_id ASC
                LIMIT :limit OFFSET :offset
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':offset', (int) $data['offset'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int) $data['limit'], PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findStudentId($data)
    {
        $sql = '
            SELECT
                student_id
              , student_code
              , school_id
              , student_name
              , id
              , enable
              , display_order
            FROM
                tbl_student where student_id = :id
        ';

        return $this->fetch($sql, array(
            ':id' => $data['id']
        ));
    }

    public function updateStudentId($data)
    {
        $sql = '
            UPDATE
                tbl_student
            SET
                school_id = :school_id
              , student_name = :student_name
              , enable = :enable
              , display_order = :display_order
            WHERE
                student_id = :student_id
        ';

        return $this->execute($sql, array(
            ':school_id' => $data['school_id'],
            ':student_name' => $data['student_name'],
            ':enable' => $data['enable'],
            ':display_order' => $data['display_order'],
            ':student_id' => $data['student_id']
        ));
    }

    public function updateStudentDisplayOrder($data)
    {
        $sql = '
            UPDATE
                tbl_student
            SET
                display_order = :display_order
            WHERE
                student_id = :id
        ';

        return $this->execute($sql, array(
            ':display_order' => $data['display_order'],
            ':id' => $data['id']
        ));
    }

    public function insertStudent($data)
    {
        $sql = '
            INSERT INTO
                tbl_student (
                    school_id
                  , student_name
                  , enable
                  , display_order
                ) VALUES (
                    :school_id
                  , :student_name
                  , :enable
                  , :display_order
                )
        ';

        if ($data['display_order'] == '') {
            $data['display_order'] = 0;
        }

        return $this->execute($sql, array(
            ':school_id' => $data['school_id'],
            ':student_name' => $data['student_name'],
            ':enable' => 1,
            ':display_order' => $data['display_order']
        ));
    }

    public function authCheck($data) {
        $sql = '
            SELECT
                student_id
              , school_id
              , student_name
              , joining
              , enable
              , bit_subject
            FROM
                tbl_student
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

    public function updateAccessData($data) {
        $sql = '
            UPDATE
                tbl_student
            SET
                access_datetime = NOW()
            WHERE
                student_id = :student_id
        ';

        return $this->execute($sql, array(
            ':student_id' => $data['student_id']
        ));
    }
}
