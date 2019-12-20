<?php
require_once '../config.php';

class ReportRepository extends PdoBase
{
    public function getContents() {
        $sql = '
            SELECT DISTINCT
                school_contents_id id,
                school_contents_id value
            FROM
                log_contents_history_student
        ';

        return $this->fetchAll($sql);
    }

    public function getLogByContent($data) {
        $sql = '
            SELECT 
                a.history_id, 
                b.event_id, 
                b.position/1000 position , 
                d.reason, 
                d.event_reason_id, 
                e.event_action_id,
                a.duration/1000 duration
            FROM 
                log_contents_history_student a 
            INNER JOIN 
                log_contents_history_student_event b 
            ON 
                a.history_id = b.history_id
            INNER JOIN 
                log_contents_history_student_event_reason c 
            ON 
                b.event_id = c.event_id
            INNER JOIN 
                mst_event_reason d 
            ON 
                c.event_reason_id = d.event_reason_id
            INNER JOIN 
                mst_event_action e 
            ON 
                b.event_action_id = e.event_action_id
            WHERE 
                a.school_contents_id = :id
            AND 
                d.event_reason_id IN (3,4,5,6,7,12,24,25)
            AND 
                !(b.event_action_id = 2 AND d.event_reason_id IN (3,5))
        ';

        return $this->fetchAll($sql, array(':id' => $data['id']));
    }

    public function getBlocksByContent($data) {
        $sql = 'select * from tbl_contents_blocks where contents_id =  :id';

        return $this->fetchAll($sql, array(':id' => $data['id']));
    }
}
