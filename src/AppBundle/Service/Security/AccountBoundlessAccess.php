<?php
// src/AppBundle/Service/Security/AccountBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class AccountBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const ACCOUNT_READ   = 'account_read';
    const ACCOUNT_CREATE = 'account_create';

    const ACCOUNT_BIND   = 'account_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::ACCOUNT_READ:
                return $this->_authorizationChecker->isGranted(self::ROLE_EMPLOYEE);
            break;

            case self::ACCOUNT_CREATE:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            case self::ACCOUNT_BIND:
                return $this->_authorizationChecker->isGranted(self::ROLE_ADMIN);
            break;

            default:
                return FALSE;
            break;
        }
    }
}
