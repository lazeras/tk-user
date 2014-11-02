<?php
/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Db;


/**
 *
 *
 * @package Usr/Db
 */
class User extends \Tk\Db\Model
{

    public $id = '';
    public $groupId = 2;
    public $username = '';
    public $password = '';
    public $email = '';
    public $avatar = '';
    public $timezone = '';
    public $notes = null;

    public $active = false;
    public $sessionId = '';      // if set then user online
    public $cookie = '';
    public $ip = '';
    public $hash = '';           // For registration etc
    public $lastLogin = null;
    public $firstActive = null;
    public $modified = null;
    public $created = null;

    /**
     * @var string
     * @deprecated
     */
    public $publicName = '';



    /**
     * @var Group
     */
    private $group = null;



    /**
     * __construct
     *
     */
    function __construct()
    {
        $this->modified = \Tk\Date::create();
        $this->created = \Tk\Date::create();
        $this->timezone = $this->getConfig()->get('system.timezone');
    }



    public function delete()
    {
        return parent::delete();
    }

    public function save()
    {
        if (!$this->firstActive) {
            $this->firstActive = \Tk\Date::create();
        }
        $this->hash = md5($this->id . $this->username . '=-=');

        $u = $this->getMapper()->find($this->id);

        $r = parent::save();

        return $r;
    }

    /**
     * Get the role object
     *
     * @return \Usr\Db\Group
     */
    public function getGroup()
    {
        if (!$this->group) {
            $this->group = Group::getMapper()->find($this->groupId);
        }
        return $this->group;
    }

    /**
     * Test to see if a user has access to a permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->getGroup()->hasPermission($permission);
    }

    /**
     * returns an array of permission strings
     *  array(
     *    'admin',
     *    'public'
     *  );
     *
     * @return array
     */
    public function getPermissions()
    {
        return array_keys($this->getGroup()->getPermissionList());
    }

    /**
     * Get the home path of this user
     *
     * @return string
     */
    public function getHomePath()
    {
        $dir = dirname($this->getGroup()->homeUrl);
        if ($dir == '/') $dir = '';
        return $dir;
    }

    /**
     * Get the home url of this user
     *
     * @return string
     */
    public function getHomeUrl()
    {
        return $this->getGroup()->homeUrl;
    }

    /**
     * Return the users avatar image url
     *
     * @param int $size Return the default avitar icon at this size. Default: 64
     * @return \Tk\Url
     */
    public function getAvatarUrl()
    {
       if (!$this->avatar || !is_file($this->getConfig()->getDataPath() . $this->avatar)) {
           $this->avatar = '/User/avatar/' . $this->id . '.png';
           $path = $this->getConfig()->getDataPath() . $this->avatar;
           if (!is_dir(dirname($path))) {
               mkdir(dirname($path), 0777, true);
           }
           //tklog($this->hash);
           \Tk\Identicon::create()->makeIcon($this->hash, 256, $path);
           $this->save();
       }
       return \Tk\Url::createDataUrl($this->avatar);
    }

    /**
     * Is the user currently logged in.
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->sessionId != '';
    }

    public function setData($key, $value)
    {
        return $this->getMapper()->setData($this->id, $key, $value);
    }

    public function getData($key)
    {
        return $this->getMapper()->getData($this->id, $key);
    }

    public function deleteData($key)
    {
        return $this->getMapper()->deleteData($this->id, $key);
    }

    public function getAllData()
    {
        return $this->getMapper()->getAllData($this->id);
    }

    public function deleteAllData()
    {
        return $this->getMapper()->deleteAllData($this->id);
    }

    /**
     * Resets the password and salt and returns the password string
     * Remember to save the user record after calling this method.
     *
     * @param null $pwd
     * @return string
     */
    public function changePassword($pwd = null)
    {
        if (!$pwd) {
            $pwd = $this->getConfig()->getAuth()->createPassword(10);
            if ($this->getConfig()->isDebug()) {
                $pwd = 'password';
            }
        }
        $phash = $this->getConfig()->getAuth()->hash($pwd);
        $this->password = $phash;
        return $pwd;
    }


    /**
     *
     *
     * @return string
     */
    public function toString()
    {
        return $this->username;
    }

}


/**
 *
 *
 * @package Usr\Db
 */
class UserValidator extends \Tk\Validator
{

    public function validate()
    {
        if (!preg_match(self::REG_USERNAME, $this->obj->username)) {
            $this->addError('username', 'Invalid characters used in username');
        }
        if (!preg_match(self::REG_EMAIL, $this->obj->email)) {
            $this->addError('email', 'Invalid email format');
        }

        $chk = User::getMapper()->findByUsername($this->obj->username);
        if ($chk) {
            if ($this->obj->id == 0) {
                $this->addError('username', 'A user already exists with selected username.');
            } else {
                if ($this->obj->id != $chk->id) {
                    $this->addError('username', 'A user already exists with selected username.');
                }
            }
        }

        $chk = User::getMapper()->findByEmail($this->obj->email);
        if ($chk) {
            if ($this->obj->id == 0) {
                $this->addError('email', 'A user already exists with selected email.');
            } else {
                if ($this->obj->id != $chk->id) {
                    $this->addError('email', 'A user already exists with selected email.');
                }
            }
        }

    }

}