<?php
// src/AppBundle/Service/Security/AccountGroupBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class AccountGroupBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const ACCOUNT_GROUP_READ   = 'account_group_read';
    const ACCOUNT_GROUP_CREATE = 'account_group_create';

    const ACCOUNT_GROUP_BIND   = 'account_group_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::ACCOUNT_GROUP_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::ACCOUNT_GROUP_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            case self::ACCOUNT_GROUP_BIND:
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            default:
                return FALSE;
            break;
        }
    }
}
