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
 * @package Usr\Module
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
        $this->setPageTitle('Set Password');
        $this->setSecure(true);
        $this->user = $this->getConfig()->getUser();
    }

    /**
     * init
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('EditUser', $this->user);

        $this->form->attach(new EditEvent('save'));
        $this->form->attach($ff->createEventLink('cancel'), 'cancel')->setRedirectUrl($this->getConfig()->getHomeUrl());

        $this->form->addField($ff->createFieldPassword('passwordCurr'))->setLabel('Current Password')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setPlaceholder('Enter current password');
        $this->form->addField($ff->createFieldPassword('passwordMod'))->setLabel('New Password')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setPlaceholder('Enter new password');
        $this->form->addField($ff->createFieldPassword('passwordConf'))->setLabel('Confirm')->setRequired(true)->setFieldset('Change Password')->setAutocomplete(false)->setPlaceholder('Confirm new password');

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
 * @package Usr\Module\User
 */
class EditEvent extends \Form\Event\Button
{

    /**
     * execute
     *
     * @param \Form\Form $form
     */
    public function update($form)
    {
        /* @var $user \Usr\Db\User */
        $user = $form->getObject();

        $pwd = $form->getFieldValue('passwordCurr');
        $pHash = $this->getAuth()->hash($pwd);

        if ($user->password != $pHash) {
            $form->addFieldError('passwordCurr', 'Invalid current Password.');
            return;
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

        $form->setRedirectUrl($user->getConfig()->getHomeUrl());
    }

}
