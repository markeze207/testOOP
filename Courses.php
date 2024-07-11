<?php

abstract class Courses extends RestApi
{
    protected $groupId;

    protected $course;

    public function set($api) {
        $this->api = $api;
    }

    // Получаем курс по айдишнику
    public function getCourseById($courseId)
    {
        $result = $this->api->get('courses.get', ['ID' => $courseId]);
        if(!empty($result['result']['item']))
        {
            $this->course = $result['result']['item'][0];
            return $this->course;
        }
        return false;
    }

    // Проверяем, курс запущен или нет
    public function isStartById($group_id, $courseId)
    {
        $resultGet = $this->api->get('courses_start.get', ['group_id' => $group_id, 'course_id' => $courseId]);
        if(!empty($resultGet['result']['item']))
        {
            return $resultGet['result']['item'][0]['ID'];
        } else {
            return false;
        }
    }

    // Запускаем курс
    public function startCourseById($group_id, $courseId): bool
    {
        $result = $this->api->get('courses_start.add', ['group_id' => $group_id, 'course_id' => $courseId]);
        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    // Заканчиваем курс
    public function endCourseById($group_id, $courseId): bool
    {
        $resultStart = $this->isStart($group_id, $courseId);
        if($resultStart)
        {
            $result = $this->api->get('courses_start.delete', ['id' => $resultStart]);
            if($result)
            {
                return $resultStart;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Получаем даты курса
    public function getDatesById($courseId)
    {
        $course = $this->getCourseById($courseId);
        if($course->course)
        {
            $date_course = $course['PROPERTY_1254'];

            $dates_arr = json_decode($date_course, true);
            $dates_out = [];
            $idx_time = 0;
            foreach ($dates_arr as $key => $date_item) {

                if (($key + 1) % 2 == 0) {
                    $dates_out[$idx_time][] = date('Y-m-d', strtotime($date_item['value']));
                    $idx_time++;
                } else {
                    $dates_out[$idx_time][] = date('Y-m-d', strtotime($date_item['value']));
                }
            }
            return $dates_out;
        }
        return false;
    }

    // Получаем время начала и конца курса
    public function getTimeCourseById($group_id, $courseId)
    {
        $res = $this->api->get('time_course.get', ['group_id' => $group_id, 'course_id' => $courseId]);
        if(!empty($res['result']['item']))
        {
            return $res['result']['item'][0];
        }
        return false;
    }


    // Устанавливаем время для курса
    public function setTimeCourseById($group_id, $time_start, $time_end, $courseId)
    {
        $res = $this->api->get('time_course.get', ['group_id' => $group_id, 'course_id' => $courseId]);

        $result = false;

        if(!empty($res['result']['item']))
        {
            $result = $this->api->get('time_course.update', ['ID' => $res['result']['item'][0]['ID'], 'time_end' => $time_end, 'time_start' => $time_start]);
        } else {
            $result = $this->api->get('time_course.add', ['group_id' => $group_id, 'course_id' => $courseId, 'time_end' => $time_end, 'time_start' => $time_start]);
        }
        return $result;
    }
}