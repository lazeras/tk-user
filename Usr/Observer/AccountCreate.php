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
class AccountCreate extends \Tk\Object implements \Tk\Observer
{

    /**
     * execute
     *
     * @param \Tk\Auth $obs
     */
    public function update($obs)
    {
        $config = $this->getConfig();
        $template = $obs->get('tplAccountCreate');
        if (!$template) {
            $template = \Tk\Mail\Message::createHtmlTemplate($this->getDefaultTemplate());
        }

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
        $subject = sprintf($siteTitle . ' - New Account Registration');
        $message->setSubject($subject);

        $message->set('userId', $user->id);
        $message->set('username', $user->username);
        $message->set('userEmail', $user->email);
        $message->set('siteEmail', $siteEmail);
        $message->set('publicName', $user->username);
        $message->set('siteTitle', $siteTitle);
        $url = \Tk\Url::create('/register.html')->set('activate', $user->hash)->toString();
        $message->set('validateUrl', $url);

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
    Thank you for registering with {siteTitle}. You registered username is `{username}`.
  </p>
  <p>
    To complete the registration process, simply click on the link below (valid for 7 days).
  </p>
  <p>
    <a href="{validateUrl}">{validateUrl}</a>
  </p>
  <p>
    <small>
      If the above link does not work, please copy and paste it into your browser address bar.
    </small>
  </p>
</div>
TPL;
        return $tpl;
    }

}