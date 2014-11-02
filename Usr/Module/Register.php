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
class Register extends \Mod\Module
{
    /**
     * @var \Usr\Db\User
     */
    public $user = null;

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
        $this->setPageTitle('Register New Account');
        $this->setSecure(true);
        $this->addEvent('activate', 'doActivate');

        if (!$this->getConfig()->get('system.user.enableRegister')) {
            \Tk\Url::create('/index.html')->redirect();
        }

        // Clear any logged out user.
        if ($this->getConfig()->getUser()) {
            $this->getAuth()->clearIdentity();
        }

        $this->user = new \Usr\Db\User();
        $this->user->groupId = $this->getConfig()->get('site.user.registeredGroupId');
    }


    /**
     * init
     */
    public function init()
    {

        $ff = \Form\Factory::getInstance();
        $this->form = $ff->createForm('loginForm', $this->user);
        $this->form->attach(new EditEvent('register'));
        $this->form->attach($ff->createEventLink('cancel'), 'cancel')->setRedirectUrl(\Tk\Url::create('/index.html'));

        $this->form->addField($ff->createFieldText('publicName'))->setRequired()->setLabel('Name');
        $this->form->addField($ff->createFieldText('username'))->setRequired();
        $this->form->addField($ff->createFieldText('email'))->setRequired();

        $this->addChild($ff->createFormRenderer($this->form), $this->form->getId());
    }

    /**
     * execute
     */
    public function doDefault()
    {

    }

    public function doActivate()
    {
        $hash = $this->getRequest()->get('activate');
        $user = \Usr\Db\User::getMapper()->findByHash($hash);
        if ($user) {
            if (!$user->active) {
                $pass = $user->changePassword();
                $user->active = true;
                $user->firstActive = \Tk\Date::create();
                $user->save();
                $this->getAuth()->set('password', $pass);
                $this->getAuth()->set('user', $user);
                $this->getAuth()->notify('postActivateUser');
                \Mod\Notice::addSuccess('Account with the email `' . $user->email . '` successfully activated. Please check your email for the login details.' );
            } else {
                \Mod\Notice::addWarning('This account is already activated.');
            }
            \Tk\Url::create('/login.html')->redirect();
        } else {
            \Mod\Notice::addError('Account not found!');
        }

        $this->getUri()->delete('activate')->redirect();
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
    <h2 class="form-signin-heading">Registration</h2>
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
 * Form Event
 *
 * @package Usr/Module
 */
class EditEvent extends \Form\Event\Button
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
        $form->loadObject($user);

        $user->username = $form->getFieldValue('username');
        // TODO: clean/validate username

        $form->addFieldErrors($user->getValidator()->getErrors());
        if ($this->getForm()->hasErrors()) {
            return;
        }

        $user->active = false;
        if (!$user->firstActive) {
            $user->firstActive = \Tk\Date::create();
        }
        $user->save();

        $auth = $this->getAuth();
        $auth->set('user', $user);
        $auth->notify('postCreateUser');

        \Mod\Notice::addSuccess('Account successfully created. An email has been sent, please follow the activation link to activate your account so you can login to the site.');

        \Tk\Url::create('/login.html')->redirect();
    }

}
