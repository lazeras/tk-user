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
class AccountRecover extends \Tk\Object implements \Tk\Observer
{

    /**
     * execute
     *
     * @param \Tk\Auth $obs
     */
    public function update($obs)
    {
        $config = $this->getConfig();

        $template = $obs->get('tplAccountRecover');
        if (!$template) {
            $template = \Tk\Mail\Message::createHtmlTemplate($this->getDefaultTemplate());
        }

        $pwd = $obs->get('password');
        /* @var $user \Usr\Db\User */
        $user = $config->getUser();

        if (!$user instanceof \Usr\Db\User) {
            return;
        }

        $siteEmail = $config->get('system.site.email');
        $siteTitle = $config->get('system.site.title');

        $message = new \Tk\Mail\TplMessage($template);

        $message->setFrom($siteEmail);
        $message->addTo($user->email);
        $subject = sprintf($siteTitle . ' - Account Password Recovery');
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
    Here is your access details for {siteTitle}.
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