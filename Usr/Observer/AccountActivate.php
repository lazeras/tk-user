<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Observer;

/**
 *
 * @package Usr\Observer
 */
class AccountActivate extends \Tk\Object implements \Tk\Observer
{

    /**
     * execute
     *
     * @param \Tk\Auth $obs
     */
    public function update($obs)
    {
        $config = $this->getConfig();
        $template = $obs->get('tplAccountActivate');
        if (!$template) {
            $template = \Tk\Mail\Message::createHtmlTemplate($this->getDefaultTemplate());
        }
        $pwd = $obs->get('password');
        /* @var $user \Usr\Db\User */
        $user = $this->getUser();
        if (!$user instanceof \Usr\Db\User) {
            return;
        }

        $siteEmail = $config->get('system.site.email');
        $siteTitle = $config->get('system.site.title');

        $message = new \Tk\Mail\TplMessage($template);

        $message->setFrom($siteEmail);
        $message->addTo($user->email);
        $subject = sprintf($siteTitle . ' - New Account Activated');
        $message->setSubject($subject);

        $message->set('publicName', $user->username);
        $message->set('siteTitle', $siteTitle);
        $message->set('username', $user->username);
        $message->set('password', $pwd);
        $login = \Tk\Url::create('/login.html')->toString();
        $message->set('loginUrl', $login);
        $message->set('userId', $user->id);
        $message->set('userEmail', $user->email);

        if (!$this->getRequest()->getReferer()) {
            $config->set('mail.validReferrer', false);
        }
        $message->send();

        $dump = 'User called ' . $user->username . ' with the ID ' . $user->getId() . ' and belonging to the Role ' . $user->getGroup()->name . ' has been activated.';

        \Tk\Log\Log::write('A user has been activated - ' . $user->username . ' [' . $user->getGroup()->name . ']', \Tk\Log\Log::MESSAGE, array('dump', $dump));

    }

    /**
     * Get the default mail template
     *
     * @return string
     */
    function getDefaultTemplate()
    {
        $tpl = <<<TPL
<div>
  <p>Welcome {username}</p>
  <p>
    Thank you for activating your account with {siteTitle}.
  </p>
  <p>Account Login Details:</p>
  <blockquote>
    <b>Acc ID:</b> {userId}<br/>
    <b>Username:</b> {username}<br/>
    <b>Password:</b> {password}
  </blockquote>
  <p>
    You can now login an access your account at <a href="{loginUrl}">{loginUrl}</a>
  </p>
</div>
TPL;
        return $tpl;
    }


}