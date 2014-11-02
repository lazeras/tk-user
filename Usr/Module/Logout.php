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
class Logout extends \Mod\Module
{

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->setSecure(true);
    }


    /**
     * Init the object
     */
    public function init()
    {
        /* @var $auth \Tk\Auth\Auth */
        $auth = $this->getConfig()->getAuth();
        if ($auth->hasIdentity()) {
            $auth->clearIdentity();
        }
        \Tk\Url::create('/index.html')->setScheme('http')->redirect();
    }

}
