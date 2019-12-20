<?php

class SubjectSectionAccessSave
{
    private $id;
    private $curl;

    public function __construct($id, $curl)
    {
        $this->id = $id;
        $this->curl = $curl;

    }

    private function deleteSectionGrade()
    {
        $curl_data = array(
            'repository' => 'GradeSubjectSectionRepository',
            'method' => 'deleteSubjectSection',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($curl_data);
    }

    public function saveSectionGrade()
    {
        $result = $this->deleteSectionGrade();

        if (isset($_POST['grade_id'])) {
            $data['grade_id'] = $_POST['grade_id'];
            $data['subject_section_id'] = $this->id;

            $curl_data = array('repository' => 'GradeSubjectSectionRepository', 'method' => 'insertSubjectSection', 'params' => $data);
            $result = $this->curl->send($curl_data);

            $message = "変更できませんでした。";

            if ($result == count($data['grade_id'])) {
                $message = "変更しました。";
            }

            return $message;

        } elseif ($result > 0) {
            return "削除しました。";
        }
    }

    private function deleteSectionClassroom()
    {
        $curl_data = array(
            'repository' => 'ClassroomSubjectSectionRepository',
            'method' => 'deleteSubjectSection',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($curl_data);
    }

    public function saveSectionClassroom()
    {
        $result = $this->deleteSectionClassroom();

        if (isset($_POST['classroom_id'])) {
            $data['classroom_id'] = $_POST['classroom_id'];
            $data['subject_section_id'] = $this->id;

            $curl_data = array('repository' => 'ClassroomSubjectSectionRepository', 'method' => 'insertSubjectSection', 'params' => $data);
            $result = $this->curl->send($curl_data);

            $message = "変更できませんでした。";

            if ($result == count($data['classroom_id'])) {
                $message = "変更しました。";
            }

            return $message;

        } elseif ($result > 0) {
            return "削除しました。";
        }
    }

    private function deleteSectionCourse()
    {
        $curl_data = array(
            'repository' => 'CourseSubjectSectionRepository',
            'method' => 'deleteSubjectSection',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($curl_data);
    }

    public function saveSectionCourse()
    {
        $result = $this->deleteSectionCourse();

        if (isset($_POST['course_id'])) {
            $data['course_id'] = $_POST['course_id'];
            $data['subject_section_id'] = $this->id;

            $curl_data = array('repository' => 'CourseSubjectSectionRepository', 'method' => 'insertSubjectSection', 'params' => $data);
            $result = $this->curl->send($curl_data);

            $message = "変更できませんでした。";

            if ($result == count($data['course_id'])) {
                $message = "変更しました。";
            }

            return $message;

        } elseif ($result > 0) {
            return "削除しました。";
        }
    }
}
