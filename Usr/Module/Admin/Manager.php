<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module\Admin;

/**
 *
 *
 * @package Usr\Module\Admin
 */
class Manager extends \Mod\Module
{
    


    /**
     *
     */
    public function __construct()
    {
        $this->setPageTitle('User Manager');
        
        $this->set(\Mod\AdminPageInterface::CRUMBS_RESET, true);
        $this->add(\Mod\AdminPageInterface::PANEL_ACTIONS_LINKS, \Mod\Menu\Item::create('Add User', \Tk\Url::createHomeUrl('/user/edit.html'), 'fa fa-user'));
        $this->add(\Mod\AdminPageInterface::PANEL_ACTIONS_LINKS, \Mod\Menu\Item::create('Groups', \Tk\Url::createHomeUrl('/user/group/manager.html'), 'fa fa-group'));
    }

    /**
     * init
     */
    public function init()
    {

        // Create Table structure
        $ff = \Form\Factory::getInstance();
        $tf = \Table\Factory::getInstance();

        $this->table = $tf->createTable('UserManager');
        $this->table->addCell(Cb::create());
        $this->table->addCell($tf->createCellInteger('id'));
        $this->table->addCell($tf->createCellString('username'))->setKey()->setUrl(\Tk\Url::createHomeUrl('/user/edit.html'));
        $this->table->addCell($tf->createCellString('publicName'));
        $this->table->addCell($tf->createCellEmail('email'));
        $this->table->addCell(new Group('groupId'))->setLabel('Group');
        $this->table->addCell($tf->createCellDate('lastLogin'));
        $this->table->addCell($tf->createCellBoolean('active'));
        $this->table->addCell($tf->createCellDate('modified'));
        $this->table->addCell($tf->createCellDate('created'));


        $this->table->addAction($tf->createActionDelete());

        $list = \Usr\Db\Group::getMapper()->findAll();
        $this->table->addFilter($ff->createFieldSelect('groupId', $list))->prependOption('-- All --', '');
        $this->table->addFilter($ff->createFieldText('keywords'));

        $this->addChild($tf->createTableRenderer($this->table), 'Table1');

    }

    /**
     * execute
     */
    public function doDefault()
    {
        $filter = $this->table->getFilterValues();

        $list = \Usr\Db\User::getMapper()->findFiltered($filter, \Tk\Db\Tool::createFromRequest($this->getInstanceId(), ''));
        $this->table->setList($list);
    }

    /**
     * show
     */
    public function show()
    {
        $template = $this->getTemplate();
    }


    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<HTML
<?xml version="1.0" encoding="UTF-8"?>
<div class="User_Manager">
  <div var="Table1"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}

/**
 *
 *
 * @package Usr\Module\Admin
 */
class Cb extends \Table\Cell\Checkbox
{
    /**
     * Create a new cell
     *
     * @return \Table\Cell\Checkbox
     */
    static function create()
    {
        $obj = new self(self::CB_NAME, '', '');
        return $obj;
    }

    public function getTd($placement)
    {
        $disable = '';
        if ($placement->id == $this->getConfig()->getUser()->id) {
            $disable = ' disabled="disabled" ';
        }

        $str = '<input ' . $disable . ' type="checkbox" name="' . $this->getObjectKey(self::CB_NAME) . '[]" value="' . $placement->getId() . '" />';
        return $str;
    }

}

/**
 *
 *
 * @package Usr\Module\Admin
 */
class Group extends \Table\Cell\Iface
{
    static function create($property, $name = '')
    {
        $obj = new self($property, $name);
        return $obj;
    }
    public function getPropertyValue($obj)
    {
        $role = $obj->getGroup();
        if ($role) {
            return $role->name;
        }
        $value = parent::getPropertyValue($obj);
        return $value;
    }
}