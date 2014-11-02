<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Db;

/**
 *
 *
 * @package Usr\Db
 */
class UserMap extends \Tk\Db\Mapper
{

    /**
     * Create the data map
     *
     * @return \Tk\Model\DataMap
     */
    protected function makeDataMap()
    {
        $dataMap = new \Tk\Model\DataMap(__CLASS__);
        $this->setTable('user');
        $this->setMarkDeleted('del');

        $dataMap->addIdProperty(\Tk\Model\Map\Integer::create('id'));
        $dataMap->addProperty(\Tk\Model\Map\Integer::create('groupId'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('username'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('password'));

        $dataMap->addProperty(\Tk\Model\Map\String::create('email'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('avatar'));

        $dataMap->addProperty(\Tk\Model\Map\Boolean::create('active'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('sessionId'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('cookie'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('ip'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('hash'));
        $dataMap->addProperty(\Tk\Model\Map\Date::create('lastLogin'));
        $dataMap->addProperty(\Tk\Model\Map\Date::create('firstActive'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('timezone'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('notes'));

        $dataMap->addProperty(\Tk\Model\Map\Date::create('modified'));
        $dataMap->addProperty(\Tk\Model\Map\Date::create('created'));

        return $dataMap;
    }





    /**
     * Find a user object by username and password
     *
     * @param string $username
     * @return \Usr\Db\User
     */
    public function findForAuth($username)
    {
        $where = sprintf('`username` = %s AND `active` = 1 ', $this->getDb()->quote($username));
        if (preg_match(\Tk\Validator::REG_EMAIL, $username)) {
            $where = sprintf('`email` = %s AND `active` = 1 ', $this->getDb()->quote($username));
        }
        return $this->selectMany($where)->current();
    }

    /**
     * Find a user object by username and password
     *
     * @param string $username
     * @param string $password
     * @return \Usr\Db\User
     */
    public function findUser($username, $password)
    {
        $where = sprintf('`username` = %s AND `password` = %s ', $this->getDb()->quote($username), $this->getDb()->quote($password));
        return $this->selectMany($where)->current();
    }

    /**
     * Find a user object by username and password
     *
     * @param string $username
     * @return \Usr\Db\User
     */
    public function findByUsername($username)
    {
        $where = sprintf('`username` = %s ', $this->getDb()->quote($username));
        return $this->selectMany($where)->current();
    }

    /**
     * findForAutocomplete
     *
     * @param string $name
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findForAutocomplete($name = '', $tool = null)
    {
        $where = '`username` LIKE ' . $this->getDb()->quote($name . '%');
        return $this->selectMany($where);
    }

    /**
     * Find a user object by username and password
     *
     * @param string $email
     * @return \Usr\Db\User
     */
    public function findByEmail($email)
    {
        $where = sprintf('`username` = %s ', $this->getDb()->quote($email));
        return $this->selectMany($where)->current();
    }

    /**
     * Find a user object by its hash
     *
     * @param string $hash
     * @return \Usr\Db\User
     */
    public function findByHash($hash)
    {
        $where = sprintf('`hash` = %s ', $this->getDb()->quote($hash));
        return $this->selectMany($where)->current();
    }

    /**
     * Find a user object by its cookie hash
     *
     * @param string $hash
     * @return \Usr\Db\User
     */
    public function findByCookie($hash)
    {
        $where = sprintf('`cookie` = %s AND `active`', $this->getDb()->quote($hash));
        return $this->selectMany($where)->current();
    }

    /**
     * Find online users
     *
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findOnline($tool = null)
    {
        $where = sprintf('`sessionId` != \'\' ');
        return $this->selectMany($where, $tool);
    }

    /**
     * Find online users
     *
     * @param \Tk\Db\Tool $tool
     * @return bool
     */
    public function isOnline($username)
    {
        $where = sprintf('`username` = %s AND `sessionId` != \'\' ', $this->getDb()->quote($username));
        $usr = $this->selectMany($where)->current();
        if ($usr) {
            return $usr;
        }
        return false;
    }

    /**
     * Find a active users
     *
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findActive($tool = null)
    {
        $where = sprintf('`active` = 1 ');
        return $this->selectMany($where, $tool);
    }

    /**
     * Get all active users with a permission
     *
     * @param string $permission
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findByPermission($permission, $tool = null)
    {
        $from = sprintf('user u, userGroup ur, userGroup_userPermission as t1 LEFT JOIN userPermission as t2 ON t1.userPermissionId = t2.id');
        $where = sprintf('u.`goupId` = ur.`id` AND ur.`id` = t1.`groupId` AND t 2.`name` = %s AND u.`active` ', $this->getDb()->quote($permission));
        $list = $this->selectFrom($from, $where, $tool, 'u', true);
        return $list;
    }

    /**
     * Get all active users of a role
     *
     * @param int $groupId
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findByGroupId($groupId, $tool = null)
    {
        $where = sprintf('`groupId` = %s AND `active` = 1 ', (int)$groupId);
        return $this->selectMany($where, $tool);
    }

    /**
     * Do user house cleaning:
     *  o Clear the session id's from any inactive accounts
     *  o Deactivate any user accounts that have not been logged into in 12 months
     *  o Deactivate any accounts
     *
     */
    public function clean()
    {
        if ($this->getConfig()->get('session.driver') == '\Tk\Session\Adapter\Database') {
            $sql = sprintf('UPDATE `%s` u LEFT JOIN `%s` s ON (u.`sessionId` = s.`id`) SET u.`sessionId` = \'\' WHERE s.`id` IS NULL ',
                $this->getTable(), $this->getConfig()->get('session.database.table'));
            $this->getDb()->query($sql);
        } else {
            // TODO: Untested...
//            $sql = sprintf('UPDATE `%s` SET sessionId = \'\' WHERE lastLogin < %s ',
//                $this->getTable(), enquote(\Tk\Date::create(time() - \Tk\Config::getInstance()->get('session.expiration'))->toString()));
//            $this->getDb()->query($sql);
        }
    }


    /**
     * Find a li
     *
     * @return \Tk\Db\ArrayObject
     */
    public function findTotals($dateFrom = null, $dateTo = null)
    {
        if (!$dateFrom)
            $dateFrom = \Tk\Date::create()->addMonths(-6)->floor();
        if (!$dateTo)
            $dateTo = \Tk\Date::create()->addMonths(6)->ceil();
        
        // create table `calDay`
        $cal = 'calDay';
        $this->getDb()->createDateTable($dateFrom->floor(), $dateTo->floor(), $cal);
        $tbl = $this->getTable();
        
        $sql = <<<SQL
SELECT DATE($cal.`date`) as 'date', IFNULL(count($tbl.`id`), 0) as 'total'
FROM `$tbl` RIGHT JOIN `$cal` ON (DATE($tbl.`created`) = DATE($cal.`date`) )
GROUP BY `date`
SQL;
        $res = $this->getDb()->query($sql);

        $arr = array();
        foreach ($res as $row) {
            $arr[$row->date] = $row->total;
        }
        return $arr;
    }

    /**
     * Find filtered records
     *
     * @param array $filter
     * @param \Tk\Db\Tool $tool
     * @return \Tk\Db\ArrayObject
     */
    public function findFiltered($filter = array(), $tool = null)
    {
        $where = '';
        if (!empty($filter['keywords'])) {
            $kw = '%' . $this->getDb()->escapeString($filter['keywords']) . '%';
            $w = '';
            $w .= sprintf('`username` LIKE %s OR ', $this->getDb()->quote($kw));
            $w .= sprintf('`email` LIKE %s OR ', $this->getDb()->quote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = intval($filter['keywords']);
                $w .= sprintf('`id` = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['groupId'])) {
            $where .= sprintf('`groupId` = %s AND ', (int)$filter['groupId']);
        }
        if (!empty($filter['lastLogin'])) {
            $date = $filter['lastLogin'];
            $where .= sprintf('`lastLogin` >= %s AND ', $this->getDb()->quote($date->toString()));
        }
        if ($where) {
            $where = substr($where, 0, -4);
        }
        return $this->selectMany($where, $tool);
    }


    /**
     * getMonthlySignups
     *
     * @param \Tk\Date $dateFrom
     * @param \Tk\Date $dateTo
     * @return array
     */
    public function getMonthlySignups($dateFrom, $dateTo)
    {
        $dateFrom = $dateFrom->getMonthStart()->floor();
        $dateTo = $dateTo->getMonthEnd()->ceil();

        $sql = sprintf("
SELECT DISTINCT
   DATE_FORMAT(firstActive,'%%Y-%%m') AS date,
   count(id) as total
 FROM user
 WHERE
   firstActive >= '%s' AND firstActive <= '%s'
 GROUP BY DATE_FORMAT(firstActive,'%%Y-%%m')
 ORDER BY firstActive ASC", $dateFrom->toString(), $dateTo->toString());

        $db = $this->getDb();
        $res = $db->query($sql);
        $arr = array();
        $idxDate = clone $dateFrom;
        $row = $res->fetch();
        while($idxDate->lessThan($dateTo)) {
            if ($row && $row->date == $idxDate->toString('Y-m')) {
                $arr[$idxDate->toString('Y-m')] = $row->total;
                $row = $res->fetch();
            } else {
                $arr[$idxDate->toString('Y-m')] = 0;
            }
            $idxDate = $idxDate->addMonths(1);
        }
        return $arr;
    }


    /**
     * getData
     *
     * @param int $userId
     * @param string $key
     * @return string
     */
    public function getData($userId, $key)
    {
        $key = $this->getDb()->escapeString($key);
        $sql = sprintf('SELECT * FROM %s WHERE `userId` = %s and `key` = %s ', $this->getTable().'Data', (int)$userId, $this->getDb()->quote($key));
        $res = $this->getDb()->query($sql);
        $row = $res->fetch(\PDO::FETCH_OBJ);

        if ($row) {
            return stripslashes($row->value);
        }
    }

    /**
     * setData
     *
     * @param $userId
     * @param $key
     * @param $value
     * @return string
     */
    public function setData($userId, $key, $value)
    {
        $key = $this->getDb()->escapeString($key);
        $value = $this->getDb()->escapeString($value);
        $sql = sprintf('SELECT * FROM %s WHERE `userId` = %s and `key` = %s ', $this->getTable().'Data', (int)$userId, $this->getDb()->quote($key));
        $res = $this->getDb()->query($sql);
        $obj = $res->fetch();

        if ($obj) {
            $sql = sprintf('UPDATE %s SET value = %s WHERE userId = %s and `key` = %s ', $this->getTable().'Data', $this->getDb()->quote($value), (int)$userId, $this->getDb()->quote($key) );
            $this->getDb()->query($sql);
        } else {
            $sql = sprintf('INSERT INTO %s (`userId`, `key`, `value`) VALUES (%s, %s, %s)', $this->getTable().'Data', (int)$userId, $this->getDb()->quote($key), $this->getDb()->quote($value) );
            $this->getDb()->query($sql);
        }
        return $value;
    }

    /**
     * deleteData
     *
     * @param $userId
     * @param null $key
     */
    public function deleteData($userId, $key = null)
    {
        $key = $this->getDb()->escapeString($key);
        $sql = sprintf('DELETE FROM %s WHERE `userId`=%s AND `key`=%s LIMIT 1', $this->getTable().'Data', (int)$userId, $this->getDb()->quote($key) );
        $this->getDb()->query($sql);
    }

    /**
     * getAllData
     *
     * @param $userId
     * @return \Tk\ArrayObject
     */
    public function getAllData($userId)
    {
        $sql = sprintf('SELECT * FROM %s WHERE `userId` = %s ', $this->getTable().'Data', (int)$userId );
        $res = $this->getDb()->query($sql);
        $res->setFetchMode(\PDO::FETCH_OBJ);
        $arr = new \Tk\ArrayObject();
        foreach ($res as $row) {
            $arr[$row->key] = $row->value;
        }
        return $arr;
    }

    /**
     * deleteAllData
     *
     * @param $userId
     */
    public function deleteAllData($userId)
    {
        $sql = sprintf('DELETE FROM %s WHERE `userId`=%s', $this->getTable().'Data', (int)$userId );
        $this->getDb()->query($sql);
    }



}