<?php

class SubjectSectionAccess
{
    private $id;
    private $curl;
    private $element;

    public function __construct($id, $curl)
    {
        $this->id = $id;
        $this->curl = $curl;
        $this->element = new AccessElement($curl);

    }

    protected function getSectionGrade()
    {
        $data = array(
            'repository' => 'GradeSubjectSectionRepository',
            'method' => 'findAllSubjectSectionId',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function checkedGrade()
    {
        $grade_section = $this->getSectionGrade();
        $grade_all = $this->element->getGrade();

        foreach ((array) $grade_section as $value) {
            foreach ($grade_all as $key => $item) {
                $grade_all[$key] += array(' checked' => '');
                if ($value['grade_id'] == $item['grade_id']) {
                    $grade_all[$key]['checked'] = ' checked';
                }
            }
        }
        
        return $grade_all;
    }

    protected function getSectionClassroom()
    {
        $data = array(
            'repository' => 'ClassroomSubjectSectionRepository',
            'method' => 'findAllSubjectSectionId',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function checkedClassroom()
    {
        $classroom_section = $this->getSectionClassroom();
        $classromm_all = $this->element->getClassroom();

        foreach ((array) $classroom_section as $value) {
            foreach ($classromm_all as $key => $item) {
                $classromm_all[$key] += array(' checked' => '');
                if ($value['classroom_id'] == $item['classroom_id']) {
                    $classromm_all[$key]['checked'] = ' checked';
                }
            }
        }
        
        return $classromm_all;
    }

    public function getSectionCourse()
    {
        $data = array(
            'repository' => 'CourseSubjectSectionRepository',
            'method' => 'findAllSubjectSectionId',
            'params' => array('subject_section_id' => $this->id)
        );

        return $this->curl->send($data);
    }

    public function checkedCourse()
    {
        $course_section = $this->getSectionCourse();
        $course_all = $this->element->getCourse();

        foreach ((array) $course_section as $value) {
            foreach ($course_all as $key => $item) {
                $course_all[$key] += array(' checked' => '');
                if ($value['course_id'] == $item['course_id']) {
                    $course_all[$key]['checked'] = ' checked';
                }
            }
        }
        
        return $course_all;
    }

}
