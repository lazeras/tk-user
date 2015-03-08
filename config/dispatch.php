<?php

$dispatcher = $config->getDispatcherStatic();


// USER
// Public Pages

$dispatcher->add('/register.html', '\Usr\Module\Register');
$dispatcher->add('/recover.html', '\Usr\Module\Recover');
$dispatcher->add('/createAdmin.html', '\Usr\Module\CreateAdmin');
// User Self Management
$dispatcher->add('/user/profile.html', '\Usr\Module\ProfileEdit');
$dispatcher->add('/user/password.html', '\Usr\Module\Password');


// ADMIN
// Self User Management
$dispatcher->add('/admin/profile.html', '\Usr\Module\ProfileEdit');
$dispatcher->add('/admin/password.html', '\Usr\Module\Password');
// User Management
$dispatcher->add('/admin/user/manager.html', '\Usr\Module\Admin\Manager');
$dispatcher->add('/admin/user/edit.html', '\Usr\Module\Admin\Edit');
$dispatcher->add('/admin/user/password.html', '\Usr\Module\Admin\Password');
$dispatcher->add('/admin/user/group/manager.html', '\Usr\Module\Admin\Group\Manager');
$dispatcher->add('/admin/user/group/edit.html', '\Usr\Module\Admin\Group\Edit');
$dispatcher->add('/admin/user/data/edit.html', '\Usr\Module\Admin\Data\Edit');

