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
 * @package Usr\Module\Admin\Data
 */
class Edit extends \Mod\Module
{
    /**
     * @var \Usr\Db\User
     */
    public $user = null;
    /**
     * @var \Usr\Db\Data
     */
    public $data = null;

    /**
     * @var Form
     */
    public $form = null;


    /**
     *
     *
     */
    public function __construct()
    {
        $this->setPageTitle('Edit User Data');

        $this->user = \Usr\Db\User::getMapper()->find($this->getRequest()->get('userId'));
        if (!$this->user) {
            throw new \Tk\NullPointerException('User Not Found!');
        }
        $this->data = new \Usr\Db\Data();
        $this->data->userId = $this->user->getId();
        if ($this->getRequest()->exists('dataId')) {
            $this->data = \Usr\Db\Data::getMapper()->find($this->getRequest()->get('dataId'));
        }
    }

    /**
     * init
     */
    public function init()
    {

        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('EditUserData', $this->data);
        $ff->createDefaultEvents($this->form, $this->getBackUrl());

        //$this->form->attach(new EditEvent());
        $this->form->addField($ff->createFieldText('key'))->setRequired();
        $this->form->addField($ff->createFieldTextarea('value'))->setRequired();

        $this->addChild($ff->createFormRenderer($this->form), $this->form->getId());

    }

    /**
     * execute
     */
    public function doDefault()
    {
    }

    /**
     * show
     */
    public function show()
    {
        $t = $this->getTemplate();

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
<div class="Data_Edit">
  <div var="EditUserData"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}

