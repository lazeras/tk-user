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
class Group extends \Tk\Db\Model implements \Form\SelectInterface
{

    public $id = 0;
    public $name = '';
    public $description = '';
    //public $area = 'public';
    public $homeUrl = '/user/index.html';
    public $deletable = true;

    private $permissionList = null;




    /**
     * delete
     *
     * @return int
     */
    public function delete()
    {
        if ($this->deletable) {
            return parent::delete();
        }
    }

    /**
     *
     * @return int
     */
    public function save()
    {
        if ($this->deletable) {
            return parent::save();
        }
    }



    /**
     * Get the select option value
     * This is commonly the object's ID or index in an array
     *
     * @return string
     */
    public function getSelectValue()
    {
        return $this->id;
    }

    /**
     * Get the text to show in the select option
     *
     * @return string
     */
    public function getSelectText()
    {
        return  ucfirst(ucSplit($this->name) . ' Group');
    }


    /**
     * Add a permission to this role.
     *
     * @param \Usr\Db\Permission $perm
     * @return \Usr\Db\Group
     */
    public function addPermission(Permission $perm)
    {
        if (!$this->hasPermission($perm->name)) {
            self::getMapper()->addPermission($this->id, $perm->id);
            $this->getPermissionList();
            $this->permissionList[$perm->name] = $perm;
        }
        return $this;
    }

    /**
     * returns an empty array if no permissions allocated
     *
     * @return array
     */
    public function getPermissionList()
    {
        if (!$this->permissionList) {
            $res = Permission::getMapper()->findByGroupId($this->id);
            $this->permissionList = array();
            foreach ($res as $p) {
                $this->permissionList[$p->name] = $p;
            }
        }
        return $this->permissionList;
    }

    /**
     * Test to see if this role has a permission available
     * The arg can be an array of permission names or a single
     * permission name or a comma seperated string of permissions
     *
     * @param array|string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        
        if (!is_array($permission)) {
            if (!preg_match('/[,\s]/', $permission)) {
                $permission = array($permission);
            } else {
                // TODO: keep an eye on this one??
                $permission = preg_split('/[,\s]/', $permission);
            }
        }
        $list = $this->getPermissionList();
        foreach ($permission as $p) {
            if (isset($list[trim($p)])) return true;
        }
        return false;
    }


}


/**
 *
 *
 * @package Usr\Db
 */
class GroupValidator extends \Tk\Validator
{

    public function validate()
    {
        if (!preg_match('/.{1,64}/', $this->obj->name)) {
            $this->addError('name', 'Invalid name');
        }
//        if (!preg_match('/.{1,128}/', $this->obj->area)) {
//            $this->addError('area', 'Invalid user area');
//        }
    }

}

