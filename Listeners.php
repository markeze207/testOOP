<?php

class Listeners extends RestApi
{

    protected $modxClass;

    protected $courseId;

    protected  $api;

    protected $apiKey;

    public function __construct(modX $modxClass, $courseId, $apiKey) {
        $this->modxClass = $modxClass;
        $this->courseId = $courseId;
        $this->apiKey = $apiKey;
        $this->api = new RestApi($modxClass, $apiKey);
    }

    // Получаем список слушателей
    public function getListeners($date_start, $date_end, $filter = array())
    {
        $filter['PRODUCT_ID'] = $this->courseId;

        $listeners = $this->api->get('student.get', $filter);
        $listener_idx = 0;

        if(isset($listeners['result']['item']) && !empty($listeners['result']['item'])){
            foreach($listeners['result']['item'] as $listener){
                $listener_date_explode = explode('T', $listener['ufCrm8_1687858443601']);
                $listener_date1 = strtotime($listener_date_explode[0]);
                $listener_date2 = strtotime($listener['ufCrm8_1687858456238']);
               
                if (($listener['stageId'] != 'DT174_8:NEW' && $listener_date1 == strtotime($date_start))) {
                    $listener_idx++;
                    $course['students'][$listener_idx] = $listener;
                }
            }
            $this->listeners = $course['students'];
            return $course['students'];
        }
    }

    // Получаем список слушателей дистанционного обучения
    public function getListenersDist()
    {
        foreach ($this->listeners as $key => $value) {
            if ($value['ufCrm8_1687858196911'] === 'Дистанционное участие') {
                $newArray[] = $this->listeners[$key];
            }
        }
        return $newArray;
    }

    // Получаем список слушателей очного обучения
    public function getListenersFullTime()
    {
        foreach ($this->listeners as $key => $value) {
            if ($value['ufCrm8_1687858196911'] === 'Очное участие') {
                $newArray[] = $this->listeners[$key];
            }
        }
        return $newArray;
    }

    // Получаем кол-во слушателей
    public function getListenerCount($listener)
    {
       return count($listener);
    }
}