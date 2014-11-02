<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module\Admin\Data;

/**
 *
 *
 * @package Usr\Module\Admin\Group
 */
class Manager extends \Mod\Module
{

    /**
     * @var \Table
     */
    protected $table = null;


    /**
     * @var \Usr\Db\User
     */
    protected $user = null;


    /**
     * __construct
     */
    public function __construct()
    {
        $this->user = \Usr\Db\User::getMapper()->find($this->getRequest()->get('userId'));

        if (!$this->user) {
            $this->enabled(false);
        }


    }


    /**
     * init
     */
    public function init()
    {
        if ($this->isContentModule()) {
            $this->setPageTitle('User Data Manager');
        }
        if (!$this->user) {
            return;
        }

        // Create Table structure
        $ff = \Form\Factory::getInstance();
        $tf = \Table\Factory::getInstance();

        $this->table = $tf->createTable('DataManager');
        $this->table->addCell($tf->createCellCheckbox());
        //$this->table->addCell($tf->createCellInteger('id'));
        $this->table->addCell($tf->createCellString('key'));
        $this->table->addCell($tf->createEditCellString('value'))->setKey()->setUrl(\Tk\Url::createHomeUrl('/user/data/edit.html')->set('userId', $this->user->getId()));

        $this->table->addAction($tf->createActionDelete());

        $this->addChild($tf->createTableRenderer($this->table), 'Table1');

    }

    /**
     * execute
     */
    public function doDefault()
    {
        if (!$this->user) {
            return;
        }
        $filter = $this->table->getFilterValues();
        $filter['userId'] = $this->user->getId();
        $list = \Usr\Db\Data::getMapper()->findFiltered($filter, $this->table->getDbTool());
        $this->table->setList($list);
    }

    /**
     * show
     */
    public function show()
    {
        if (!$this->user) {
            return;
        }
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
<div class="Data_Manager">
  <div var="Table1"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}
