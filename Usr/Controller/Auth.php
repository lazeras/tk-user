<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Controller;

/**
 * Use this controller to check if the currently logged in user
 * has permission to access the requested resource
 *
 * The permission to the resource reside in the \Mod\Page
 *
 *
 * @package Usr\Controller
 */
class Auth extends \Tk\Object implements \Tk\Controller\Iface
{

    /**
     * update
     *
     * @param \Tk\FrontController $obs
     */
    public function update($obs)
    {
        tklog('' . $this->getClassName() . '::update()');
        
        $permission = $this->getConfig()->get('res.system.permission');
        if (!$permission || $permission == 'public') {
            return;
        }

        // Get session user
        $user = $this->getConfig()->getUser();
        if ($user instanceof \Usr\Db\User) {
            /* @var $user \Usr\Db\User */
            tklog('Auth Check: U: ' . $user->username . ' - G: ' . $user->getGroup()->name . ' - ID: ' . $user->id);
            if (!$user->getGroup()->hasPermission($permission)) {
                $e = new \Tk\Exception('You do not have valid permissions to access this resource.');
                $e->setDump('U: ' . $user->username . ' - G: ' . $user->getGroup()->name . ' - ID: ' . $user->id.
                    "\nPermissions: " . implode( ', ', $user->getGroup()->getPermissionList()) ."\nRequired Permission: " . $permission.'.' );
                throw $e;
            }
            return;
        } else if (is_string($user)) {
            tklog('Auth Check: U: ' . $user . ' - P: ' . $permission);
            return;
        }
        // redirect all other invalid user requests to login page
        $this->getConfig()->getAuth()->clearIdentity();
        \Tk\Url::create('/login.html')->redirect();
        
    }

}