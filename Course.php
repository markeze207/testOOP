<?php

class Course extends Courses
{

    protected $modxClass;

    protected $courseId;

    protected $api;

    protected $groupId;

    protected $course;

    public function __construct(modX $modxClass, $courseId, $apiKey) {
        $this->modxClass = $modxClass;
        $this->courseId = $courseId;

        $this->api = new RestApi($modxClass, $apiKey);

        self::set($this->api);
    }

    // Получаем курс по айдишнику
    public function getCourse()
    {
        $result = $this->api->get('courses.get', ['ID' => $this->courseId]);
        if(!empty($result['result']['item']))
        {
            $this->course = $result['result']['item'][0];
            return $this->course;
        }
        return false;
    }

    // Проверяем, курс запущен или нет
    public function isStart($group_id)
    {
        $resultGet = $this->api->get('courses_start.get', ['group_id' => $group_id, 'course_id' => $this->courseId]);
        if(!empty($resultGet['result']['item']))
        {
            return $resultGet['result']['item'][0]['ID'];
        } else {
            return false;
        }
    }

    // Запускаем курс
    public function startCourse($group_id): bool
    {
        $result = $this->api->get('courses_start.add', ['group_id' => $group_id, 'course_id' => $this->courseId]);
        if($result)
        {
            return true;
        } else {
            return false;
        }
    }

    // Заканчиваем курс
    public function endCourse($group_id): bool
    {
        $resultStart = $this->isStart($group_id);
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
    public function getDates()
    {
        if($this->course)
        {
            $date_course = $this->course['PROPERTY_1254'];
            
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

    // Получаем все даты от даты начала до даты конца
    public function getFilterDate($date_start, $date_end)
    {
        $start_date = date_create($date_start);
        $end_date   = date_create(date('Y-m-d', strtotime($date_end . "+1 days")));
        $interval = DateInterval::createFromDateString('1 day');
        $daterange = new DatePeriod($start_date, $interval, $end_date);

        $dates_out_interval_get = [];

        foreach($daterange as $dr){
            $dates_out_interval_get[] = $dr->format('Y-m-d');
        }
        return $dates_out_interval_get;
    }

    // Получаем время начала и конца курса
    public function getTimeCourse($group_id)
    {
        $res = $this->api->get('time_course.get', ['group_id' => $group_id, 'course_id' => $this->courseId]);
        if(!empty($res['result']['item']))
        {
            return $res['result']['item'][0];
        }
        return false;
    }

    // Получаем/устанавливаем group_id
    public function setGroupId($date_end): string
    {
        $dateTime = new DateTime($date_end);

        // Форматируем дату в нужном виде
        $formattedDate = $dateTime->format("d.m.Y");

        $this->groupId = $formattedDate.'-'.$_POST['course_id'];

        return $this->groupId;
    }

    // Устанавливаем время для курса
    public function setTimeCourse($group_id, $time_start, $time_end)
    {
        $res = $this->api->get('time_course.get', ['group_id' => $group_id, 'course_id' => $this->courseId]);

        $result = false;

        if(!empty($res['result']['item']))
        {
            $result = $this->api->get('time_course.update', ['ID' => $res['result']['item'][0]['ID'], 'time_end' => $time_end, 'time_start' => $time_start]);
        } else {
            $result = $this->api->get('time_course.add', ['group_id' => $group_id, 'course_id' => $this->courseId, 'time_end' => $time_end, 'time_start' => $time_start]);
        }
        return $result;
    }
}