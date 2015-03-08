<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */

// Setup Default Config settings
$config = \Tk\Config::getInstance();
$config->attach(new \Usr\Observer\AuthConfig(), 'res.auth');

// Set the default group a new registered user will
// be given. This number corosponds to a record in the `userGroups` table.
$config['system.user.defaultGroupId'] = 2;

// Enable public users to register on the system.
// setting this to false removes the registration form.
// Also removes the link to it from the login form.
$config['system.user.enableRegister'] = true;

// Enable the recovery of password link and page on the login url
//
$config['system.user.enablePwdRecovery'] = true;

// If set this html will be placed under the username and password
// fields. Useful to add external registration and recovery information and links.
$config['system.user.loginHtml'] = '';

// This is the default login url
$config['system.auth.loginUrl'] = '/login.html';
// This is the default logout url
$config['system.auth.logoutUrl'] = '/logout.html';

// Set a user class here for the login system EG: \Usr\Db\User
$config['system.auth.userClass'] = '';

/*
 * LDAP Config Options
 */
//$config['system.auth.ldap.enable'] = true;
//$config['system.auth.ldap.uri']    = 'ldap://ldap.example.com';
//$config['system.auth.ldap.port']   = 389;
//$config['system.auth.ldap.baseDn'] = 'ou=people,o=example';
//$config['system.auth.ldap.filter'] = 'uid=%s';

$config['system.auth.loginAdapters'] = array(
    'Config' => '\Tk\Auth\Adapter\Config'
    ,'Trap' => '\Tk\Auth\Adapter\Trapdoor'
    //,'Digest' => '\Tk\Auth\Adapter\Digest'
    //,'Ldap' => '\Tk\Auth\Adapter\Ldap'
);

$config['system.user.loginForm.enableAdapters'] = false;
$config['system.user.loginForm.enableRegister'] = true;
$config['system.user.loginForm.enablePwdRecovery'] = true;


