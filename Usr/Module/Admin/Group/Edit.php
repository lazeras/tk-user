<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module\Admin\Group;

/**
 *
 *
 * @package Usr\Module\Admin\Group
 */
class Edit extends \Mod\Module
{
    /**
     * @var \Usr\Db\Group
     */
    public $group = null;

    /**
     * @var Form
     */
    public $form = null;



    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->setPageTitle('Edit Group');
    }

    /**
     * init
     */
    public function init()
    {

        $this->group = new \Usr\Db\Group();
        if ($this->getRequest()->exists('groupId')){
            $this->group = \Usr\Db\Group::getMapper()->find($this->getRequest()->get('groupId'));
        }

        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('Role', $this->group);
        $ff->createDefaultEvents($this->form, $this->getBackUrl());
        $this->form->attach(new GroupEvent());

        $this->form->addField($ff->createFieldText('name'));

        // TODO: Get this list from groups table with a groupBy query....
//        $list = array(array('-- Select --', ''), array('admin', 'admin'), array('public', 'public'));
//        $this->form->addField($ff->createFieldSelect('area', $list))->setRequired()->setNotes('This field determins what theme path the group will use.');

        $list = \Usr\Db\Permission::getMapper()->findAll();
        $this->form->addField($ff->createFieldCheckboxGroup('permissions', $list));

//        foreach ($this->allPerms as $name => $o) {
//            $this->form->addField($ff->createFieldCheckbox($name))->setValue(false);
//        }

        $this->addChild($ff->createFormRenderer($this->form), $this->form->getId());


    }

    /**
     * execute
     */
    public function doDefault()
    {
        $permList = $this->group->getPermissionList();
        if (!$this->form->isSubmitted()) {
            $arr = array();
            foreach ($permList as $obj) {
                $arr[] = $obj->id;
            }
            $this->form->setFieldValue('permissions', $arr);
        }

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
<div class="Group_Edit">
  <div var="Role"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}

/**
 *
 *
 * @package Usr\Module\Admin\Group
 */
class GroupEvent extends \Form\Event\Hidden
{


    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        /* @var $object \Usr\Db\Group */
        $object = $form->getObject();

        if (!$object->getValidator()->isValid()) {
            $this->form->addFieldErrors($object->getValidator()->getErrors());
        }
        if ($this->getForm()->hasErrors()) {
            return;
        }

        $object->save();
        \Usr\Db\Group::getMapper()->deleteGroupPermissions($object->id);
        foreach ($form->getFieldValue('permissions') as $id) {
            \Usr\Db\Group::getMapper()->addPermission($object->id, $id);
        }

    }

}
