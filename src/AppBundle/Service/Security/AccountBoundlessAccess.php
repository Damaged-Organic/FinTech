<?php
// src/AppBundle/Service/Security/AccountBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class AccountBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const ACCOUNT_READ   = 'account_read';
    const ACCOUNT_CREATE = 'account_create';

    const ACCOUNT_BIND = 'account_bind';

    const ACCOUNT_UPDATE_ACCOUNT_GROUP = 'account_update_account_group';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::ACCOUNT_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::ACCOUNT_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::ACCOUNT_BIND:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::ACCOUNT_UPDATE_ACCOUNT_GROUP:
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
