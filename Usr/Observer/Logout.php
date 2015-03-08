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
class Logout extends \Tk\Object implements \Tk\Observer
{

    /**
     * execute
     *
     * @param \Tk\Auth $obs
     */
    public function update($obs)
    {
        // @var $user \Usr\Db\User
        $user = $this->getConfig()->getUser();
        if ($this->getRequest()->cookieExists('tk_auth_cookie')) {
            $this->getRequest()->deleteCookie('tk_auth_cookie');
        }
        if ($this->getSession()->exists('system.auth.usingMasterKey')) {
            $this->getSession()->delete('system.auth.usingMasterKey');
        }
        if (!$user instanceof \Usr\Db\User) {
            return;
        }
        $user->cookie = '';
        $user->sessionId = '';
        $user->save();
    }


}