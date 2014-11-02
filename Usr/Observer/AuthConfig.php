<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Observer;

/**
 * Setup Auth observers
 * More observers can be added in other Packages
 * to attach extra functionality hooks
 *
 * @package Usr\Observer
 */
class AuthConfig implements \Tk\Observer
{
    public function update($obs)
    {
        $auth = $obs['res.auth'];
        $auth->attach(new Login(), 'postLogin');
        $auth->attach(new Logout(), 'preLogout');

        $auth->attach(new AccountCreate(), 'postCreateUser');
        $auth->attach(new AccountActivate(), 'postActivateUser');
        $auth->attach(new AccountRecover(), 'postRecoverUser');
    }
}