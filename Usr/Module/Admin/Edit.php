<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module\Admin;use Mod\AdminPageInterface;

/**
 *
 *
 * @package Usr\Module\Admin
 */
class Edit extends \Mod\Module
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
        $this->setPageTitle('Edit User Profile');

        $this->user = new \Usr\Db\User();
        if ($this->getRequest()->exists('userId')) {
            $this->user = \Usr\Db\User::getMapper()->find($this->getRequest()->get('userId'));
        }

        if ($this->user->id) {
            $itm = \Mod\Menu\Item::create('Password', \Tk\Url::createHomeUrl('/password.html')->set('userId', $this->user->id), 'fa fa-key');
            if ($this->getConfig()->get('system.auth.ldap.enable')) {
                $itm->setCssClass('disabled ldap');
            }
            $this->add(AdminPageInterface::PANEL_ACTIONS_LINKS, $itm);
            $this->add(AdminPageInterface::PANEL_ACTIONS_LINKS, \Mod\Menu\Item::create('User Data',
                        \Tk\Url::createHomeUrl('/data/edit.html')->set('userId', $this->user->id), 'fa fa-edit'));
        }
    }

    /**
     * init
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('EditUser', $this->user);
        $ff->createDefaultEvents($this->form, $this->getBackUrl());
        $this->form->attach(new EditEvent());

        $list = \Usr\Db\Group::getMapper()->findAll();
        $this->form->addField($ff->createFieldSelect('groupId', $list))->prependOption('-- Select --', '')->setRequired();

        $this->form->addField($ff->createFieldText('username'))->setRequired();
        $this->form->addField($ff->createFieldText('email'))->setRequired();
        $this->form->addField($ff->createFieldCheckbox('active'));

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

        if ($this->user->id) {
            $t->setChoice('update');
        } else {
            $t->setChoice('add');
        }

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
<div class="User_Edit">
  <div var="EditUser"></div>

  <div choice="update">
      <p>&nbsp;</p>
      <h3>User Persistant Data</h3>
      <module class="Usr_Module_Admin_Data_Manager"></module>
  </div>

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
class EditEvent extends \Form\Event\Hidden
{

    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        /* @var $object \Usr\Db\User */
        $object = $form->getObject();

        // INFO: Remember to validate user data
//        if (!preg_match('/.{0,64}/', $form->getFieldValue('companyName') )) {
//            $form->addFieldError('companyName', 'Invalid field value');
//        }
//        if ($form->getFieldValue('website') && !preg_match(\Tk\Validator::REG_URL, $form->getFieldValue('website') )) {
//            $form->addFieldError('website', 'Invalid website URL.(EG: http://example.com./)');
//        }

        if ($this->getForm()->hasErrors()) {
            return;
        }

        // If new user setup new password and email use password
        if ($object->id == 0) {
            $object->firstActive = \Tk\Date::create();
            $object->active = true;
            $pass = $object->changePassword();
            $object->save();

            $this->getAuth()->set('password', $pass);
            $this->getAuth()->set('user', $object);
            $this->getAuth()->notify('postActivateUser');

            \Mod\Notice::addSuccess('Account with the email `' . $object->email . '` successfully activated. Please check your email for the login details.' );
        } else {
            \Mod\Notice::addSuccess('Record successfully saved.' );
        }
        $object->save();

        // INFO: Set any user data here
//        $object->setData('firstName', $form->getFieldValue('firstName'));
//        $object->setData('lastName', $form->getFieldValue('lastName'));
//        $object->setData('companyName', $form->getFieldValue('companyName'));
//        $object->setData('website', $form->getFieldValue('website'));


    }

}
