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
class PermissionMap extends \Tk\Db\Mapper
{

    /**
     * Create the data map
     *
     * @return \Tk\Model\DataMap
     */
    protected function makeDataMap()
    {
        $dataMap = new \Tk\Model\DataMap(__CLASS__);
        $this->table = 'userPermission';

        $dataMap->addIdProperty(\Tk\Model\Map\Integer::create('id'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('name'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('description'));

        return $dataMap;
    }

    /**
     * Check if a permission name exists in the system
     *
     * @param string $permissionName
     * @return bool
     */
    public function exists($permissionName)
    {
        $permissionName = $this->getDb()->escapeString($permissionName);
        $where = sprintf('`name` = %s ', enquote($permissionName));
        $obj = $this->selectMany($where, \Tk\Db\Tool::create('', 1))->current();
        if ($obj instanceof Permission) {
            return true;
        }
        return false;
    }


    /**
     * Get an array of available permissions to a role
     *
     * @param int $groupId
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findByGroupId($groupId, $tool = null)
    {
        $from = sprintf('userGroup_userPermission as t1 JOIN userPermission as t2 ON t1.permissionId = t2.id');
        $where = sprintf('t1.groupId = %s', (int)$groupId);
        $list = $this->selectFrom($from, $where, $tool, 't2', true);
        return $list;
    }

    /**
     * WARNING: This method will remove all role-permission associations
     *
     */
    public function deleteAll()
    {
        $sql = 'TRUNCATE userGroup_userPermission';
        //$this->getDb()->query($sql);
    }


    public function delete($obj)
    {
        throw new \Tk\Db\Exception('System dependant table, cannot delete permissions.');
    }

    public function update($obj)
    {
        throw new \Tk\Db\Exception('System dependant table, cannot update permissions.');
    }

    public function insert($obj)
    {
        throw new \Tk\Db\Exception('System dependant table, cannot insert permissions.');
    }

}

