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
class Permission extends \Tk\Db\Model implements \Form\SelectInterface
{

    public $id = 0;
    public $name = '';
    public $description = '';


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
        return $this->name;
    }


}



/**
 *
 *
 * @package Usr\Db
 */
class PermissionValidator extends \Tk\Validator
{

    public function validate()
    {
        if (!preg_match('/.{1,64}/', $this->obj->name)) {
            $this->addError('name', 'Invalid name');
        }
    }
}


