<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module\Admin\Group;
use Mod\AdminPageInterface;

/**
 * Manager
 *
 * @package Usr\Module\Admin\Group
 */
class Manager extends \Mod\Module
{
    /**
     * @var \Table\Table
     *
     */
    protected $table = null;

    /**
     *
     * @var \Tk\Url
     */
    protected $editUrl = null;


    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->setPageTitle('Group Manager');
        $this->editUrl = \Tk\Url::createHomeUrl('/user/group/edit.html');
        
        $this->add(AdminPageInterface::PANEL_ACTIONS_LINKS, \Mod\Menu\Item::create('Add Group', \Tk\Url::createHomeUrl('/user/group/edit.html'), 'fa fa-group'));
    }


    /**
     * init
     */
    public function init()
    {
        $tf = \Table\Factory::getInstance();

        // Create Table structure
        $this->table = $tf->createTable('Table');
        $this->table->addCell($tf->createCellCheckbox());
        $this->table->addCell($tf->createCellInteger('id'));
        $this->table->addCell(String::create('name'))->setKey()->setUrl($this->editUrl);
        //$this->table->addCell(String::create('area'));
        $this->table->addCell(new Permissions('permissions'));

        $this->table->addAction($tf->createActionDelete());

        $this->addChild($tf->createTableRenderer($this->table), $this->table->getId());

    }

    /**
     * execute
     */
    public function doDefault()
    {
        //$filter = $this->table->getFilterValues();
        $list = \Usr\Db\Group::getMapper()->findAll($this->table->getDbTool());
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
<div class="Group_Manager">
  <div var="Table"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}

/**
 * String
 *
 * @package Usr\Module\Admin\Group
 */
class String extends \Table\Cell\String
{
    /**
     * Create a new cell
     *
     * @param $property
     * @return \Usr\Module\Admin\Group\String
     */
    static function create($property)
    {
        $obj = new self($property);
        return $obj;
    }

    public function getTd($placement)
    {
        $str = parent::getTd($placement);
        if ($placement->id > 1) {
            return $str;
        }
        return htmlentities($this->getPropertyValue($placement));
    }

}

/**
 * Permissions
 *
 * @package Usr\Module\Admin\Group
 */
class Permissions extends \Table\Cell\Iface
{

    public function getPropertyValue($obj)
    {
        $list = $obj->getPermissionList();
        $arr = array_keys($list);
        return implode(', ', $arr);
    }

    public function setOrderProperty($orderProperty)
    {
        return null;
    }
}