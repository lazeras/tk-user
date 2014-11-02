<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module;

/**
 *
 *
 * @package Usr
 */
class CreateAdmin extends \Mod\Module
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
     * __construct
     *
     */
    public function __construct()
    {
        $this->setSecure(true);

        // Check if an admin account exists.
        $list = \Usr\Db\User::getMapper()->findAll(\Tk\Db\Tool::create('', 1));
        if ($list->count() > 0) {
            \Tk\Url::create('/index.html')->redirect();
        }

        // Create new User object
        $this->user = new \Usr\Db\User();
        $this->user->groupId = 1;
        $this->user->username = 'admin';
    }


    /**
     * Init the object
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();
        $this->form = $ff->createForm('User', $this->user);
        $this->form->attach(new RecoverEvent('create'));

        $this->form->addField($ff->createFieldText('username'))->setAutocomplete(false)->setRequired()->setReadonly();
        $this->form->addField($ff->createFieldText('email'))->setAutocomplete(false)->setRequired()->setNotes('A valid email address');

        $this->form->addField($ff->createFieldPassword('newPassword'))->setRequired();
        $this->form->addField($ff->createFieldPassword('confPassword'))->setRequired()->setLabel('Confirm');

        $this->addChild($ff->createFormRenderer($this->form), $this->form->getId());

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
<div>
  <h2>Admin Account Creation</h2>
  <p>
    Since this is the first time running your new site you will need to create an Administrator account.<br/>
    This account will give you access to the backend so you can manage and maintain your site.
  </p>
  <div var="User"></div>
</div>
HTML;

        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }

}

/**
 *
 *
 * @package Usr/Module
 */
class RecoverEvent extends Form\Event\Button
{

    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        /* @var $user Usr\Db\User */
        $user = $form->getObject();

        $form->loadObject($user);
        $form->addFieldErrors($user->getValidator()->getErrors());

        if (!preg_match(Tk\Validator::REG_PASSWORD, $form->getFieldValue('newPassword'))) {
            $form->addFieldError('newPassword', 'Invalid Password format. size must be 6-64 characters.');
        } else {
            if ($form->getFieldValue('newPassword') != $form->getFieldValue('confPassword')) {
                $form->addFieldError('newPassword', 'Passwords do not match, try again.');
            }
        }

        if ($form->hasErrors()) {
            Mod\Notice::addError('The form contains errors.');
            return;
        }

        $pwd = $form->getFieldValue('newPassword');
        $user->changePassword($pwd);
        $user->username = 'admin';
        $user->active = true;
        $user->firstActive = Tk\Date::create();
        $user->save();

        $this->getAuth()->notify('preLogin');
        $this->getAuth()->getStorage()->write($user);
        $this->getAuth()->notify('postLogin');

        Mod\Notice::addSuccess('Welcome to your new website Administration Dashboard.', 'Welcome');
        Tk\Url::create('/admin/index.html')->redirect();
    }

}

