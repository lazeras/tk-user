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
 * @package Usr\Db
 */
class Data extends \Tk\Db\Model
{

    public $id = '';
    public $userId = 0;
    public $key = '';
    public $value = '';






}


/**
 *
 *
 * @package Usr
 */
class DataValidator extends \Tk\Validator
{

    public function validate()
    {
        if (!preg_match('/^[a-z0-9_-]{1,32}$/i', $this->obj->key)) {
            $this->addError('key', 'Invalid characters used in key');
        }
        
    }

}