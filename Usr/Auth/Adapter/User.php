<?php
/*
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 */
namespace Usr\Auth\Adapter;

/**
 * The standard mod-user Auth adapter
 *
 */
class User extends \Tk\Auth\Adapter\Iface
{

    /**
     * authenticate() - defined by Tk\Auth\Adapter\Iface.  This method is called to
     * attempt an authentication.  Previous to this call, this adapter would have already
     * been configured with all necessary information to successfully connect to a database
     * table and attempt to find a record matching the provided identity.
     *
     * @return \Tk\Auth\Result
     */
    public function authenticate()
    {
        if (!preg_match (\Tk\Validator::REG_USERNAME, $this->getUsername()) || !preg_match (\Tk\Validator::REG_PASSWORD, $this->getPassword())) {
            return $this->makeResult(\Tk\Auth\Result::FAILURE_CREDENTIAL_INVALID, 'Invalid username or password.');
        }

        $user = \Usr\Db\User::getMapper()->findForAuth($this->getUsername());
        if (!$user || !$this->getPassword()) {
            return $this->makeResult(\Tk\Auth\Result::FAILURE_IDENTITY_NOT_FOUND, 'Invalid username or password.');
        }
        $passHash = $this->getConfig()->getAuth()->hash($this->getPassword());
        if ($passHash == $user->password) {
            return $this->makeResult(\Tk\Auth\Result::SUCCESS);
        }
        return $this->makeResult(\Tk\Auth\Result::FAILURE_IDENTITY_NOT_FOUND, 'Invalid username or password.');


    }

}
