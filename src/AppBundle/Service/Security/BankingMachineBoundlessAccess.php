<?php
// src/AppBundle/Service/Security/BankingMachineBoundlessAccess.php
namespace AppBundle\Service\Security;

use AppBundle\Service\Security\Utility\Abstracted\AbstractBoundlessAccess,
    AppBundle\Service\Security\Utility\Interfaces\UserRoleListInterface;

class BankingMachineBoundlessAccess extends AbstractBoundlessAccess implements UserRoleListInterface
{
    const BANKING_MACHINE_READ   = 'banking_machine_read';
    const BANKING_MACHINE_CREATE = 'banking_machine_create';

    const BANKING_MACHINE_BIND   = 'banking_machine_bind';

    public function isGranted($attribute)
    {
        switch($attribute)
        {
            case self::BANKING_MACHINE_READ:
                if( $this->_authorizationChecker->isGranted(self::ROLE_MANAGER) )
                    return self::ROLE_MANAGER;

                return FALSE;
            break;

            case self::BANKING_MACHINE_CREATE:
                if( $this->_authorizationChecker->isGranted(self::ROLE_ADMIN) )
                    return self::ROLE_ADMIN;

                return FALSE;
            break;

            case self::BANKING_MACHINE_BIND:
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
