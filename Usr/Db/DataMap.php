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
class DataMap extends \Tk\Db\Mapper
{

    /**
     * Create the data map
     *
     * @return \Tk\Model\Model\DataMap
     */
    protected function makeDataMap()
    {
        $dataMap = new \Tk\Model\DataMap(__CLASS__);
        $this->setTable('userData');

        $dataMap->addIdProperty(\Tk\Model\Map\Integer::create('id'));
        $dataMap->addProperty(\Tk\Model\Map\Integer::create('userId'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('key'));
        $dataMap->addProperty(\Tk\Model\Map\String::create('value'));

        return $dataMap;
    }



    /**
     * Find a user object by username and password
     *
     * @param string $userId
     * @return \Tk\Db\ArrayObject
     */
    public function findByUserId($userId)
    {
        $where = sprintf('`userId` = %s ', (int)$userId);
        return $this->selectMany($where)->current();
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
            $w .= sprintf('`key` LIKE %s OR ', enquote($kw));
            $w .= sprintf('`value` LIKE %s OR ', enquote($kw));
            if (is_numeric($filter['keywords'])) {
                $id = (int)$filter['keywords'];
                $w .= sprintf('`id` = %d OR ', $id);
            }
            if ($w) {
                $where .= '(' . substr($w, 0, -3) . ') AND ';
            }
        }

        if (!empty($filter['userId'])) {
            $where .= sprintf('`userId` = %s AND ', (int)$filter['userId']);
        }
        if ($where) {
            $where = substr($where, 0, -4);
        }
        return $this->selectMany($where, $tool);
    }



}