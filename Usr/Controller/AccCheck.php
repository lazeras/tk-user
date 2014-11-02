<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Controller;

/**
 * This controller checks for an admin account if none found
 * It interrupts the system to prompt the user to enter the details
 * of an admin, good for first time installs.
 *
 * NOTE: Could be a security hole if someone can delete all admin accounts.
 * so use it wisely.
 *
 * @package Usr\Controller
 */
class AccCheck extends \Tk\Object implements \Tk\Controller\Iface
{

    /**
     *
     * @param \Tk\FrontController $obs
     */
    public function update($obs)
    {
        tklog($this->getClassName() . '::update()');

        if ($this->getAuth()->hasIdentity()) {
            return;
        }
        //
        $list = \Usr\Db\User::getMapper()->findAll(\Tk\Db\Tool::create('', 1));
        $path = $this->getUri()->getPath(true);
        if (!preg_match('/\/createAdmin\.(html|php)$/', $path) && !$list->count()) {
            \Tk\Url::create('/createAdmin.html')->redirect();
        }
    }

}