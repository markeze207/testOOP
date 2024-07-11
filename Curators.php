<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/assets/components/ais/classes/Groups.php';
class Curators extends Groups
{

    private $modxClass;

    protected $course_id;
    public function __construct($modxClass, $course_id = null)
    {
        $this->modxClass = $modxClass;
        $this->course_id = $course_id;
    }

    // Получаем кураторов
    public function getCuratorsByCourseId()
    {
        $groups = new Groups($this->modxClass);
        $groupData = $groups->getGroupData('Кураторы');
        foreach($groupData as $userGroupMember)
        {
            $userGroup = $userGroupMember->getOne('User');

            $uid = $userGroup->id;

            if(!empty($uid))
            {
                // Запрос к базе данных
                $sql = "SELECT * FROM aismodx_ais_teacher_courses WHERE user_id = :user_id AND course_id = :course_id AND is_main = 3";

                $params = array(
                    ':user_id' => $uid,
                    ':course_id' => $this->course_id
                );

                $stmt = $this->modxClass->prepare($sql);
                $stmt->execute($params);

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $course['Curators'][] = [
                        'id' => $uid,
                        'name' => $userGroup->username,
                        'selected' => true,
                    ];
                } else {
                    $course['Curators'][] = [
                        'id' => $uid,
                        'name' => $userGroup->username,
                        'selected' => false,
                    ];
                }
            }
        }
        file_put_contents($_SERVER['DOCUMENT_ROOT'].'/assets/components/ais/classes/test.txt', print_r($course, true));
        return $course['Curators'];
    }
}