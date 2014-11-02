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
class Login extends \Tk\Object implements \Tk\Observer
{

    /**
     * execute
     *
     * @param \Tk\Auth\Auth $obs
     */
    public function update($obs)
    {
        $remember = $obs->get('remember');
        /* @var $user \Usr\Db\User */
        $user = $this->getConfig()->getUser();
        if ($user instanceof \Usr\Db\User) {
            $user->getMapper()->clean();
            $user->ip = $this->getRequest()->getRemoteAddr();
            $user->sessionId = $this->getSession()->getId();
            $user->lastLogin = \Tk\Date::create();
            if ($remember === true) {
                $user->cookie = md5(time());
                $this->getRequest()->setCookie('tk_auth_cookie', $user->cookie);
            }
            $user->save();
        }


    }


}