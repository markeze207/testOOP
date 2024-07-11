<?php

class Groups
{
    private $modxClass;

    public function __construct($modxClass)
    {
        $this->modxClass = $modxClass;
    }

    // Получаем группу пользователей по имени
    public function getGroupData($name)
    {
        $groupId = $this->modxClass->getObject('modUserGroup', array('name' => $name))->get('id');

        return $this->modxClass->getCollection('modUserGroupMember', array('user_group' => $groupId));
    }
}