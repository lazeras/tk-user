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
class Recover extends \Mod\Module
{

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
        $this->setPageTitle('Reset Password');
        $this->setSecure(true);
    }


    /**
     * init
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();
        $this->form = $ff->createForm('loginForm');

        $this->form->attach(new RecoverEvent('reset'));
        $this->form->addField($ff->createFieldText('username'))->setRequired()->setLabel('Username / Email');

        $extraHtml = '<a href="/login.html" class="recover">Back To Login</a>';
        $this->form->addField($ff->createFieldRenderer('extra', '<div class="extra">'.$extraHtml.'</div>'))->setLabel('');


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
<div class="row">
  <div class="login-form">
    <h2 class="form-signin-heading">Reset Password</h2>
    <div class="col-lg-12">
      <div var="loginForm"></div>
    </div>
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
 * @package Usr\Module
 */
class RecoverEvent extends \Form\Event\Button
{

    /**
     * execute
     *
     * @param Form $form
     */
    public function update($form)
    {
        /* @var $user \Usr\Db\User */
        $user = \Usr\Db\User::getMapper()->findByUsername($form->getFieldValue('username'));
        if (!$user) {
            $user = \Usr\Db\User::getMapper()->findByEmail($form->getFieldValue('username'));
            if (!$user) {
                $form->addFieldError('username', 'User not found');
            }
        }

        if ($this->getForm()->hasErrors()) {
            return;
        }

        $pwd = $user->changePassword();
        $user->save();

        $this->getAuth()->set('password', $pwd);
        $this->getAuth()->set('user', $user);
        $this->getAuth()->notify('postRecoverUser');

        \Mod\Notice::addSuccess('Your password has been reset and emailed to your account.');
        $form->setRedirectUrl(\Tk\Url::create('/login.html'));
    }

}
