<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module;use Mod\AdminPageInterface;

/**
 *
 *
 * @package Usr\Module
 */
class ProfileEdit extends \Mod\Module
{

    /**
     * @var \Usr\Db\User
     */
    public $user = null;

    /**
     * @var \Form\Form
     */
    public $form = null;


    /**
     *
     *
     */
    public function __construct()
    {
        $this->setPageTitle('Edit Profile');
        $this->setSecure(true);

        $this->user = $this->getConfig()->getUser();
        
        $this->set(AdminPageInterface::CRUMBS_RESET, true);
        $this->add(AdminPageInterface::PANEL_ACTIONS_LINKS, \Mod\Menu\Item::create('Password', \Tk\Url::createHomeUrl('/password.html'), 'fa fa-key'));

    }

    /**
     * init
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('EditUser', $this->user);
        $ff->createDefaultEvents($this->form, $this->getConfig()->getHomeUrl());
        $this->form->attach(new EditEvent());


        $this->form->addField($ff->createFieldText('username'))->setReadonly();

        $this->form->addField($ff->createFieldText('email'))->setRequired();
        //$this->form->addField($ff->createFieldText('firstName'));
        //$this->form->addField($ff->createFieldText('lastName'));
        //$this->form->addField($ff->createFieldText('publicName'));
        //$this->form->addField($ff->createFieldText('companyName'));
        //$this->form->addField($ff->createFieldText('website'));

        $this->addChild($ff->createFormRenderer($this->form), $this->form->getId());

        $this->form->loadFromArray($this->user->getAllData());

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

        if ($this->getSession()->getOnce('resetDone')) {
            $t->setChoice('resetDone');
        }

        $url = $this->getUri()->set('reset', '1');
        $t->setAttr('reset', 'href', $url);
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
<div class="Admin_Edit">
  <div var="EditUser"></div>
</div>
HTML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}


/**
 *
 *
 * @package Usr\Module\User
 */
class EditEvent extends \Form\Event\Hidden
{

    /**
     * execute
     *
     * @param \Form $form
     */
    public function update($form)
    {
        /* @var $object \Usr\Db\User */
        $object = $form->getObject();

//        if (!preg_match('/.{0,64}/', $form->getFieldValue('companyName') )) {
//            $form->addFieldError('companyName', 'Invalid field value');
//        }
//        if ($form->getFieldValue('website') && !preg_match(\Tk\Validator::REG_URL, $form->getFieldValue('website') )) {
//            $form->addFieldError('website', 'Invalid website URL.(EG: http://example.com./)');
//        }

        if ($this->getForm()->hasErrors()) {
            return;
        }

        \Mod\Notice::addSuccess('Record successfully saved.' );
        $object->save();

//        $object->setData('firstName', $form->getFieldValue('firstName'));
//        $object->setData('lastName', $form->getFieldValue('lastName'));
//        $object->setData('companyName', $form->getFieldValue('companyName'));
//        $object->setData('website', $form->getFieldValue('website'));


    }

}
