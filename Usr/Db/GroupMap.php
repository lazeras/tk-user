<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Db;

/**
 *
 *
 * @package Usr\Db
 */
class GroupMap extends \Tk\Db\Mapper
{

    /**
     * Create the data map
     *
     * @return \Tk\Model\DataMap
     */
    protected function makeDataMap()
    {
        $dataMap = new \Tk\Model\DataMap(__CLASS__);
        $this->table = 'userGroup';

        $dataMap->addIdProperty(\Tk\Model\Map\Integer::create('id'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('name'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('description'));
        //$dataMap->addProperty(\Tk\Model\Map\String::create('area'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('homeUrl'));
        $dataMap->addProperty(\Tk\Model\Map\Boolean::create('deletable'));

        return $dataMap;
    }

    /**
     * findByName
     *
     * @param string $name
     * @return \Usr\Db\Group
     */
    public function findByName($name)
    {
        return $this->selectMany('`name` = ' . $this->getDb()->quote($name), \Tk\Db\Tool::create('', 1))->current();
    }

    /**
     * Add a permission link to the db
     *
     * @param int $groupId
     * @param int $permissionId
     * @return bool
     */
    public function addPermission($groupId, $permissionId)
    {
        $sql = sprintf('INSERT INTO userGroup_userPermission (groupId, permissionId) VALUES (%s, %s)', (int)$groupId, (int)$permissionId);
        $this->getDb()->query($sql);
        return true;
    }

    /**
     *
     * @param int $groupId
     * @return \Tk\Db\ArrayObject
     */
    public function deleteGroupPermissions($groupId)
    {
        $sql = sprintf('DELETE FROM userGroup_userPermission WHERE groupId = %s', (int)$groupId);
        return $this->getDb()->query($sql);
    }

    /**
     * delete
     *
     * @param mixed $obj
     * @return int
     */
    public function delete($obj)
    {
        $sql = sprintf('DELETE FROM userGroup WHERE id = %s', $obj->id);
        $this->getDb()->query($sql);
        $sql = sprintf('DELETE FROM userGroup_userPermission WHERE groupId = %s', $obj->id);
        return $this->getDb()->query($sql);
    }


}

