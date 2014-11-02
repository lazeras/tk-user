<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Module;

/**
 * The base login module.
 *
 * *
 * @improvements: Implement a 3 try system then throw up an image validator
 *        then if they get it wrong another 5 times then ban IP for
 *        a 1 hour (configurable) duration before they can try again.
 *
 * @package Usr/Module
 */
class Login extends \Mod\Module
{
    /**
     * @var \Form\Form
     */
    protected $form = null;

    protected $showRemember = true;

    /**
     *
     *
     */
    public function __construct()
    {
        $this->setSecure(true);
        $this->setInsertMethod(self::INS_REPLACE);
    }

    /**
     * Get the login event
     *
     * @param string $name
     * @return \Usr\Module\LoginEvent
     */
    protected function getLoginEvent($name)
    {
        return new LoginEvent($name);
    }


    /**
     * Init the object
     */
    public function init()
    {
        $ff = \Form\Factory::getInstance();

        $this->form = $ff->createForm('loginForm');
        $this->form->attach($this->getLoginEvent('login'));

        $list = $this->getConfig()->get('system.auth.loginAdapters');
        if (count($list) > 1 && $this->getConfig()->get('system.user.loginForm.enableAdapters')) {
            $this->form->addField($ff->createFieldSelect('adapter', array_keys($list)));
        }

        $this->form->addField($ff->createFieldText('username'));
        $this->form->addField($ff->createFieldPassword('password'))->setAutocomplete(false);

        if ($this->showRemember) {
            $this->form->addField($ff->createFieldCheckbox('remember'));
        }

        $extraHtml = '';
        if ($this->getConfig()->get('system.user.loginForm.enableRegister')) {
            $extraHtml .= '<a href="/register.html" class="register">Create Account</a>';
        }
        if ($this->getConfig()->get('system.user.loginForm.enablePwdRecovery')) {
            $extraHtml .= '<a href="/recover.html" class="recover">Reset Password</a>';
        }
        if ($extraHtml) {
            $this->form->addField($ff->createFieldRenderer('extra', '<div class="extra">'.$extraHtml.'</div>'))->setLabel('');
        }




        if ($this->getConfig()->getUser() instanceof \Usr\Db\User) {
            \Tk\Url::create($this->getConfig()->getUser()->getGroup()->homeUrl)->redirect();
        }
        if ($this->getRequest()->cookieExists('tk_auth_cookie')) {
            \Tk\Log\Log::write('User cookie found: ' . $this->getRequest()->getCookie('tk_auth_cookie'));
            $auth = $this->getConfig()->getAuth();
            $userHash = $this->getRequest()->getCookie('tk_auth_cookie');
            $user = \Usr\Db\User::getMapper()->findByHash($userHash);
            if (!$user || !$user->active) {
                $this->getRequest()->deleteCookie('tk_auth_cookie');
                \Tk\Url::create('/login.html')->redirect();
            }
            $auth->getStorage()->write($user);
            \Tk\Url::create($this->getConfig()->getUser()->getGroup()->homeUrl)->redirect();
        }

        $this->addChild($this->getFormRenderer(), $this->form->getId());


    }
    
    
    /**
     * 
     * @return \Form\Renderer
     */
    public function getFormRenderer()
    {
       return  \Form\Factory::getInstance()->createFormRenderer($this->form);
    }
    
    

    /**
     * Execute the object
     */
    public function doDefault()
    {

    }

    /**
     * show
     */
    public function show()
    {
        $template = $this->getTemplate();
        if ($this->showRemember) {
            $template->setChoice('remember');
        }
        

    }



    /**
     * makeTemplate
     *
     * @return string
     */
    public function __makeTemplate()
    {
        $xmlStr = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<div class="row">
  <div class="col-sm-offset-3 col-sm-6 login-form">
    <h2 class="form-signin-heading">Login</h2>
    <div class="col-lg-12 clearfix">
      <div var="loginForm"></div>
      <p>&nbsp;</p>
    </div>
  </div>
</div>
XML;
        $template = \Mod\Dom\Loader::load($xmlStr, $this->getClassName());
        return $template;
    }
}

/**
 *
 */
class LoginEvent extends \Form\Event\Button
{

    /**
     * execute
     *
     * @param \Form\Form $form
     */
    public function update($form)
    {
        $list = $this->getConfig()->get('system.auth.loginAdapters');
        if (!count($list)) {
            throw new \Tk\Exception('No valid Authentication Adapters enabled.');
        }

        $auth = $this->getConfig()->getAuth();
        if ($form->getFieldValue('remember') === true) {
            $auth->set('remember', true);
        }

        $result = null;
        foreach($list as $name => $class) {
            /* @var $adapter \Tk\Auth\Adapter\Iface */
            $adapter = new $class($form->getFieldValue('username'), $form->getFieldValue('password'));
            $result = $auth->authenticate($adapter);
            if ($result->isValid()) {
                tklog('Logged in using: `' . $class . '`');
                break;
            }
        }
        if (!$result->isValid()) {
            $form->addFieldErrors($result->getMessages());
            return;
        }

        if ($this->getConfig()->getUser() instanceof \Usr\Db\User) {
            \Tk\Url::createHomeUrl()->redirect();
        } else if ($this->getConfig()->getUser() == 'admin') {
            \Tk\Url::create('/admin/index.html')->redirect();
        } else {
            \Tk\Url::create('/index.html')->redirect();
        }
    }

}