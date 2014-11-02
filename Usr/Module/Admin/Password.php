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
class Password extends \Mod\Module
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
     */
    public function __construct()
    {
        $this->setPageTitle('Change Password');
        $this->user = \Usr\Db\User::getMapper()->find($this->getRequest()->get('userId'));
        if (!$this->user) {
            throw new \Tk\NullPointerException('User Not Found.');
        }
    }

    /**
     * init
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();

        //$this->form = $ff->createDefaultForm('EditUser', $this->user, \Tk\Url::createHomeUrl('/user/edit.html')->set('userId', $this->user->id));
        $this->form = $ff->createForm('EditUser', $this->user);
        $ff->createDefaultEvents($this->form, $this->getBackUrl());
        $this->form->attach(new EditEvent());

        if (!$this->getConfig()->getUser()->getGroup()->hasPermission('admin')) {
            $this->form->addField($ff->createFieldPassword('passwordCurr'))->setLabel('Current Password')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setNotes('Enter to add/change password');
        }
        $this->form->addField($ff->createFieldPassword('passwordMod'))->setLabel('New Password')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setNotes('Enter to add/change password');
        $this->form->addField($ff->createFieldPassword('passwordConf'))->setLabel('Confirm')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setNotes('Enter to confirm password');

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
<div class="tk-module User_Password">
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
        /* @var $user \Usr\Db\User */
        $user = $form->getObject();

        if (!$this->getConfig()->getUser()->getGroup()->hasPermission('admin')) {
            $pwd = $form->getFieldValue('passwordCurr');
            $pHash = \Tk\Auth\Auth::saltedHash($pwd, $user->salt);
            if ($user->password != $pHash) {
                $form->addFieldError('passwordCurr', 'Invalid current Password.');
                return;
            }
        }

        if ($form->getFieldValue('passwordMod') != $form->getFieldValue('passwordConf')) {
            $form->addFieldError('passwordMod', 'Passwords do not match, try again.');
        }
        if (!preg_match(\Tk\Validator::REG_PASSWORD, $form->getFieldValue('passwordMod'))) {
            $form->addFieldError('passwordMod', 'Invalid Password format. size must be 6-64 characters.');
        }

        if ($form->hasErrors()) {
            return;
        }

        $user->changePassword($form->getFieldValue('passwordMod'));
        $user->save();

        \Tk\Log\Log::write('Password manually updated for user ' . $user->username . ' ', \Tk\Log\Log::SYSTEM);
        \Mod\Notice::addSuccess('Password updated.');

    }

}
